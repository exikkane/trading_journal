@extends('layouts.app')

@section('content')
<div class="card trades-card" style="margin-bottom: 16px;">
    <div class="row trades-toolbar">
        <div>
            <div class="muted" style="font-size: 13px;">Filters</div>
            <div class="row" style="margin-top: 6px;">
                <a class="btn {{ $filter === 'all' ? 'secondary' : 'light' }}" href="{{ route('trades.index', ['filter' => 'all', 'pair' => $pairName ?? 'all']) }}">All</a>
                <a class="btn {{ $filter === 'week' ? 'secondary' : 'light' }}" href="{{ route('trades.index', ['filter' => 'week', 'pair' => $pairName ?? 'all']) }}">Current Week</a>
                <a class="btn {{ $filter === 'month' ? 'secondary' : 'light' }}" href="{{ route('trades.index', ['filter' => 'month', 'pair' => $pairName ?? 'all']) }}">Current Month</a>
                <a class="btn {{ $filter === 'quarter' ? 'secondary' : 'light' }}" href="{{ route('trades.index', ['filter' => 'quarter', 'pair' => $pairName ?? 'all']) }}">Current Quarter</a>

            </div>
        </div>
        <div>
            <div class="muted" style="font-size: 13px;">Pair</div>
            <form method="GET" action="{{ route('trades.index') }}" class="row" style="margin-top: 6px;">
                <input type="hidden" name="filter" value="{{ $filter }}">
                <select name="pair" onchange="this.form.submit()">
                    <option value="all">All Pairs</option>
                    @foreach ($pairs as $pair)
                        <option value="{{ $pair->name }}" {{ ($pairName ?? 'all') === $pair->name ? 'selected' : '' }}>{{ $pair->name }}</option>
                    @endforeach
                </select>
                <noscript><button class="btn light" type="submit">Apply</button></noscript>
            </form>
        </div>
        <div class="spacer"></div>
        <a class="btn" href="{{ route('trades.create') }}">Add Trade</a>
    </div>
</div>

<div class="trades-card">
    <div style="overflow-x: auto;">
        <table class="trades-table">
            <thead>
                <tr>
                    <th>Trade ID</th>
                    <th>Start - End Date</th>
                    <th>Direction</th>
                    <th>Pair</th>
                    <th>Result</th>
                    <th>Profit</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($trades as $trade)
                    @php
                        $updateFormId = 'trade-update-' . $trade->id;
                        $deleteFormId = 'trade-delete-' . $trade->id;
                    @endphp
                    <tr>
                        <td>#{{ $trade->id }}</td>
                        <td>
                            <div class="row">
                                <input class="date-input" type="date" name="start_date" value="{{ $trade->start_date->format('Y-m-d') }}" form="{{ $updateFormId }}" required>
                                <span class="muted">-</span>
                                <input class="date-input" type="date" name="end_date" value="{{ $trade->end_date ? $trade->end_date->format('Y-m-d') : '' }}" form="{{ $updateFormId }}">
                            </div>
                        </td>
                        <td>
                            <select class="chip-select chip-direction chip-direction-{{ $trade->direction }}" data-chip="direction" name="direction" form="{{ $updateFormId }}" required>
                                <option value="long" {{ $trade->direction === 'long' ? 'selected' : '' }}>Long</option>
                                <option value="short" {{ $trade->direction === 'short' ? 'selected' : '' }}>Short</option>
                            </select>
                        </td>
                        <td>
                            <span class="trade-pair">{{ $trade->pair }}</span>
                            <input type="hidden" name="pair" value="{{ $trade->pair }}" form="{{ $updateFormId }}">
                        </td>
                        <td>
                            <select class="chip-select chip-result chip-result-{{ $trade->result }}" data-chip="result" name="result" form="{{ $updateFormId }}" required>
                                <option class="in_progress" value="in_progress" {{ $trade->result === 'in_progress' ? 'selected' : '' }}>In progress</option>
                                <option class="win" value="win" {{ $trade->result === 'win' ? 'selected' : '' }}>Win</option>
                                <option class="loss" value="loss" {{ $trade->result === 'loss' ? 'selected' : '' }}>Loss</option>
                                <option class="be" value="be" {{ $trade->result === 'be' ? 'selected' : '' }}>BE</option>
                            </select>
                        </td>
                        <td>{{ number_format((float) ($profits[$trade->id] ?? 0), 2, '.', '') }}%</td>
                        <td class="actions trades-actions">
                            <form id="{{ $updateFormId }}" method="POST" action="{{ route('trades.update', ['trade' => $trade, 'filter' => $filter, 'pair' => $pairName ?? 'all']) }}">
                                @csrf
                                @method('PUT')
                            </form>
                            <form id="{{ $deleteFormId }}" method="POST" action="{{ route('trades.destroy', ['trade' => $trade, 'filter' => $filter, 'pair' => $pairName ?? 'all']) }}" onsubmit="return confirm('Delete this trade?')">
                                @csrf
                                @method('DELETE')
                            </form>
                            <div class="row">
                                <button class="btn secondary" type="submit" form="{{ $updateFormId }}">Save</button>
                                <a class="btn light" href="{{ route('trades.show', $trade) }}">Details</a>
                                <button class="btn light" type="submit" form="{{ $deleteFormId }}">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="muted">No trades yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    (function () {
        const chips = {
            direction: ['long', 'short'],
            result: ['win', 'loss', 'be', 'in_progress'],
        };
        document.querySelectorAll('.chip-select').forEach((select) => {
            const kind = select.dataset.chip;
            const values = chips[kind] || [];
            const update = () => {
                values.forEach((value) => {
                    select.classList.remove(`chip-${kind}-${value}`);
                });
                select.classList.add(`chip-${kind}-${select.value}`);
            };
            select.addEventListener('change', update);
            update();
        });
    })();
</script>
@endsection
