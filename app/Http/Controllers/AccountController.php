<?php

namespace App\Http\Controllers;

use App\Models\Account;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::query()->orderBy('name')->get();

        $stats = [];
        foreach ($accounts as $account) {
            $accountTrades = $account->accountTrades()->with('trade')->get();

            $wins = $accountTrades->filter(fn ($at) => $at->trade && $at->trade->result === 'win')->count();
            $losses = $accountTrades->filter(fn ($at) => $at->trade && $at->trade->result === 'loss')->count();
            $be = $accountTrades->filter(fn ($at) => $at->trade && $at->trade->result === 'be')->count();
            $inProgress = $accountTrades->filter(fn ($at) => $at->trade && $at->trade->result === 'in_progress')->count();
            $total = $accountTrades->count();
            $winsLosses = $wins + $losses;

            $winningRatio = $winsLosses > 0 ? ($wins / $winsLosses) * 100 : 0;
            $netProfit = 0.0;
            $currentLossStreak = 0.0;
            $maxDrawdown = 0.0;

            foreach ($accountTrades as $accountTrade) {
                $trade = $accountTrade->trade;
                if (! $trade) {
                    continue;
                }

                $riskPct = (float) $accountTrade->risk_pct;
                $riskReward = (float) $accountTrade->risk_reward;

                if ($trade->result === 'win') {
                    $netProfit += $riskReward * $riskPct;
                    $currentLossStreak = 0.0;
                } elseif ($trade->result === 'loss') {
                    $netProfit -= $riskPct;
                    $currentLossStreak += $riskPct;
                    if ($currentLossStreak > $maxDrawdown) {
                        $maxDrawdown = $currentLossStreak;
                    }
                } elseif ($trade->result === 'be') {
                    $delta = $riskReward * $riskPct;
                    $netProfit += $delta;
                    if ($delta < 0) {
                        $currentLossStreak += abs($delta);
                        if ($currentLossStreak > $maxDrawdown) {
                            $maxDrawdown = $currentLossStreak;
                        }
                    } else {
                        $currentLossStreak = 0.0;
                    }
                }
            }

            $initialBalance = (float) $account->initial_balance;
            $profitAmount = $initialBalance * ($netProfit / 100);
            $currentBalance = $account->current_balance !== null
                ? (float) $account->current_balance
                : $initialBalance + $profitAmount;

            $stats[$account->id] = [
                'total' => $total,
                'wins' => $wins,
                'losses' => $losses,
                'be' => $be,
                'in_progress' => $inProgress,
                'winning_ratio' => $winningRatio,
                'net_profit_pct' => $netProfit,
                'profit_amount' => $profitAmount,
                'max_drawdown' => $maxDrawdown,
                'current_balance' => $currentBalance,
            ];
        }

        return view('accounts.index', [
            'accounts' => $accounts,
            'stats' => $stats,
        ]);
    }

    public function store()
    {
        $data = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'initial_balance' => ['required', 'numeric', 'min:0'],
            'current_balance' => ['nullable', 'numeric', 'min:0'],
        ]);

        Account::create($data);

        return redirect()->route('accounts.index');
    }
}
