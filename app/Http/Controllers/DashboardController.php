<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountTrade;
use App\Models\Trade;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'month');
        if (! in_array($filter, ['month', 'quarter', 'all'], true)) {
            $filter = 'month';
        }

        $accountId = $request->query('account');
        if ($accountId !== null && $accountId !== 'all') {
            $accountId = (int) $accountId;
        } else {
            $accountId = null;
        }

        $dateRange = null;
        if ($filter !== 'all') {
            $now = Carbon::now();
            $start = $filter === 'month' ? $now->copy()->startOfMonth() : $now->copy()->startOfQuarter();
            $end = $filter === 'month' ? $now->copy()->endOfMonth() : $now->copy()->endOfQuarter();
            $dateRange = [$start, $end];
        }
        $currentData = $this->buildPeriodData($dateRange, $accountId);

        $previousStats = null;
        $previousLabel = null;
        if ($filter === 'month') {
            $prevStart = Carbon::now()->subMonthNoOverflow()->startOfMonth();
            $prevEnd = $prevStart->copy()->endOfMonth();
            $previousStats = $this->buildPeriodData([$prevStart, $prevEnd], $accountId)['stats'];
            $previousLabel = $prevStart->format('F Y');
        } elseif ($filter === 'quarter') {
            $prevStart = Carbon::now()->subQuarter()->startOfQuarter();
            $prevEnd = $prevStart->copy()->endOfQuarter();
            $previousStats = $this->buildPeriodData([$prevStart, $prevEnd], $accountId)['stats'];
            $previousLabel = 'Q' . $prevStart->quarter . ' ' . $prevStart->year;
        }

        $accounts = Account::query()->orderBy('name')->get();

        return view('dashboard', [
            'filter' => $filter,
            'accountId' => $accountId,
            'accounts' => $accounts,
            'totalTrades' => $currentData['stats']['totalTrades'],
            'wins' => $currentData['stats']['wins'],
            'losses' => $currentData['stats']['losses'],
            'be' => $currentData['stats']['be'],
            'inProgress' => $currentData['stats']['inProgress'],
            'winningRatio' => $currentData['stats']['winningRatio'],
            'averageRr' => $currentData['stats']['averageRr'],
            'maxDrawdown' => $currentData['stats']['maxDrawdown'],
            'netProfit' => $currentData['stats']['netProfit'],
            'equity' => $currentData['stats']['equity'],
            'previousStats' => $previousStats,
            'previousLabel' => $previousLabel,
        ]);
    }

    private function buildPeriodData(?array $dateRange, ?int $accountId): array
    {
        $tradeQuery = Trade::query()->orderBy('start_date');
        if ($dateRange) {
            $tradeQuery->whereBetween('start_date', [$dateRange[0]->toDateString(), $dateRange[1]->toDateString()]);
        }

        $accountTradeQuery = AccountTrade::query()->with('trade');
        if ($dateRange) {
            $accountTradeQuery->whereHas('trade', function ($tradeQuery) use ($dateRange) {
                $tradeQuery->whereBetween('start_date', [$dateRange[0]->toDateString(), $dateRange[1]->toDateString()]);
            });
        }
        if ($accountId) {
            $accountTradeQuery->where('account_id', $accountId);
        }

        $accountTrades = $accountTradeQuery->get();
        $trades = $accountId
            ? $accountTrades->pluck('trade')->filter()->unique('id')->values()
            : $tradeQuery->get();

        return [
            'trades' => $trades,
            'accountTrades' => $accountTrades,
            'stats' => $this->computeStats($trades, $accountTrades, (bool) $accountId),
        ];
    }

    private function computeStats($trades, $accountTrades, bool $accountMode): array
    {
        if ($accountMode) {
            $totalTrades = $accountTrades->count();
            $wins = $accountTrades->filter(fn ($at) => $at->trade && $at->trade->result === 'win')->count();
            $losses = $accountTrades->filter(fn ($at) => $at->trade && $at->trade->result === 'loss')->count();
            $be = $accountTrades->filter(fn ($at) => $at->trade && $at->trade->result === 'be')->count();
            $inProgress = $accountTrades->filter(fn ($at) => $at->trade && $at->trade->result === 'in_progress')->count();
        } else {
            $totalTrades = $trades->count();
            $wins = $trades->where('result', 'win')->count();
            $losses = $trades->where('result', 'loss')->count();
            $be = $trades->where('result', 'be')->count();
            $inProgress = $trades->where('result', 'in_progress')->count();
        }

        $winsLosses = $wins + $losses;
        $winningRatio = $winsLosses > 0 ? ($wins / $winsLosses) * 100 : 0;

        $winAccountTrades = $accountTrades->filter(function ($accountTrade) {
            return $accountTrade->trade && $accountTrade->trade->result === 'win';
        });
        $averageRr = $winAccountTrades->count() > 0
            ? (float) $winAccountTrades->avg('risk_reward')
            : 0;

        $netProfit = 0.0;
        $equity = [0.0];
        $running = 0.0;
        $currentLossStreak = 0.0;
        $maxDrawdown = 0.0;

        $sortedTrades = $accountTrades->sortBy(function ($accountTrade) {
            return $accountTrade->trade ? $accountTrade->trade->start_date : null;
        })->values();

        foreach ($sortedTrades as $accountTrade) {
            $trade = $accountTrade->trade;
            if (! $trade) {
                continue;
            }

            $riskPct = (float) $accountTrade->risk_pct;
            $riskReward = (float) $accountTrade->risk_reward;

            $delta = 0.0;
            if ($trade->result === 'win') {
                $delta = $riskReward * $riskPct;
                $currentLossStreak = 0.0;
            } elseif ($trade->result === 'loss') {
                $delta = -$riskPct;
                $currentLossStreak += $riskPct;
                if ($currentLossStreak > $maxDrawdown) {
                    $maxDrawdown = $currentLossStreak;
                }
            } elseif ($trade->result === 'be') {
                $delta = $riskReward * $riskPct;
                if ($delta < 0) {
                    $currentLossStreak += abs($delta);
                    if ($currentLossStreak > $maxDrawdown) {
                        $maxDrawdown = $currentLossStreak;
                    }
                } else {
                    $currentLossStreak = 0.0;
                }
            }

            $running += $delta;
            $netProfit = $running;
            $equity[] = $running;
        }

        return [
            'totalTrades' => $totalTrades,
            'wins' => $wins,
            'losses' => $losses,
            'be' => $be,
            'inProgress' => $inProgress,
            'winningRatio' => $winningRatio,
            'averageRr' => $averageRr,
            'maxDrawdown' => $maxDrawdown,
            'netProfit' => $netProfit,
            'equity' => $equity,
        ];
    }
}
