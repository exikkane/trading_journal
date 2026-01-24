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
            <div class="row">
                <button class="btn" type="submit">Create Account</button>
            </div>
        </form>
    </div>
    <div class="card">
        <div class="stat-title">Notes</div>
        <div class="stat-sub">
            Accounts link trades together and power account-level stats. Current balance is calculated from current balance (if set) plus net profit % of the initial balance.
        </div>
    </div>
</div>

<div class="card">
    <h2 style="margin: 0 0 16px 0;">Accounts</h2>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Initial Balance</th>
                    <th>Current Balance</th>
                    <th>Winning Ratio</th>
                    <th>Total Trades</th>
                    <th>Wins</th>
                    <th>Losses</th>
                    <th>BE</th>
                    <th>In Progress</th>
                    <th>Profit %</th>
                    <th>Profit $</th>
                    <th>Max Drawdown</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($accounts as $account)
                    @php
                        $stat = $stats[$account->id] ?? null;
                    @endphp
                    <tr>
                        <td>{{ $account->name }}</td>
                        <td>{{ number_format((float) $account->initial_balance, 2, '.', '') }}</td>
                        <td>{{ number_format((float) ($stat['current_balance'] ?? $account->current_balance ?? 0), 2, '.', '') }}</td>
                        <td>{{ number_format((float) ($stat['winning_ratio'] ?? 0), 2, '.', '') }}%</td>
                        <td>{{ $stat['total'] ?? 0 }}</td>
                        <td>{{ $stat['wins'] ?? 0 }}</td>
                        <td>{{ $stat['losses'] ?? 0 }}</td>
                        <td>{{ $stat['be'] ?? 0 }}</td>
                        <td>{{ $stat['in_progress'] ?? 0 }}</td>
                        <td>{{ number_format((float) ($stat['net_profit_pct'] ?? 0), 2, '.', '') }}%</td>
                        <td>{{ number_format((float) ($stat['profit_amount'] ?? 0), 2, '.', '') }}</td>
                        <td>{{ number_format((float) ($stat['max_drawdown'] ?? 0), 2, '.', '') }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="muted">No accounts yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
