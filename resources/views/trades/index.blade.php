@extends('layouts.app')

@section('content')
<div class="card">
    <div class="row">
        <div>
            <div class="muted" style="font-size: 13px;">Filters</div>
            <div class="row" style="margin-top: 6px;">
                <a class="btn {{ $filter === 'all' ? 'secondary' : 'light' }}" href="{{ route('trades.index', ['filter' => 'all']) }}">All</a>
                <a class="btn {{ $filter === 'week' ? 'secondary' : 'light' }}" href="{{ route('trades.index', ['filter' => 'week']) }}">Current Week</a>
                <a class="btn {{ $filter === 'month' ? 'secondary' : 'light' }}" href="{{ route('trades.index', ['filter' => 'month']) }}">Current Month</a>
                <a class="btn {{ $filter === 'quarter' ? 'secondary' : 'light' }}" href="{{ route('trades.index', ['filter' => 'quarter']) }}">Current Quarter</a>

            </div>
        </div>
        <div class="spacer"></div>
        <a class="btn" href="{{ route('trades.create') }}">Add Trade</a>
    </div>

    <div style="margin-top: 20px; overflow-x: auto;">
        <table>
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
                            <select name="direction" form="{{ $updateFormId }}" required>
                                <option value="long" {{ $trade->direction === 'long' ? 'selected' : '' }}>Long</option>
                                <option value="short" {{ $trade->direction === 'short' ? 'selected' : '' }}>Short</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="pair" value="{{ $trade->pair }}" form="{{ $updateFormId }}" required>
                        </td>
                        <td>
                            <select name="result" form="{{ $updateFormId }}" required>
                                <option class="in_progress" value="in_progress" {{ $trade->result === 'in_progress' ? 'selected' : '' }}>In progress</option>
                                <option class="win" value="win" {{ $trade->result === 'win' ? 'selected' : '' }}>Win</option>
                                <option class="loss" value="loss" {{ $trade->result === 'loss' ? 'selected' : '' }}>Loss</option>
                                <option class="be" value="be" {{ $trade->result === 'be' ? 'selected' : '' }}>BE</option>
                            </select>
                        </td>
                        <td>{{ number_format((float) ($profits[$trade->id] ?? 0), 2, '.', '') }}%</td>
                        <td class="actions">
                            <form id="{{ $updateFormId }}" method="POST" action="{{ route('trades.update', ['trade' => $trade, 'filter' => $filter]) }}">
                                @csrf
                                @method('PUT')
                            </form>
                            <form id="{{ $deleteFormId }}" method="POST" action="{{ route('trades.destroy', ['trade' => $trade, 'filter' => $filter]) }}" onsubmit="return confirm('Delete this trade?')">
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
@endsection
