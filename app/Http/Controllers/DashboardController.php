<?php

namespace App\Http\Controllers;

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

        $query = Trade::query()->orderBy('start_date');
        $dateRange = null;
        if ($filter !== 'all') {
            $now = Carbon::now();
            $start = $filter === 'month' ? $now->copy()->startOfMonth() : $now->copy()->startOfQuarter();
            $end = $filter === 'month' ? $now->copy()->endOfMonth() : $now->copy()->endOfQuarter();
            $query->whereBetween('start_date', [$start->toDateString(), $end->toDateString()]);
            $dateRange = [$start->toDateString(), $end->toDateString()];
        }

        $trades = $query->get();
        $accountTradeQuery = AccountTrade::query()->with('trade');
        if ($dateRange) {
            $accountTradeQuery->whereHas('trade', function ($tradeQuery) use ($dateRange) {
                $tradeQuery->whereBetween('start_date', $dateRange);
            });
        }
        $accountTrades = $accountTradeQuery->get();

        $totalTrades = $trades->count();
        $wins = $trades->where('result', 'win')->count();
        $losses = $trades->where('result', 'loss')->count();
        $be = $trades->where('result', 'be')->count();
        $inProgress = $trades->where('result', 'in_progress')->count();

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

        $accountTrades = $accountTrades->sortBy(function ($accountTrade) {
            return $accountTrade->trade ? $accountTrade->trade->start_date : null;
        })->values();

        foreach ($accountTrades as $accountTrade) {
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
            } else {
                $delta = 0.0;
            }

            $running += $delta;
            $netProfit = $running;
            $equity[] = $running;
        }

        return view('dashboard', [
            'filter' => $filter,
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
        ]);
    }
}
