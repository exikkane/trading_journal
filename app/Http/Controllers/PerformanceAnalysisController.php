<?php

namespace App\Http\Controllers;

use App\Models\PerformanceReview;
use App\Models\AccountTrade;
use App\Models\Trade;
use App\Models\TradingPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class PerformanceAnalysisController extends Controller
{
    public function index(Request $request)
    {
        $scope = $request->query('scope', 'mpa');
        if (! in_array($scope, ['mpa', 'qpa'], true)) {
            $scope = 'mpa';
        }

        $mpaFilter = $request->query('mpa', 'this_quarter');
        if (! in_array($mpaFilter, ['this_quarter', 'by_quarter', 'by_years'], true)) {
            $mpaFilter = 'this_quarter';
        }

        $now = Carbon::now();
        $currentYear = (int) $now->year;
        $currentQuarter = (int) $now->quarter;

        $trades = Trade::query()
            ->with(['accountTrades.account'])
            ->orderBy('start_date')
            ->get();

        $yearsWithTrades = $trades
            ->map(fn (Trade $trade) => (int) $trade->start_date->format('Y'))
            ->unique()
            ->sort()
            ->values();

        $minYear = $yearsWithTrades->first() ?? $currentYear;
        $maxYear = $yearsWithTrades->last() ?? $currentYear;
        $yearOptions = collect(range($minYear, $maxYear + 1))->values();

        $selectedYear = (int) $request->query('year', $currentYear);
        if (! $yearOptions->contains($selectedYear)) {
            $yearOptions = $yearOptions->push($selectedYear)->sort()->values();
        }

        $viewData = [
            'scope' => $scope,
            'mpaFilter' => $mpaFilter,
            'yearOptions' => $yearOptions,
            'selectedYear' => $selectedYear,
            'currentYear' => $currentYear,
            'currentQuarter' => $currentQuarter,
        ];

        if ($scope === 'mpa') {
            if ($mpaFilter === 'this_quarter') {
                $quarter = $currentQuarter;
                $year = $currentYear;
                $quarterTrades = $this->tradesForQuarter($trades, $year, $quarter);
                $viewData['quarterGroups'] = [
                    [
                        'label' => 'Q' . $quarter,
                        'year' => $year,
                        'profit_pct' => $this->computeStats($quarterTrades)['profit_pct'],
                        'months' => $this->buildMonthCards($trades, $year, $quarter),
                    ],
                ];
            } elseif ($mpaFilter === 'by_quarter') {
                $year = $selectedYear;
                $groups = [];
                foreach ([1, 2, 3, 4] as $quarter) {
                    $quarterTrades = $this->tradesForQuarter($trades, $year, $quarter);
                    $groups[] = [
                        'label' => 'Q' . $quarter,
                        'year' => $year,
                        'profit_pct' => $this->computeStats($quarterTrades)['profit_pct'],
                        'months' => $this->buildMonthCards($trades, $year, $quarter),
                    ];
                }
                $viewData['quarterGroups'] = $groups;
            } else {
                $yearGroups = [];
                foreach ($yearOptions as $year) {
                    $yearTrades = $trades->filter(fn (Trade $trade) => (int) $trade->start_date->format('Y') === (int) $year);
                    $yearGroups[] = [
                        'year' => $year,
                        'profit_pct' => $this->computeStats($yearTrades)['profit_pct'],
                        'months' => $this->buildMonthCards($trades, (int) $year, null),
                    ];
                }
                $viewData['yearGroups'] = $yearGroups;
            }
        } else {
            $year = $selectedYear;
            $quarterCards = [];
            foreach ([1, 2, 3, 4] as $quarter) {
                $quarterTrades = $this->tradesForQuarter($trades, $year, $quarter);
                $stats = $this->computeStats($quarterTrades);
                $quarterCards[] = [
                    'label' => 'Q' . $quarter,
                    'year' => $year,
                    'quarter' => $quarter,
                    'stats' => $stats,
                ];
            }
            $viewData['quarterCards'] = $quarterCards;
        }

        return view('performance.index', $viewData);
    }

    private function buildMonthCards(Collection $trades, int $year, ?int $quarter): array
    {
        $months = $quarter
            ? range(($quarter - 1) * 3 + 1, $quarter * 3)
            : range(1, 12);

        $cards = [];
        foreach ($months as $month) {
            $monthTrades = $trades->filter(function (Trade $trade) use ($year, $month) {
                return (int) $trade->start_date->format('Y') === $year
                    && (int) $trade->start_date->format('n') === $month;
            });

            $stats = $this->computeStats($monthTrades);
            $cards[] = [
                'label' => Carbon::create($year, $month, 1)->format('F Y'),
                'year' => $year,
                'month' => $month,
                'quarter' => 'Q' . (int) ceil($month / 3),
                'stats' => $stats,
            ];
        }

        return $cards;
    }

    private function tradesForQuarter(Collection $trades, int $year, int $quarter): Collection
    {
        return $trades->filter(function (Trade $trade) use ($year, $quarter) {
            return (int) $trade->start_date->format('Y') === $year
                && (int) $trade->start_date->quarter === $quarter;
        });
    }

    private function computeStats(Collection $trades): array
    {
        $totalTrades = $trades->count();
        $wins = $trades->where('result', 'win')->count();
        $losses = $trades->where('result', 'loss')->count();
        $be = $trades->where('result', 'be')->count();

        $profitPct = 0.0;
        $profitAmount = 0.0;
        $rrTotal = 0.0;
        $rrWins = [];

        foreach ($trades as $trade) {
            foreach ($trade->accountTrades as $accountTrade) {
                $deltaPct = $this->resultProfitPct($trade, $accountTrade);
                $profitPct += $deltaPct;
                if ($accountTrade->account) {
                    $profitAmount += ($deltaPct / 100) * (float) $accountTrade->account->initial_balance;
                }
                if ($trade->result === 'win') {
                    $rrTotal += (float) $accountTrade->risk_reward;
                    $rrWins[] = (float) $accountTrade->risk_reward;
                }
            }
        }

        $winsLosses = $wins + $losses;
        $winRate = $winsLosses > 0 ? ($wins / $winsLosses) * 100 : 0.0;
        $avgRr = count($rrWins) > 0 ? array_sum($rrWins) / count($rrWins) : 0.0;

        return [
            'total' => $totalTrades,
            'wins' => $wins,
            'losses' => $losses,
            'be' => $be,
            'profit_pct' => $profitPct,
            'profit_amount' => $profitAmount,
            'rr_total' => $rrTotal,
            'avg_rr' => $avgRr,
            'win_rate' => $winRate,
        ];
    }

    public function show(Request $request, string $type, int $year, int $period)
    {
        if (! in_array($type, ['month', 'quarter'], true)) {
            abort(404);
        }

        $bounds = $this->periodBounds($type, $year, $period);
        if (! $bounds) {
            abort(404);
        }

        [$start, $end] = $bounds;

        $trades = Trade::query()
            ->with(['accountTrades.account'])
            ->whereBetween('start_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('start_date')
            ->get();

        $stats = $this->computeStats($trades);
        $accountsStats = $this->computeAccountStats($trades);

        $resultFilter = $request->query('result', 'all');
        if (! in_array($resultFilter, ['all', 'win', 'loss'], true)) {
            $resultFilter = 'all';
        }

        $filteredTrades = $resultFilter === 'all'
            ? $trades
            : $trades->where('result', $resultFilter);

        $tradesList = $filteredTrades->map(function (Trade $trade) {
            $profitPct = 0.0;
            $rrValues = [];
            foreach ($trade->accountTrades as $accountTrade) {
                $profitPct += $this->resultProfitPct($trade, $accountTrade);
                $rrValues[] = (float) $accountTrade->risk_reward;
            }
            $avgRr = count($rrValues) > 0 ? array_sum($rrValues) / count($rrValues) : 0.0;

            return [
                'id' => $trade->id,
                'pair' => $trade->pair,
                'date' => $trade->start_date->format('Y-m-d'),
                'result' => $trade->result,
                'avg_rr' => $avgRr,
                'profit_pct' => $profitPct,
            ];
        });

        $plans = TradingPlan::query()
            ->whereBetween('plan_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('plan_date')
            ->get();

        $reviewQuarter = $type === 'quarter' ? $period : $this->monthToQuarter($period);
        $reviewMonth = $type === 'month' ? $period : 0;
        $review = PerformanceReview::query()
            ->where('period_type', $type)
            ->where('year', $year)
            ->where('quarter', $reviewQuarter)
            ->where('month', $reviewMonth)
            ->first();

        $monthCards = [];
        if ($type === 'quarter') {
            $allTrades = Trade::query()
                ->with(['accountTrades.account'])
                ->orderBy('start_date')
                ->get();
            $monthCards = $this->buildMonthCards($allTrades, $year, $period);
        }

        return view('performance.show', [
            'type' => $type,
            'year' => $year,
            'period' => $period,
            'start' => $start,
            'end' => $end,
            'stats' => $stats,
            'accountsStats' => $accountsStats,
            'plans' => $plans,
            'tradesList' => $tradesList,
            'resultFilter' => $resultFilter,
            'review' => $review,
            'monthCards' => $monthCards,
        ]);
    }

    public function updateReview(Request $request, string $type, int $year, int $period)
    {
        if (! in_array($type, ['month', 'quarter'], true)) {
            abort(404);
        }

        $bounds = $this->periodBounds($type, $year, $period);
        if (! $bounds) {
            abort(404);
        }
        [$start, $end] = $bounds;

        $data = $request->validate([
            'mpa_metric' => ['nullable', 'string'],
            'trades_conclusions' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'notes_screenshots.*' => ['nullable', 'image', 'max:5120'],
            'summary_general' => ['nullable', 'string'],
            'summary_what_works' => ['nullable', 'string'],
            'summary_what_not' => ['nullable', 'string'],
            'summary_key_lessons' => ['nullable', 'string'],
            'summary_next_steps' => ['nullable', 'string'],
        ]);

        $reviewQuarter = $type === 'quarter' ? $period : $this->monthToQuarter($period);
        $reviewMonth = $type === 'month' ? $period : 0;
        $review = PerformanceReview::query()->firstOrNew([
            'period_type' => $type,
            'year' => $year,
            'quarter' => $reviewQuarter,
            'month' => $reviewMonth,
        ]);

        $review->start_date = $start->toDateString();
        $review->end_date = $end->toDateString();

        $review->fill(Arr::except($data, ['notes_screenshots']));

        if ($request->hasFile('notes_screenshots')) {
            $existing = $review->notes_screenshots ?? [];
            foreach ($request->file('notes_screenshots') as $file) {
                $existing[] = $file->store('performance-notes', 'public');
            }
            $review->notes_screenshots = $existing;
        }

        $review->save();

        return redirect()->route('performance.detail', [
            'type' => $type,
            'year' => $year,
            'period' => $period,
            'result' => $request->query('result', 'all'),
        ]);
    }

    private function computeAccountStats(Collection $trades): array
    {
        $stats = [];
        foreach ($trades as $trade) {
            foreach ($trade->accountTrades as $accountTrade) {
                if (! $accountTrade->account) {
                    continue;
                }
                $accountId = $accountTrade->account->id;
                if (! isset($stats[$accountId])) {
                    $stats[$accountId] = [
                        'account' => $accountTrade->account,
                        'profit_pct' => 0.0,
                        'profit_amount' => 0.0,
                        'rr_total' => 0.0,
                        'rr_values' => [],
                    ];
                }

                $deltaPct = $this->resultProfitPct($trade, $accountTrade);
                $stats[$accountId]['profit_pct'] += $deltaPct;
                $stats[$accountId]['profit_amount'] += ($deltaPct / 100) * (float) $accountTrade->account->initial_balance;
                if ($trade->result === 'win') {
                    $stats[$accountId]['rr_total'] += (float) $accountTrade->risk_reward;
                    $stats[$accountId]['rr_values'][] = (float) $accountTrade->risk_reward;
                }
            }
        }

        return collect($stats)
            ->map(function (array $item) {
                $avg = count($item['rr_values']) > 0 ? array_sum($item['rr_values']) / count($item['rr_values']) : 0.0;
                return [
                    'account' => $item['account'],
                    'profit_pct' => $item['profit_pct'],
                    'profit_amount' => $item['profit_amount'],
                    'rr_total' => $item['rr_total'],
                    'avg_rr' => $avg,
                ];
            })
            ->values()
            ->all();
    }

    private function periodBounds(string $type, int $year, int $period): ?array
    {
        if ($type === 'month') {
            if ($period < 1 || $period > 12) {
                return null;
            }
            $start = Carbon::create($year, $period, 1)->startOfDay();
            $end = $start->copy()->endOfMonth();
            return [$start, $end];
        }

        if ($period < 1 || $period > 4) {
            return null;
        }
        $month = ($period - 1) * 3 + 1;
        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end = $start->copy()->endOfQuarter();
        return [$start, $end];
    }

    private function monthToQuarter(int $month): int
    {
        return (int) ceil($month / 3);
    }

    private function resultProfitPct(Trade $trade, AccountTrade $accountTrade): float
    {
        $riskPct = (float) $accountTrade->risk_pct;
        $riskReward = (float) $accountTrade->risk_reward;

        if ($trade->result === 'win') {
            return $riskReward * $riskPct;
        }
        if ($trade->result === 'loss') {
            return -$riskPct;
        }
        if ($trade->result === 'be') {
            return $riskReward * $riskPct;
        }

        return 0.0;
    }
}
