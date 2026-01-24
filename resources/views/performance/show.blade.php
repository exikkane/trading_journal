@extends('layouts.app')

@section('content')
@php
    $periodLabel = $type === 'month'
        ? \Carbon\Carbon::create($year, $period, 1)->format('F Y')
        : 'Q' . $period . ' ' . $year;
@endphp

<div class="card">
    <div class="row" style="margin-bottom: 16px;">
        <div>
            <h2 style="margin: 0 0 4px 0;">Performance Detail — {{ $periodLabel }}</h2>
            <div class="muted" style="font-size: 13px;">{{ $start->format('Y-m-d') }} to {{ $end->format('Y-m-d') }}</div>
        </div>
        <div class="spacer"></div>
        <a class="btn light" href="{{ route('performance.index') }}">Back to Performance</a>
    </div>

    <div class="plan-section">
        <div class="perf-title" style="margin-bottom: 8px;">Short Statistics</div>
        <div class="perf-row">Q: {{ $stats['total'] }} | W: {{ $stats['wins'] }} | L: {{ $stats['losses'] }} | B: {{ $stats['be'] }}</div>
        <div class="perf-row">Profit: {{ number_format((float) $stats['profit_pct'], 2, '.', '') }}% | {{ number_format((float) $stats['profit_amount'], 2, '.', '') }}$</div>
        <div class="perf-row">RR: {{ number_format((float) $stats['rr_total'], 2, '.', '') }}</div>
        <div class="perf-row">AVG RR: {{ number_format((float) $stats['avg_rr'], 2, '.', '') }}</div>
        <div class="perf-row">Win Rate: {{ number_format((float) $stats['win_rate'], 2, '.', '') }}%</div>
    </div>

    <div class="plan-section" style="margin-top: 16px;">
        <h3>Accounts</h3>
        @forelse ($accountsStats as $accountStat)
            <div style="margin-bottom: 12px;">
                <div class="perf-title">{{ $accountStat['account']->name }}</div>
                <div class="perf-row">
                    Profit: {{ number_format((float) $accountStat['profit_pct'], 2, '.', '') }}%
                    / {{ number_format((float) $accountStat['profit_amount'], 2, '.', '') }}$
                    / {{ number_format((float) $accountStat['rr_total'], 2, '.', '') }} RR
                    / AVG RR {{ number_format((float) $accountStat['avg_rr'], 2, '.', '') }}
                </div>
            </div>
        @empty
            <div class="muted" style="font-size: 13px;">No account stats for this period.</div>
        @endforelse
    </div>

    @if ($type === 'quarter')
        <div class="plan-section" style="margin-top: 16px;">
            <h3>Quarter Months</h3>
            <div class="perf-grid">
                @foreach ($monthCards as $card)
                    @include('performance.partials.month-card', ['card' => $card])
                @endforeach
            </div>
        </div>
    @endif

    <div class="plan-section" style="margin-top: 16px;">
        <h3>Analysis History (Plans)</h3>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Pair</th>
                        <th>Narrative</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($plans as $plan)
                        <tr>
                            <td>{{ $plan->plan_date->format('Y-m-d') }}</td>
                            <td>{{ $plan->pair }}</td>
                            <td>
                                <span class="badge {{ $plan->narrative }}">{{ ucfirst($plan->narrative) }}</span>
                            </td>
                            <td class="actions">
                                <a class="btn light" href="{{ route('plans.show', $plan) }}">Show</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="muted">No plans for this period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <form method="POST" action="{{ route('performance.detail.update', ['type' => $type, 'year' => $year, 'period' => $period, 'result' => $resultFilter]) }}" enctype="multipart/form-data">
        @csrf

        <div class="plan-section" style="margin-top: 16px;">
            <h3>MPA METRIX</h3>
            <div class="field" style="margin-bottom: 12px;">
                <label for="mpa_metric">That has improved/deteriorated. Why?</label>
                <textarea id="mpa_metric" name="mpa_metric">{{ old('mpa_metric', $review?->mpa_metric) }}</textarea>
            </div>
        </div>

        <div class="plan-section" style="margin-top: 16px;">
            <div class="row" style="margin-bottom: 12px;">
                <div>
                    <h3 style="margin: 0;">Position History</h3>
                    <div class="muted" style="font-size: 12px;">Filter by result</div>
                </div>
                <div class="spacer"></div>
                <div class="row">
                    <a class="btn {{ $resultFilter === 'all' ? 'secondary' : 'light' }}" href="{{ route('performance.detail', ['type' => $type, 'year' => $year, 'period' => $period, 'result' => 'all']) }}">All</a>
                    <a class="btn {{ $resultFilter === 'win' ? 'secondary' : 'light' }}" href="{{ route('performance.detail', ['type' => $type, 'year' => $year, 'period' => $period, 'result' => 'win']) }}">Win</a>
                    <a class="btn {{ $resultFilter === 'loss' ? 'secondary' : 'light' }}" href="{{ route('performance.detail', ['type' => $type, 'year' => $year, 'period' => $period, 'result' => 'loss']) }}">Loss</a>
                </div>
            </div>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Pair</th>
                            <th>Date</th>
                            <th>Result</th>
                            <th>RR / PnL</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tradesList as $trade)
                            <tr>
                                <td>{{ $trade['pair'] }}</td>
                                <td>{{ $trade['date'] }}</td>
                                <td>
                                    <span class="badge {{ $trade['result'] }}">{{ strtoupper($trade['result']) }}</span>
                                </td>
                                <td>
                                    {{ number_format((float) $trade['avg_rr'], 2, '.', '') }} RR
                                    / {{ number_format((float) $trade['profit_pct'], 2, '.', '') }}%
                                </td>
                                <td>
                                    <a class="btn light" href="{{ route('trades.show', $trade['id']) }}">Details</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="muted">No trades for this filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="plan-section" style="margin-top: 16px;">
            <h3>TRADES CONCLUSIONS</h3>
            <div class="field">
                <label for="trades_conclusions">Strong & Weak Points / Trading Errors</label>
                <textarea id="trades_conclusions" name="trades_conclusions">{{ old('trades_conclusions', $review?->trades_conclusions) }}</textarea>
            </div>
        </div>

        <div class="plan-section" style="margin-top: 16px;">
            <h3>Notes</h3>
            <div class="field" style="margin-bottom: 12px;">
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes">{{ old('notes', $review?->notes) }}</textarea>
            </div>
            <div class="field">
                <label for="notes_screenshots">Upload screenshots</label>
                <input id="notes_screenshots" type="file" name="notes_screenshots[]" accept="image/*" multiple>
            </div>
            @if (!empty($review?->notes_screenshots))
                <div class="image-stack" style="margin-top: 12px;">
                    @foreach ($review->notes_screenshots as $path)
                        <img src="{{ Storage::url($path) }}" alt="Note screenshot" style="max-width: 100%; border: 1px solid var(--border); border-radius: 8px;">
                    @endforeach
                </div>
            @endif
        </div>

        <div class="plan-section" style="margin-top: 16px;">
            <h3>SUMMARY</h3>
            <div class="field" style="margin-bottom: 12px;">
                <label for="summary_general">General conclusion</label>
                <textarea id="summary_general" name="summary_general">{{ old('summary_general', $review?->summary_general) }}</textarea>
            </div>
            <div class="field" style="margin-bottom: 12px;">
                <label for="summary_what_works">What works?</label>
                <textarea id="summary_what_works" name="summary_what_works">{{ old('summary_what_works', $review?->summary_what_works) }}</textarea>
            </div>
            <div class="field" style="margin-bottom: 12px;">
                <label for="summary_what_not">What doesn't work?</label>
                <textarea id="summary_what_not" name="summary_what_not">{{ old('summary_what_not', $review?->summary_what_not) }}</textarea>
            </div>
            <div class="field" style="margin-bottom: 12px;">
                <label for="summary_key_lessons">Key Lessons</label>
                <textarea id="summary_key_lessons" name="summary_key_lessons">{{ old('summary_key_lessons', $review?->summary_key_lessons) }}</textarea>
            </div>
            <div class="field">
                <label for="summary_next_steps">Next Steps</label>
                <textarea id="summary_next_steps" name="summary_next_steps">{{ old('summary_next_steps', $review?->summary_next_steps) }}</textarea>
            </div>
        </div>

        <div class="row" style="margin-top: 16px;">
            <button class="btn" type="submit">Save Analysis</button>
        </div>
    </form>
</div>
@endsection
