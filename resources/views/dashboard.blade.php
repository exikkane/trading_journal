@extends('layouts.app')

@section('content')
<div class="card" style="margin-bottom: 16px;">
    <div class="row" style="gap: 24px;">
        <div>
            <div class="stat-title">Time Filter</div>
            <div class="row" style="margin-top: 6px;">
                <a class="btn {{ $filter === 'all' ? 'secondary' : 'light' }}" href="{{ route('dashboard', ['filter' => 'all', 'account' => $accountId ?? 'all']) }}">All</a>
                <a class="btn {{ $filter === 'month' ? 'secondary' : 'light' }}" href="{{ route('dashboard', ['filter' => 'month', 'account' => $accountId ?? 'all']) }}">Current Month</a>
                <a class="btn {{ $filter === 'quarter' ? 'secondary' : 'light' }}" href="{{ route('dashboard', ['filter' => 'quarter', 'account' => $accountId ?? 'all']) }}">Current Quarter</a>
            </div>
        </div>
        <div>
            <div class="stat-title">Account Filter</div>
            <form method="GET" action="{{ route('dashboard') }}" class="row" style="margin-top: 6px;">
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

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-title">Total Trades</div>
        <div class="stat-value">{{ $totalTrades }}</div>
        <div class="stat-sub">
            Wins: {{ $wins }} · Losses: {{ $losses }} · BE: {{ $be }} · In Progress: {{ $inProgress }}
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Winning Ratio</div>
        <div class="stat-value">{{ number_format($winningRatio, 2, '.', '') }}%</div>
        <div class="stat-sub">Wins ÷ (Wins + Losses).</div>
    </div>
    <div class="stat-card">
        <div class="stat-title">AVG RR</div>
        <div class="stat-value">{{ number_format($averageRr, 2, '.', '') }}</div>
        <div class="stat-sub">Based on winning trades only.</div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Net Profit</div>
        <div class="stat-value">{{ number_format($netProfit, 2, '.', '') }}%</div>
        <div class="stat-sub">Sum of all closed trades.</div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Max Drawdown</div>
        <div class="stat-value">{{ number_format($maxDrawdown, 2, '.', '') }}%</div>
        <div class="stat-sub">Longest loss streak in %.</div>
    </div>
</div>

@if ($previousStats)
    @php
        $deltaWinRate = $winningRatio - $previousStats['winningRatio'];
        $deltaAvgRr = $averageRr - $previousStats['averageRr'];
        $deltaNet = $netProfit - $previousStats['netProfit'];
        $formatDelta = function ($value, $suffix = '') {
            $sign = $value >= 0 ? '+' : '';
            return $sign . number_format((float) $value, 2, '.', '') . $suffix;
        };
    @endphp
    <div class="card vs-card" style="margin-top: 16px;">
        <div class="row">
            <div class="stat-title">VS Previous Period</div>
            <div class="spacer"></div>
            <div class="muted" style="font-size: 12px;">{{ $previousLabel }}</div>
        </div>
        <div class="row" style="margin-top: 12px; gap: 24px;">
            <div class="vs-metric">
                Win Rate
                <strong>{{ number_format($winningRatio, 2, '.', '') }}%</strong>
                <span class="stat-chip {{ $deltaWinRate < 0 ? 'negative' : '' }}">{{ $formatDelta($deltaWinRate, '%') }}</span>
            </div>
            <div class="vs-metric">
                AVG RR
                <strong>{{ number_format($averageRr, 2, '.', '') }}</strong>
                <span class="stat-chip {{ $deltaAvgRr < 0 ? 'negative' : '' }}">{{ $formatDelta($deltaAvgRr) }}</span>
            </div>
            <div class="vs-metric">
                Net Profit
                <strong>{{ number_format($netProfit, 2, '.', '') }}%</strong>
                <span class="stat-chip {{ $deltaNet < 0 ? 'negative' : '' }}">{{ $formatDelta($deltaNet, '%') }}</span>
            </div>
        </div>
    </div>
@endif

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
            <rect width="100%" height="100%" fill="#0f1627"></rect>
            @if (! is_null($zeroY))
                <line x1="0" x2="{{ $width }}" y1="{{ $zeroY }}" y2="{{ $zeroY }}" stroke="#1f2a3d" stroke-width="2"></line>
            @endif
            <polyline fill="none" stroke="#8b5cf6" stroke-width="3" points="{{ $pointsString }}"></polyline>
        </svg>
    @endif
</div>
@endsection
