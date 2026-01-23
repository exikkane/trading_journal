<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\AccountTrade;
use App\Models\Trade;
use Carbon\Carbon;
use Illuminate\Console\Command;
use SplFileObject;

class ImportTrades extends Command
{
    protected $signature = 'journal:import {path=Journal.csv} {--dry-run}';

    protected $description = 'Import trades from a Notion CSV export.';

    public function handle(): int
    {
        $pathArg = $this->argument('path');
        $path = $pathArg;
        if (! str_starts_with($pathArg, '/')) {
            $path = base_path($pathArg);
        }

        if (! file_exists($path)) {
            $this->error("File not found: {$path}");
            return Command::FAILURE;
        }

        $file = new SplFileObject($path);
        $file->setFlags(SplFileObject::READ_CSV | SplFileObject::DROP_NEW_LINE);

        $header = $file->fgetcsv();
        if (! is_array($header)) {
            $this->error('Could not read CSV header.');
            return Command::FAILURE;
        }
        if (isset($header[0])) {
            $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
        }

        $indices = [
            'date' => array_search('Start - End Date', $header, true),
            'account' => array_search('Account', $header, true),
            'direction' => array_search('Direction', $header, true),
            'pair' => array_search('Pair', $header, true),
            'result' => array_search('Result', $header, true),
            'rr' => array_search('RR', $header, true),
            'risk' => array_search('Risk', $header, true),
        ];

        foreach ($indices as $key => $index) {
            if ($index === false) {
                $this->error("Missing column in CSV: {$key}");
                return Command::FAILURE;
            }
        }

        $imported = 0;
        $skipped = 0;

        foreach ($file as $row) {
            if (! is_array($row)) {
                continue;
            }

            $hasData = false;
            foreach ($row as $cell) {
                if (is_string($cell) && trim($cell) !== '') {
                    $hasData = true;
                    break;
                }
            }
            if (! $hasData) {
                continue;
            }

            $data = $this->mapRow($row, $indices);
            if ($data === null) {
                $skipped++;
                continue;
            }

            if (! $this->option('dry-run')) {
                $trade = Trade::create($data['trade']);
                $account = Account::firstOrCreate(
                    ['name' => $data['account_name']],
                    ['initial_balance' => 0, 'current_balance' => null]
                );
                AccountTrade::create([
                    'trade_id' => $trade->id,
                    'account_id' => $account->id,
                    'risk_reward' => $data['risk_reward'],
                    'risk_pct' => $data['risk_pct'],
                ]);
            }
            $imported++;
        }

        $this->info("Imported: {$imported}");
        $this->info("Skipped: {$skipped}");

        if ($this->option('dry-run')) {
            $this->info('Dry run only. No data inserted.');
        }

        return Command::SUCCESS;
    }

    private function mapRow(array $row, array $indices): ?array
    {
        $rawDate = trim((string) ($row[$indices['date']] ?? ''));
        if ($rawDate === '') {
            return null;
        }

        [$startDate, $endDate] = $this->parseDateRange($rawDate);
        if (! $startDate) {
            return null;
        }

        $directionRaw = $this->extractLabel((string) ($row[$indices['direction']] ?? ''));
        $direction = $this->normalizeDirection($directionRaw);
        if (! $direction) {
            return null;
        }

        $resultRaw = $this->extractLabel((string) ($row[$indices['result']] ?? ''));
        $result = $this->normalizeResult($resultRaw);
        if (! $result) {
            return null;
        }

        $riskReward = $this->parseNumber((string) ($row[$indices['rr']] ?? ''));
        $riskPct = $this->parseNumber((string) ($row[$indices['risk']] ?? ''));
        if ($riskReward === null || $riskPct === null) {
            return null;
        }

        $account = trim((string) ($row[$indices['account']] ?? ''));
        $pair = $this->extractLabel((string) ($row[$indices['pair']] ?? ''));

        if ($account === '') {
            $account = 'Unknown';
        }
        if ($pair === '') {
            $pair = 'Unknown';
        }

        return [
            'trade' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate?->toDateString(),
                'direction' => $direction,
                'pair' => $pair,
                'result' => $result,
            ],
            'account_name' => $account,
            'risk_reward' => abs($riskReward),
            'risk_pct' => abs($riskPct),
        ];
    }

    private function extractLabel(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        $pos = strpos($value, ' (');
        if ($pos !== false) {
            return trim(substr($value, 0, $pos));
        }

        return $value;
    }

    private function normalizeDirection(string $value): ?string
    {
        $value = strtolower(trim($value));
        if ($value === '') {
            return null;
        }

        if (str_contains($value, 'long')) {
            return 'long';
        }
        if (str_contains($value, 'short')) {
            return 'short';
        }

        return null;
    }

    private function normalizeResult(string $value): ?string
    {
        $value = strtolower(trim($value));
        if ($value === '') {
            return null;
        }

        if (in_array($value, ['win', 'won'], true)) {
            return 'win';
        }
        if (in_array($value, ['loss', 'lose', 'lost'], true)) {
            return 'loss';
        }
        if (in_array($value, ['be', 'break even', 'breakeven'], true)) {
            return 'be';
        }
        if (in_array($value, ['in progress', 'open'], true)) {
            return 'in_progress';
        }

        return null;
    }

    private function parseNumber(string $value): ?float
    {
        $clean = preg_replace('/[^0-9.+-]/', '', $value);
        if ($clean === '' || $clean === null) {
            return null;
        }

        return (float) $clean;
    }

    private function parseDateRange(string $value): array
    {
        $start = null;
        $end = null;

        if (str_contains($value, '→')) {
            $parts = array_map('trim', explode('→', $value));
        } elseif (str_contains($value, '->')) {
            $parts = array_map('trim', explode('->', $value));
        } elseif (str_contains($value, ' to ')) {
            $parts = array_map('trim', explode(' to ', $value));
        } else {
            $parts = [trim($value)];
        }

        $start = $this->parseDate($parts[0] ?? '');
        $end = isset($parts[1]) ? $this->parseDate($parts[1]) : null;

        return [$start, $end];
    }

    private function parseDate(string $value): ?Carbon
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        $formats = [
            'd/m/Y',
            'd/m/Y H:i',
            'd/m/Y H:i:s',
            'm/d/Y',
            'm/d/Y H:i',
            'Y-m-d',
        ];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $value);
            } catch (\Exception $e) {
                // try next format
            }
        }

        return null;
    }
}
