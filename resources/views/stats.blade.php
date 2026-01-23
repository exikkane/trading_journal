@extends('layouts.app')

@section('content')
<div class="card" style="margin-bottom: 16px;">
    <div class="row" style="gap: 24px;">
        <div>
            <div class="stat-title">Time Filter</div>
            <div class="row" style="margin-top: 6px;">
                <a class="btn {{ $filter === 'month' ? 'secondary' : 'light' }}" href="{{ route('stats.index', ['filter' => 'month', 'account' => $accountId ?? 'all']) }}">Current Month</a>
                <a class="btn {{ $filter === 'quarter' ? 'secondary' : 'light' }}" href="{{ route('stats.index', ['filter' => 'quarter', 'account' => $accountId ?? 'all']) }}">Current Quarter</a>
                <a class="btn {{ $filter === 'all' ? 'secondary' : 'light' }}" href="{{ route('stats.index', ['filter' => 'all', 'account' => $accountId ?? 'all']) }}">All</a>
            </div>
        </div>
        <div>
            <div class="stat-title">Account Filter</div>
            <form method="GET" action="{{ route('stats.index') }}" class="row" style="margin-top: 6px;">
                <input type="hidden" name="filter" value="{{ $filter }}">
                <select name="account" onchange="this.form.submit()">
                    <option value="all">All Accounts</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}" {{ (string) $accountId === (string) $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                    @endforeach
                </select>
                <noscript><button class="btn light" type="submit">Apply</button></noscript>
            </form>
        </div>
    </div>
</div>

<div class="grid three">
    <div class="card">
        <div class="stat-title">Total Trades</div>
        <div class="stat-value">{{ $totalTrades }}</div>
        <div class="stat-sub">
            Wins: {{ $wins }}<br>
            Losses: {{ $losses }}<br>
            BE: {{ $be }}<br>
            In Progress: {{ $inProgress }}
        </div>
    </div>
    <div class="card">
        <div class="stat-title">Winning Ratio</div>
        <div class="stat-value">{{ number_format($winningRatio, 2, '.', '') }}%</div>
        <div class="stat-sub">Wins ÷ (Wins + Losses).</div>
    </div>
    <div class="card">
        <div class="stat-title">Average RR</div>
        <div class="stat-value">{{ number_format($averageRr, 2, '.', '') }}</div>
        <div class="stat-sub">Based on winning trades only.</div>
    </div>
</div>

<div class="grid two" style="margin-top: 16px;">
    <div class="card">
        <div class="stat-title">Max Drawdown</div>
        <div class="stat-value">{{ number_format($maxDrawdown, 2, '.', '') }}%</div>
        <div class="stat-sub">Longest loss streak in %.</div>
    </div>
    <div class="card">
        <div class="stat-title">Net Profit</div>
        <div class="stat-value">{{ number_format($netProfit, 2, '.', '') }}%</div>
        <div class="stat-sub">Sum of all closed trades.</div>
    </div>
</div>

<div class="card" style="margin-top: 16px;">
    <div class="row" style="margin-bottom: 12px;">
        <div>
            <div class="stat-title">Equity Curve</div>
            <div class="muted" style="font-size: 13px;">Capital change in % over time.</div>
        </div>
        <div class="spacer"></div>
        <div class="muted" style="font-size: 13px;">Last: {{ number_format($netProfit, 2, '.', '') }}%</div>
    </div>

    @if (count($equity) <= 1)
        <div class="muted">No data yet. Add trades to see the equity curve.</div>
    @else
        @php
            $min = min($equity);
            $max = max($equity);
            $range = $max - $min;
            if ($range == 0) {
                $range = 1;
            }
            $width = 1000;
            $height = 240;
            $points = [];
            $count = count($equity);
            foreach ($equity as $index => $value) {
                $x = $count > 1 ? ($index / ($count - 1)) * $width : 0;
                $y = $height - (($value - $min) / $range) * $height;
                $points[] = $x . ',' . $y;
            }
            $pointsString = implode(' ', $points);
            $zeroY = null;
            if ($min <= 0 && $max >= 0) {
                $zeroY = $height - ((0 - $min) / $range) * $height;
            }
        @endphp
        <svg viewBox="0 0 {{ $width }} {{ $height }}" width="100%" height="240" preserveAspectRatio="none" role="img" aria-label="Equity curve">
            <rect width="100%" height="100%" fill="#f8fafc"></rect>
            @if (! is_null($zeroY))
                <line x1="0" x2="{{ $width }}" y1="{{ $zeroY }}" y2="{{ $zeroY }}" stroke="#e2e8f0" stroke-width="2"></line>
            @endif
            <polyline fill="none" stroke="#0f766e" stroke-width="3" points="{{ $pointsString }}"></polyline>
        </svg>
    @endif
</div>
@endsection
