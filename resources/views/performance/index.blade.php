@extends('layouts.app')

@section('content')
<div class="card">
    <div class="row" style="margin-bottom: 16px;">
        <div>
            <h2 style="margin: 0 0 4px 0;">Performance Analysis</h2>
            <div class="muted" style="font-size: 13px;">Aggregate trade statistics by period.</div>
        </div>
        <div class="spacer"></div>
        <div class="row">
            <a class="btn {{ $scope === 'mpa' ? 'secondary' : 'light' }}" href="{{ route('performance.index', ['scope' => 'mpa', 'mpa' => $mpaFilter, 'year' => $selectedYear]) }}">MPA</a>
            <a class="btn {{ $scope === 'qpa' ? 'secondary' : 'light' }}" href="{{ route('performance.index', ['scope' => 'qpa', 'year' => $selectedYear]) }}">QPA</a>
        </div>
    </div>

    @if ($scope === 'mpa')
        <div class="row" style="margin-bottom: 16px;">
            <div>
                <div class="muted" style="font-size: 13px;">Monthly Performance Analysis</div>
                <div class="row" style="margin-top: 6px;">
                    <a class="btn {{ $mpaFilter === 'this_quarter' ? 'secondary' : 'light' }}" href="{{ route('performance.index', ['scope' => 'mpa', 'mpa' => 'this_quarter']) }}">This Quarter</a>
                    <a class="btn {{ $mpaFilter === 'by_quarter' ? 'secondary' : 'light' }}" href="{{ route('performance.index', ['scope' => 'mpa', 'mpa' => 'by_quarter', 'year' => $selectedYear]) }}">By Quarter per Year</a>
                    <a class="btn {{ $mpaFilter === 'by_years' ? 'secondary' : 'light' }}" href="{{ route('performance.index', ['scope' => 'mpa', 'mpa' => 'by_years']) }}">By Years</a>
                </div>
            </div>
            <div class="spacer"></div>
            @if ($mpaFilter === 'by_quarter')
                <form method="GET" action="{{ route('performance.index') }}">
                    <input type="hidden" name="scope" value="mpa">
                    <input type="hidden" name="mpa" value="by_quarter">
                    <select name="year" onchange="this.form.submit()">
                        @foreach ($yearOptions as $year)
                            <option value="{{ $year }}" {{ (int) $year === (int) $selectedYear ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>

        @if ($mpaFilter === 'by_years')
            @foreach ($yearGroups as $group)
                <div class="row" style="margin: 16px 0 8px;">
                    <div class="stat-title">Year {{ $group['year'] }} <div style="margin-left: 10px;" class="badge {{ $group['profit_pct'] >= 0 ? 'bullish' : 'bearish' }}">{{ number_format((float) $group['profit_pct'], 2, '.', '') }}%</div></div>
                </div>
                <div class="perf-grid" style="margin-bottom: 20px;">
                    @foreach ($group['months'] as $card)
                        @include('performance.partials.month-card', ['card' => $card])
                    @endforeach
                </div>
            @endforeach
        @else
            @foreach ($quarterGroups as $group)
                <div class="row" style="margin: 16px 0 8px;">
                    <div class="stat-title">{{ $group['label'] }} {{ $group['year'] }}<div style="margin-left: 10px;" class="badge  {{ $group['profit_pct'] >= 0 ? 'bullish' : 'bearish' }}">{{ number_format((float) $group['profit_pct'], 2, '.', '') }}%</div></div>


                </div>
                <div class="perf-grid" style="margin-bottom: 20px;">
                    @foreach ($group['months'] as $card)
                        @include('performance.partials.month-card', ['card' => $card])
                    @endforeach
                </div>
            @endforeach
        @endif
    @else
        <div class="row" style="margin-bottom: 16px;">
            <div>
                <div class="muted" style="font-size: 13px;">Quarter Performance Analysis</div>
                <div class="muted" style="font-size: 12px;">Grouped by year.</div>
            </div>
            <div class="spacer"></div>
            <form method="GET" action="{{ route('performance.index') }}">
                <input type="hidden" name="scope" value="qpa">
                <select name="year" onchange="this.form.submit()">
                    @foreach ($yearOptions as $year)
                        <option value="{{ $year }}" {{ (int) $year === (int) $selectedYear ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="perf-grid">
            @foreach ($quarterCards as $card)
                <a class="perf-card perf-card-link" href="{{ route('performance.detail', ['type' => 'quarter', 'year' => $card['year'], 'period' => $card['quarter']]) }}">
                    <div class="perf-title">{{ $card['label'] }} {{ $card['year'] }}</div>
                    <div class="perf-row">Q: {{ $card['stats']['total'] }} | W: {{ $card['stats']['wins'] }} | L: {{ $card['stats']['losses'] }} | B: {{ $card['stats']['be'] }}</div>
                    <div class="perf-row">Profit: {{ number_format((float) $card['stats']['profit_pct'], 2, '.', '') }}% | {{ number_format((float) $card['stats']['profit_amount'], 2, '.', '') }}$</div>
                    <div class="perf-row">RR: {{ number_format((float) $card['stats']['rr_total'], 2, '.', '') }}</div>
                    <div class="perf-row">AVG RR: {{ number_format((float) $card['stats']['avg_rr'], 2, '.', '') }}</div>
                    <div class="perf-row">Win Rate: {{ number_format((float) $card['stats']['win_rate'], 2, '.', '') }}%</div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
