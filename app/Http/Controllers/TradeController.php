<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountTrade;
use App\Models\Pair;
use App\Models\Trade;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TradeController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'all');
        if (! in_array($filter, ['week', 'month', 'quarter', 'all'], true)) {
            $filter = 'all';
        }
        $pairs = Pair::query()->orderBy('name')->get();
        $pairName = $request->query('pair', 'all');
        if ($pairName === 'all') {
            $pairName = null;
        } elseif (! $pairs->pluck('name')->contains($pairName)) {
            $pairName = null;
        }
        $query = Trade::query()->orderByDesc('start_date');

        if (in_array($filter, ['week', 'month', 'quarter'], true)) {
            $now = Carbon::now();
            $start = match ($filter) {
                'week' => $now->copy()->startOfWeek(),
                'month' => $now->copy()->startOfMonth(),
                'quarter' => $now->copy()->startOfQuarter(),
            };
            $end = match ($filter) {
                'week' => $now->copy()->endOfWeek(),
                'month' => $now->copy()->endOfMonth(),
                'quarter' => $now->copy()->endOfQuarter(),
            };

            $query->whereBetween('start_date', [$start->toDateString(), $end->toDateString()]);
        }
        if ($pairName) {
            $query->where('pair', $pairName);
        }

        $trades = $query->with('accountTrades')->get();

        $profits = [];
        foreach ($trades as $trade) {
            $profit = 0.0;
            foreach ($trade->accountTrades as $accountTrade) {
                $profit += $this->resultProfitPct($trade, $accountTrade);
            }
            $profits[$trade->id] = $profit;
        }

        return view('trades.index', [
            'trades' => $trades,
            'filter' => $filter,
            'pairName' => $pairName,
            'pairs' => $pairs,
            'profits' => $profits,
        ]);
    }

    public function create()
    {
        $pairsByCategory = Pair::query()->orderBy('name')->get()->groupBy('category');

        return view('trades.create', [
            'pairsByCategory' => $pairsByCategory,
            'pairCategories' => Pair::categories(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'direction' => ['required', 'in:long,short'],
            'pair' => ['required', Rule::exists('pairs', 'name')],
            'result' => ['required', 'in:in_progress,loss,win,be'],
            'execution' => ['nullable', 'string', 'max:255'],
            'entry_tf' => ['nullable', 'string', 'max:50'],
            'idea_notes' => ['nullable', 'string'],
            'conclusions_notes' => ['nullable', 'string'],
            'idea_screenshot' => ['nullable', 'image', 'max:5120'],
            'exit_screenshot' => ['nullable', 'image', 'max:5120'],
            'conclusion_screenshot' => ['nullable', 'image', 'max:5120'],
        ]);

        $data = $this->handleUploads($request, $data);

        Trade::create($data);

        return redirect()->route('trades.index');
    }

    public function show(Trade $trade)
    {
        $accounts = Account::query()->orderBy('name')->get();
        $pairsByCategory = Pair::query()->orderBy('name')->get()->groupBy('category');

        $accountTrades = $trade->accountTrades()->with(['account', 'trade'])->orderByDesc('id')->get();
        $accountsProfit = 0.0;
        foreach ($accountTrades as $accountTrade) {
            $accountsProfit += $this->resultProfitPct($trade, $accountTrade);
        }

        return view('trades.show', [
            'trade' => $trade,
            'accounts' => $accounts,
            'accountTrades' => $accountTrades,
            'accountsProfit' => $accountsProfit,
            'pairsByCategory' => $pairsByCategory,
            'pairCategories' => Pair::categories(),
        ]);
    }

    public function update(Request $request, Trade $trade)
    {
        $data = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'direction' => ['required', 'in:long,short'],
            'pair' => ['required', Rule::exists('pairs', 'name')],
            'result' => ['required', 'in:in_progress,loss,win,be'],
            'execution' => ['nullable', 'string', 'max:255'],
            'entry_tf' => ['nullable', 'string', 'max:50'],
            'idea_notes' => ['nullable', 'string'],
            'conclusions_notes' => ['nullable', 'string'],
            'idea_screenshot' => ['nullable', 'image', 'max:5120'],
            'exit_screenshot' => ['nullable', 'image', 'max:5120'],
            'conclusion_screenshot' => ['nullable', 'image', 'max:5120'],
        ]);

        $data = $this->handleUploads($request, $data, $trade);

        $trade->update($data);

        $redirectTo = $request->input('redirect_to');
        if ($redirectTo) {
            return redirect($redirectTo);
        }

        return redirect()->route('trades.index', [
            'filter' => $request->query('filter', 'all'),
            'pair' => $request->query('pair', 'all'),
        ]);
    }

    public function destroy(Request $request, Trade $trade)
    {
        $this->deleteUploads($trade);
        $trade->delete();

        return redirect()->route('trades.index', [
            'filter' => $request->query('filter', 'all'),
            'pair' => $request->query('pair', 'all'),
        ]);
    }

    public function storeSubtrade(Request $request, Trade $trade)
    {
        $data = $request->validate([
            'account_id' => [
                'required',
                'exists:accounts,id',
                Rule::unique('account_trades', 'account_id')->where('trade_id', $trade->id),
            ],
            'risk_reward' => ['required', 'numeric', 'min:0'],
            'risk_pct' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);
        AccountTrade::updateOrCreate(
            ['trade_id' => $trade->id, 'account_id' => $data['account_id']],
            ['risk_reward' => $data['risk_reward'], 'risk_pct' => $data['risk_pct']]
        );

        return redirect()->route('trades.show', $trade);
    }

    public function updateSubtrade(Request $request, Trade $trade, AccountTrade $subtrade)
    {
        if ($subtrade->trade_id !== $trade->id) {
            abort(404);
        }

        $data = $request->validate([
            'account_id' => [
                'required',
                'exists:accounts,id',
                Rule::unique('account_trades', 'account_id')
                    ->where('trade_id', $trade->id)
                    ->ignore($subtrade->id),
            ],
            'risk_reward' => ['required', 'numeric', 'min:0'],
            'risk_pct' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $subtrade->update($data);

        return redirect()->route('trades.show', $trade);
    }

    public function destroySubtrade(Trade $trade, AccountTrade $subtrade)
    {
        if ($subtrade->trade_id !== $trade->id) {
            abort(404);
        }

        $subtrade->delete();

        return redirect()->route('trades.show', $trade);
    }

    private function handleUploads(Request $request, array $data, ?Trade $trade = null): array
    {
        $map = [
            'idea_screenshot' => 'idea_screenshot_path',
            'exit_screenshot' => 'exit_screenshot_path',
            'conclusion_screenshot' => 'conclusion_screenshot_path',
        ];

        foreach ($map as $input => $column) {
            if ($request->hasFile($input)) {
                if ($trade && $trade->{$column}) {
                    Storage::disk('public')->delete($trade->{$column});
                }
                $data[$column] = $request->file($input)->store('trades', 'public');
            }
            unset($data[$input]);
        }

        return $data;
    }

    private function deleteUploads(Trade $trade): void
    {
        foreach (['idea_screenshot_path', 'exit_screenshot_path', 'conclusion_screenshot_path'] as $column) {
            if ($trade->{$column}) {
                Storage::disk('public')->delete($trade->{$column});
            }
        }
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
