@extends('layouts.app')

@section('content')
<div class="card">
    <div class="row">
        <div>
            <div class="muted" style="font-size: 13px;">Filters</div>
            <div class="row" style="margin-top: 6px;">
                <a class="btn {{ $filter === 'all' ? 'secondary' : 'light' }}" href="{{ route('plans.index', ['filter' => 'all', 'pair' => $pairName ?? 'all']) }}">All</a>
                <a class="btn {{ $filter === 'month' ? 'secondary' : 'light' }}" href="{{ route('plans.index', ['filter' => 'month', 'pair' => $pairName ?? 'all']) }}">Current Month</a>
                <a class="btn {{ $filter === 'quarter' ? 'secondary' : 'light' }}" href="{{ route('plans.index', ['filter' => 'quarter', 'pair' => $pairName ?? 'all']) }}">Current Quarter</a>
            </div>
        </div>
        <div>
            <div class="muted" style="font-size: 13px;">Pair</div>
            <form method="GET" action="{{ route('plans.index') }}" class="row" style="margin-top: 6px;">
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
        <a class="btn" href="{{ route('plans.create') }}">Add Plan</a>
    </div>

    <div style="margin-top: 20px; overflow-x: auto;">
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
                    @php
                        $deleteId = 'plan-delete-' . $plan->id;
                    @endphp
                    <tr>
                        <td>{{ $plan->plan_date->format('Y-m-d') }}</td>
                        <td>{{ $plan->pair }}</td>
                        <td>
                            <span class="badge {{ $plan->narrative }}">{{ ucfirst($plan->narrative) }}</span>
                        </td>
                        <td class="actions">
                            <div class="row">
                                <a class="btn light" href="{{ route('plans.show', $plan) }}">Details</a>
                                <a class="btn secondary" href="{{ route('plans.edit', $plan) }}">Edit</a>
                                <form id="{{ $deleteId }}" method="POST" action="{{ route('plans.destroy', ['plan' => $plan, 'filter' => $filter, 'pair' => $pairName ?? 'all']) }}" onsubmit="return confirm('Delete this plan?')">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <button class="btn light" type="submit" form="{{ $deleteId }}">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="muted">No trading plans yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
