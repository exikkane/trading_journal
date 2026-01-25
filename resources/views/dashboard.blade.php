@extends('layouts.app')

@section('content')
<div class="card" style="margin-bottom: 16px;">
    <div class="row" style="gap: 24px;">
        <div>
            <div class="stat-title">Time Filter</div>
            <div class="row" style="margin-top: 6px;">
                <a class="btn {{ $filter === 'all' ? 'secondary' : 'light' }}" href="{{ route('dashboard', ['filter' => 'all', 'account' => $accountId ?? 'all', 'pair' => $pairName ?? 'all', 'year' => $selectedYear, 'range' => $range]) }}">All</a>
                <a class="btn {{ $filter === 'month' ? 'secondary' : 'light' }}" href="{{ route('dashboard', ['filter' => 'month', 'account' => $accountId ?? 'all', 'pair' => $pairName ?? 'all', 'year' => $selectedYear, 'range' => $range]) }}">Current Month</a>
                <a class="btn {{ $filter === 'quarter' ? 'secondary' : 'light' }}" href="{{ route('dashboard', ['filter' => 'quarter', 'account' => $accountId ?? 'all', 'pair' => $pairName ?? 'all', 'year' => $selectedYear, 'range' => $range]) }}">Current Quarter</a>
            </div>
        </div>
        <div>
            <div class="stat-title">Account Filter</div>
            <form method="GET" action="{{ route('dashboard') }}" class="row" style="margin-top: 6px;">
                <input type="hidden" name="filter" value="{{ $filter }}">
                <input type="hidden" name="pair" value="{{ $pairName ?? 'all' }}">
                <input type="hidden" name="range" value="{{ $range }}">
                <input type="hidden" name="year" value="{{ $selectedYear }}">
                <select name="account" onchange="this.form.submit()">
                    <option value="all">All Accounts</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}" {{ (string) $accountId === (string) $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                    @endforeach
                </select>
                <noscript><button class="btn light" type="submit">Apply</button></noscript>
            </form>
        </div>
        <div>
            <div class="stat-title">Pair Filter</div>
            <form method="GET" action="{{ route('dashboard') }}" class="row" style="margin-top: 6px;">
                <input type="hidden" name="filter" value="{{ $filter }}">
                <input type="hidden" name="account" value="{{ $accountId ?? 'all' }}">
                <input type="hidden" name="range" value="{{ $range }}">
                <input type="hidden" name="year" value="{{ $selectedYear }}">
                <select name="pair" onchange="this.form.submit()">
                    <option value="all">All Pairs</option>
                    @foreach ($pairs as $pair)
                        <option value="{{ $pair->name }}" {{ ($pairName ?? 'all') === $pair->name ? 'selected' : '' }}>{{ $pair->name }}</option>
                    @endforeach
                </select>
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

<div class="grid two" style="margin-top: 16px;">
    <div class="card">
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

    <div class="card">
        <div class="row" style="margin-bottom: 12px;">
            <div>
                <div class="stat-title">Monthly Analytics</div>
                <div class="muted" style="font-size: 13px;">Monthly Gain (Change)</div>
                <div class="row" style="margin-top: 6px;">
                    <a class="btn {{ $range === 'recent' ? 'secondary' : 'light' }}" href="{{ route('dashboard', ['filter' => $filter, 'account' => $accountId ?? 'all', 'pair' => $pairName ?? 'all', 'year' => $selectedYear, 'range' => 'recent']) }}">Last 5</a>
                    <a class="btn {{ $range === 'quarter' ? 'secondary' : 'light' }}" href="{{ route('dashboard', ['filter' => $filter, 'account' => $accountId ?? 'all', 'pair' => $pairName ?? 'all', 'year' => $selectedYear, 'range' => 'quarter']) }}">Last Quarter</a>
                    <a class="btn {{ $range === 'year' ? 'secondary' : 'light' }}" href="{{ route('dashboard', ['filter' => $filter, 'account' => $accountId ?? 'all', 'pair' => $pairName ?? 'all', 'year' => $selectedYear, 'range' => 'year']) }}">Year</a>
                </div>
            </div>
            <div class="spacer"></div>
            <form method="GET" action="{{ route('dashboard') }}">
                <input type="hidden" name="filter" value="{{ $filter }}">
                <input type="hidden" name="account" value="{{ $accountId ?? 'all' }}">
                <input type="hidden" name="pair" value="{{ $pairName ?? 'all' }}">
                <input type="hidden" name="range" value="{{ $range }}">
                <select name="year" onchange="this.form.submit()">
                    @foreach ($yearOptions as $yearOption)
                        <option value="{{ $yearOption }}" {{ (int) $selectedYear === (int) $yearOption ? 'selected' : '' }}>{{ $yearOption }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        @php
            $monthlyValues = $monthlyAnalytics['values'] ?? array_fill(1, 12, 0.0);
            $selectedMonths = $monthlyAnalytics['months'] ?? range(1, 12);
            $selectedValues = [];
            foreach ($selectedMonths as $month) {
                $selectedValues[] = (float) ($monthlyValues[$month] ?? 0);
            }
            $minValue = min($selectedValues);
            $maxValue = max($selectedValues);
            $minAxis = floor(min($minValue, 0) / 10) * 10;
            $maxAxis = ceil(max($maxValue, 0) / 10) * 10;
            if ($minAxis === $maxAxis) {
                $minAxis -= 10;
                $maxAxis += 10;
            }
            $axisStep = 10;
            $width = 1000;
            $height = 260;
            $leftPad = 50;
            $rightPad = 20;
            $topPad = 10;
            $bottomPad = 20;
            $chartWidth = $width - $leftPad - $rightPad;
            $chartHeight = $height - $topPad - $bottomPad;
            $barCount = max(count($selectedMonths), 1);
            $barSlot = $chartWidth / $barCount;
            $barWidth = $barSlot * 0.65;
            $zeroY = $topPad + ($maxAxis / ($maxAxis - $minAxis)) * $chartHeight;
            $colors = ['#a78bfa', '#f59e0b', '#22c55e', '#38bdf8', '#f97316', '#ef4444', '#eab308', '#10b981', '#06b6d4', '#f472b6', '#8b5cf6', '#84cc16'];
        @endphp

        <svg viewBox="0 0 {{ $width }} {{ $height }}" width="100%" height="260" preserveAspectRatio="none" role="img" aria-label="Monthly gain chart">
            <rect width="100%" height="100%" fill="#0f1627"></rect>
            <line x1="{{ $leftPad }}" x2="{{ $leftPad }}" y1="{{ $topPad }}" y2="{{ $height - $bottomPad }}" stroke="#1f2a3d" stroke-width="2"></line>
            @for ($tick = $minAxis; $tick <= $maxAxis; $tick += $axisStep)
                @php
                    $yTick = $topPad + ($maxAxis - $tick) / ($maxAxis - $minAxis) * $chartHeight;
                @endphp
                <line x1="{{ $leftPad }}" x2="{{ $width - $rightPad }}" y1="{{ $yTick }}" y2="{{ $yTick }}" stroke="#1f2a3d" stroke-width="1"></line>
                <text x="{{ $leftPad - 8 }}" y="{{ $yTick + 4 }}" text-anchor="end" fill="#94a3b8" font-size="11">{{ $tick }}%</text>
            @endfor

            @foreach ($selectedMonths as $index => $month)
                @php
                    $value = (float) ($monthlyValues[$month] ?? 0);
                    $x = $leftPad + ($index * $barSlot) + ($barSlot - $barWidth) / 2;
                    $yValue = $topPad + ($maxAxis - $value) / ($maxAxis - $minAxis) * $chartHeight;
                    if ($value >= 0) {
                        $barY = $yValue;
                        $barH = $zeroY - $yValue;
                    } else {
                        $barY = $zeroY;
                        $barH = $yValue - $zeroY;
                    }
                    $barH = max($barH, 1);
                    $color = $colors[$index % count($colors)];
                    $labelY = $value >= 0 ? max($barY - 6, $topPad + 10) : min($barY + $barH + 14, $height - 6);
                @endphp
                <rect x="{{ $x }}" y="{{ $barY }}" width="{{ $barWidth }}" height="{{ $barH }}" fill="{{ $color }}" rx="4" ry="4"></rect>
                <text x="{{ $x + $barWidth / 2 }}" y="{{ $labelY }}" text-anchor="middle" fill="{{ $color }}" font-size="12">
                    {{ number_format($value, 2, '.', '') }}%
                </text>
            @endforeach
        </svg>
        <div style="display: grid; grid-template-columns: repeat({{ count($selectedMonths) }}, minmax(0, 1fr)); margin-top: 8px; font-size: 11px; color: var(--muted);">
            @foreach ($selectedMonths as $month)
                <div style="text-align: center; white-space: nowrap;">
                    {{ \Carbon\Carbon::create($selectedYear, $month, 1)->format('M Y') }}
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
