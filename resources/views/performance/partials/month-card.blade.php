<a class="perf-card perf-card-link" href="{{ route('performance.detail', ['type' => 'month', 'year' => $card['year'], 'period' => $card['month']]) }}">
    <div class="perf-title">{{ $card['label'] }}</div>
    <div class="perf-row">Q: {{ $card['stats']['total'] }} | W: {{ $card['stats']['wins'] }} | L: {{ $card['stats']['losses'] }} | B: {{ $card['stats']['be'] }}</div>
    <div class="perf-row">Profit: {{ number_format((float) $card['stats']['profit_pct'], 2, '.', '') }}% | {{ number_format((float) $card['stats']['profit_amount'], 2, '.', '') }}$</div>
    <div class="perf-row">RR: {{ number_format((float) $card['stats']['rr_total'], 2, '.', '') }}</div>
    <div class="perf-row">AVG RR: {{ number_format((float) $card['stats']['avg_rr'], 2, '.', '') }}</div>
    <div class="perf-row">Win Rate: {{ number_format((float) $card['stats']['win_rate'], 2, '.', '') }}%</div>
</a>
