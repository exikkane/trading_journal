<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountTrade;
use App\Models\Trade;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatsController extends Controller
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
        $tradeQuery = Trade::query()->orderBy('start_date');

        if ($filter !== 'all') {
            $now = Carbon::now();
            $start = $filter === 'month' ? $now->copy()->startOfMonth() : $now->copy()->startOfQuarter();
            $end = $filter === 'month' ? $now->copy()->endOfMonth() : $now->copy()->endOfQuarter();
            $tradeQuery->whereBetween('start_date', [$start->toDateString(), $end->toDateString()]);
            $dateRange = [$start->toDateString(), $end->toDateString()];
        }

        $accountTradeQuery = AccountTrade::query()->with('trade');
        if ($dateRange) {
            $accountTradeQuery->whereHas('trade', function ($q) use ($dateRange) {
                $q->whereBetween('start_date', $dateRange);
            });
        }
        if ($accountId) {
            $accountTradeQuery->where('account_id', $accountId);
        }

        $accountTrades = $accountTradeQuery->get();
        $trades = $accountId ? collect() : $tradeQuery->get();

        if ($accountId) {
            $wins = $accountTrades->filter(fn ($at) => $at->trade && $at->trade->result === 'win')->count();
            $losses = $accountTrades->filter(fn ($at) => $at->trade && $at->trade->result === 'loss')->count();
            $be = $accountTrades->filter(fn ($at) => $at->trade && $at->trade->result === 'be')->count();
            $inProgress = $accountTrades->filter(fn ($at) => $at->trade && $at->trade->result === 'in_progress')->count();
            $totalTrades = $accountTrades->count();
        } else {
            $totalTrades = $trades->count();
            $wins = $trades->where('result', 'win')->count();
            $losses = $trades->where('result', 'loss')->count();
            $be = $trades->where('result', 'be')->count();
            $inProgress = $trades->where('result', 'in_progress')->count();
        }

        $winsLosses = $wins + $losses;
        $winningRatio = $winsLosses > 0 ? ($wins / $winsLosses) * 100 : 0;
        $averageRr = $accountTrades->filter(fn ($at) => $at->trade && $at->trade->result === 'win')->count() > 0
            ? (float) $accountTrades->filter(fn ($at) => $at->trade && $at->trade->result === 'win')->avg('risk_reward')
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
            }

            $running += $delta;
            $netProfit = $running;
            $equity[] = $running;
        }

        $accounts = Account::query()->orderBy('name')->get();

        return view('stats', [
            'filter' => $filter,
            'accountId' => $accountId,
            'accounts' => $accounts,
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
