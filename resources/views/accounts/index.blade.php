@extends('layouts.app')

@section('content')
<div class="grid two" style="margin-bottom: 16px;">
    <div class="card">
        <h2 style="margin: 0 0 12px 0;">Add Account</h2>
        <form method="POST" action="{{ route('accounts.store') }}" class="grid">
            @csrf
            <div class="field">
                <label for="name">Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="initial_balance">Initial Balance</label>
                <input id="initial_balance" type="number" step="0.01" min="0" name="initial_balance" value="{{ old('initial_balance') }}" required>
                @error('initial_balance')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="current_balance">Current Balance (optional)</label>
                <input id="current_balance" type="number" step="0.01" min="0" name="current_balance" value="{{ old('current_balance') }}">
                @error('current_balance')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" {{ old('status', \App\Models\Account::STATUS_EVAL_STAGE_1) === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="row">
                <button class="btn" type="submit">Create Account</button>
            </div>
        </form>
    </div>
    <div class="card">
        <div class="row" style="margin-bottom: 12px;">
            <div class="stat-title">Pairs</div>
        </div>
        <div class="grid two">
            @foreach ($pairCategories as $key => $label)
                <div>
                    <div class="stat-title" style="margin-bottom: 8px;">{{ $label }}</div>
                    <div class="muted" style="font-size: 12px; margin-bottom: 8px;">
                        {{ $pairsByCategory->get($key, collect())->implode('name', ', ') ?: 'No pairs yet.' }}
                    </div>
                    <form method="POST" action="{{ route('accounts.pairs.store') }}" class="row">
                        @csrf
                        <input type="hidden" name="category" value="{{ $key }}">
                        <input type="text" name="name" placeholder="Add {{ $label }} pair" required>
                        <button class="btn light" type="submit">Add</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="row" style="margin-bottom: 12px;">
    <div>
        <h2 style="margin: 0 0 4px 0;">Accounts</h2>
        <div class="muted" style="font-size: 13px;">Active accounts on the left, archived on the right.</div>
    </div>
</div>

<div class="grid split accounts-split">
    <div class="grid" style="gap: 16px;">
        <div class="card">
            <div class="row" style="margin-bottom: 12px;">
                <div class="stat-title">Active Accounts</div>
            </div>
            <div class="accounts-grid">
            @forelse ($activeAccounts as $account)
                @php
                    $stat = $stats[$account->id] ?? null;
                    $statusLabel = $statusLabels[$account->status] ?? 'Unknown';
                    $statusBadge = $statusBadgeClasses[$account->status] ?? 'neutral';
                @endphp
                <div class="perf-card account-card">
                    <div class="row" style="justify-content: space-between;">
                        <div class="perf-title">{{ $account->name }}</div>
                        <span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span>
                    </div>
                    <div class="perf-row">Trades: Q: {{ $stat['total'] ?? 0 }} | W: {{ $stat['wins'] ?? 0 }} | L: {{ $stat['losses'] ?? 0 }} | B: {{ $stat['be'] ?? 0 }} | P: {{ $stat['in_progress'] ?? 0 }}</div>
                    <div class="perf-row">Profit: {{ number_format((float) ($stat['net_profit_pct'] ?? 0), 2, '.', '') }}% | {{ number_format((float) ($stat['profit_amount'] ?? 0), 2, '.', '') }}$</div>
                    <div class="perf-row">Win Rate: {{ number_format((float) ($stat['winning_ratio'] ?? 0), 2, '.', '') }}% | Max DD: {{ number_format((float) ($stat['max_drawdown'] ?? 0), 2, '.', '') }}%</div>
                    <div class="perf-row">Balance: {{ number_format((float) ($stat['current_balance'] ?? 0), 2, '.', '') }}$ / {{ number_format((float) $account->initial_balance, 2, '.', '') }}$</div>
                    <div class="perf-row">Payouts: {{ number_format((float) ($stat['payouts_sum'] ?? 0), 2, '.', '') }}$</div>
                    <form method="POST" action="{{ route('accounts.status', $account) }}" style="margin-top: 8px;">
                        @csrf
                        <select name="status" onchange="this.form.submit()" style="width: 100%;">
                            @foreach ($statusLabels as $value => $label)
                                <option value="{{ $value }}" {{ $account->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            @empty
                <div class="muted">No active accounts yet.</div>
            @endforelse
            </div>
        </div>

        <div class="card">
            <div class="row" style="margin-bottom: 12px;">
                <h3 style="margin: 0;">Payouts</h3>
                <div class="spacer"></div>
                <button class="btn" type="submit" form="payout-form">Add Payout</button>
            </div>
            <form id="payout-form" method="POST" action="{{ route('accounts.payouts.store') }}">
                @csrf
            </form>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Account</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input form="payout-form" class="date-input" type="date" name="payout_date" value="{{ old('payout_date') }}" required>
                            </td>
                            <td>
                                <select form="payout-form" name="account_id" required>
                                    <option value="" disabled {{ old('account_id') ? '' : 'selected' }}>Select account</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}" {{ (string) old('account_id') === (string) $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input form="payout-form" type="number" step="0.01" name="amount" value="{{ old('amount') }}" required>
                            </td>
                        </tr>
                        @forelse ($payouts as $payout)
                            <tr>
                                <td>{{ $payout->payout_date->format('Y-m-d') }}</td>
                                <td>{{ $payout->account?->name ?? '—' }}</td>
                                <td>{{ number_format((float) $payout->amount, 2, '.', '') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="muted">No payouts yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="row" style="margin-bottom: 12px;">
            <div class="stat-title">Archived Accounts</div>
        </div>
        <div class="accounts-grid">
            @forelse ($archivedAccounts as $account)
                @php
                    $stat = $stats[$account->id] ?? null;
                    $statusLabel = $statusLabels[$account->status] ?? 'Unknown';
                    $statusBadge = $statusBadgeClasses[$account->status] ?? 'neutral';
                @endphp
                <div class="perf-card account-card">
                    <div class="row" style="justify-content: space-between;">
                        <div class="perf-title">{{ $account->name }}</div>
                        <span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span>
                    </div>
                    <div class="perf-row">Trades: Q: {{ $stat['total'] ?? 0 }} | W: {{ $stat['wins'] ?? 0 }} | L: {{ $stat['losses'] ?? 0 }} | B: {{ $stat['be'] ?? 0 }} | P: {{ $stat['in_progress'] ?? 0 }}</div>
                    <div class="perf-row">Profit: {{ number_format((float) ($stat['net_profit_pct'] ?? 0), 2, '.', '') }}% | {{ number_format((float) ($stat['profit_amount'] ?? 0), 2, '.', '') }}$</div>
                    <div class="perf-row">Win Rate: {{ number_format((float) ($stat['winning_ratio'] ?? 0), 2, '.', '') }}% | Max DD: {{ number_format((float) ($stat['max_drawdown'] ?? 0), 2, '.', '') }}%</div>
                    <div class="perf-row">Balance: {{ number_format((float) ($stat['current_balance'] ?? 0), 2, '.', '') }}$ / {{ number_format((float) $account->initial_balance, 2, '.', '') }}$</div>
                    <div class="perf-row">Payouts: {{ number_format((float) ($stat['payouts_sum'] ?? 0), 2, '.', '') }}$</div>
                    <form method="POST" action="{{ route('accounts.status', $account) }}" style="margin-top: 8px;">
                        @csrf
                        <select name="status" onchange="this.form.submit()" style="width: 100%;">
                            @foreach ($statusLabels as $value => $label)
                                <option value="{{ $value }}" {{ $account->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            @empty
                <div class="muted">No archived accounts yet.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
