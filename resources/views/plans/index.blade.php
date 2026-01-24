@extends('layouts.app')

@section('content')
<div class="card">
    <div class="row">
        <div>
            <div class="muted" style="font-size: 13px;">Filters</div>
            <div class="row" style="margin-top: 6px;">
                <a class="btn {{ $filter === 'all' ? 'secondary' : 'light' }}" href="{{ route('plans.index', ['filter' => 'all']) }}">All</a>
                <a class="btn {{ $filter === 'month' ? 'secondary' : 'light' }}" href="{{ route('plans.index', ['filter' => 'month']) }}">Current Month</a>
                <a class="btn {{ $filter === 'quarter' ? 'secondary' : 'light' }}" href="{{ route('plans.index', ['filter' => 'quarter']) }}">Current Quarter</a>
            </div>
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
                    <tr>
                        <td>{{ $plan->plan_date->format('Y-m-d') }}</td>
                        <td>{{ $plan->pair }}</td>
                        <td>
                            <span class="badge {{ $plan->narrative }}">{{ ucfirst($plan->narrative) }}</span>
                        </td>
                        <td class="actions">
                            <a class="btn light" href="{{ route('plans.edit', $plan) }}">Edit</a>
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
