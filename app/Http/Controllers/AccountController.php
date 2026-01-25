<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountPayout;
use App\Models\Pair;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::query()
            ->with(['accountTrades.trade', 'payouts'])
            ->orderBy('name')
            ->get();

        $stats = [];
        foreach ($accounts as $account) {
            $accountTrades = $account->accountTrades;

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
            $baseBalance = $account->current_balance !== null
                ? (float) $account->current_balance
                : $initialBalance;
            $currentBalance = $baseBalance + $profitAmount;
            $payoutsSum = (float) $account->payouts->sum('amount');

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
                'payouts_sum' => $payoutsSum,
            ];
        }

        $activeAccounts = $accounts->filter(fn ($account) => $account->archived_at === null);
        $archivedAccounts = $accounts->filter(fn ($account) => $account->archived_at !== null);

        $payouts = AccountPayout::query()
            ->with('account')
            ->orderByDesc('payout_date')
            ->get();

        $pairs = Pair::query()->orderBy('name')->get();
        $pairsByCategory = $pairs->groupBy('category');

        return view('accounts.index', [
            'accounts' => $accounts,
            'activeAccounts' => $activeAccounts,
            'archivedAccounts' => $archivedAccounts,
            'stats' => $stats,
            'payouts' => $payouts,
            'statusLabels' => Account::statusLabels(),
            'statusBadgeClasses' => Account::statusBadgeClasses(),
            'pairsByCategory' => $pairsByCategory,
            'pairCategories' => Pair::categories(),
        ]);
    }

    public function store()
    {
        $data = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'initial_balance' => ['required', 'numeric', 'min:0'],
            'current_balance' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(Account::statusValues())],
        ]);

        if (in_array($data['status'], [Account::STATUS_PASSED, Account::STATUS_FAILED], true)) {
            $data['archived_at'] = now();
        }

        Account::create($data);

        return redirect()->route('accounts.index');
    }

    public function updateStatus(Account $account)
    {
        $data = request()->validate([
            'status' => ['required', Rule::in(Account::statusValues())],
        ]);

        $status = $data['status'];
        $account->status = $status;
        if (in_array($status, [Account::STATUS_PASSED, Account::STATUS_FAILED], true)) {
            $account->archived_at = now();
        } else {
            $account->archived_at = null;
        }
        $account->save();

        return redirect()->route('accounts.index');
    }

    public function storePayout()
    {
        $data = request()->validate([
            'payout_date' => ['required', 'date'],
            'account_id' => ['required', 'exists:accounts,id'],
            'amount' => ['required', 'numeric'],
        ]);

        AccountPayout::create($data);

        return redirect()->route('accounts.index');
    }

    public function storePair()
    {
        $data = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(array_keys(Pair::categories()))],
        ]);

        Pair::firstOrCreate(
            ['name' => $data['name']],
            ['category' => $data['category']]
        );

        return redirect()->route('accounts.index');
    }
}
