@extends('layouts.app')

@section('content')
<div class="card">
    <div class="row" style="margin-bottom: 16px;">
        <div>
            <h2 style="margin: 0 0 4px 0;">Trading Plan Details</h2>
            <div class="muted" style="font-size: 13px;">Read-only view of the plan.</div>
        </div>
        <div class="spacer"></div>
        <a class="btn light" href="{{ route('plans.index') }}">Back to List</a>
        <a class="btn" href="{{ route('plans.edit', $plan) }}">Edit</a>
    </div>

    <div class="grid three" style="margin-bottom: 16px;">
        <div class="field">
            <label>Date</label>
            <input type="text" value="{{ $plan->plan_date->format('Y-m-d') }}" readonly>
        </div>
        <div class="field">
            <label>Pair</label>
            <input type="text" value="{{ $plan->pair }}" readonly>
        </div>
        <div class="field">
            <label>Narrative</label>
            <input type="text" value="{{ ucfirst($plan->narrative) }}" readonly>
        </div>
    </div>

    @php
        $leftImages = [
            '/plan-images/left-1.jpg',
            '/plan-images/left-2.jpg',
            '/plan-images/left-3.jpg',
        ];
    @endphp

    <div class="grid plan-split">
        <div class="plan-section">
            <h3>Reference Images</h3>
            <div class="image-stack">
                @foreach ($leftImages as $index => $path)
                    <img src="{{ $path }}" alt="Reference image {{ $index + 1 }}" style="width: 100%; border: 1px solid var(--border); border-radius: 8px;">
                @endforeach
            </div>
        </div>
        <div class="plan-section">
            <h3>Chart Analysis</h3>
            <div class="field">
                <label>Weekly Screenshot</label>
                @if ($plan->weekly_chart_screenshot_path)
                    <img src="{{ Storage::url($plan->weekly_chart_screenshot_path) }}" alt="Weekly chart screenshot" style="max-width: 100%; border: 1px solid var(--border); border-radius: 8px;">
                @else
                    <div class="muted" style="font-size: 12px;">No screenshot</div>
                @endif
            </div>
            <div class="field">
                <label>Weekly Description</label>
                <textarea readonly>{{ $plan->weekly_chart_notes }}</textarea>
            </div>
            <div class="field">
                <label>Daily Screenshot</label>
                @if ($plan->daily_chart_screenshot_path)
                    <img src="{{ Storage::url($plan->daily_chart_screenshot_path) }}" alt="Daily chart screenshot" style="max-width: 100%; border: 1px solid var(--border); border-radius: 8px;">
                @else
                    <div class="muted" style="font-size: 12px;">No screenshot</div>
                @endif
            </div>
            <div class="field">
                <label>Daily Description</label>
                <textarea readonly>{{ $plan->daily_chart_notes }}</textarea>
            </div>
        </div>
        <div class="plan-section">
            <h3>Trading Plan</h3>
            <div class="field">
                <label>Plan A Screenshot</label>
                @if ($plan->plan_a_screenshot_path)
                    <img src="{{ Storage::url($plan->plan_a_screenshot_path) }}" alt="Plan A screenshot" style="max-width: 100%; border: 1px solid var(--border); border-radius: 8px;">
                @else
                    <div class="muted" style="font-size: 12px;">No screenshot</div>
                @endif
            </div>
            <div class="field">
                <label>Plan A Description</label>
                <textarea readonly>{{ $plan->plan_a_notes }}</textarea>
            </div>
            <div class="field">
                <label>Plan B Screenshot</label>
                @if ($plan->plan_b_screenshot_path)
                    <img src="{{ Storage::url($plan->plan_b_screenshot_path) }}" alt="Plan B screenshot" style="max-width: 100%; border: 1px solid var(--border); border-radius: 8px;">
                @else
                    <div class="muted" style="font-size: 12px;">No screenshot</div>
                @endif
            </div>
            <div class="field">
                <label>Plan B Description</label>
                <textarea readonly>{{ $plan->plan_b_notes }}</textarea>
            </div>
            <div class="field">
                <label>Plan's cancel condition</label>
                <input type="text" value="{{ $plan->cancel_condition }}" readonly>
            </div>
        </div>
    </div>

    <div class="grid split" style="margin-top: 16px;">
        <div class="plan-section">
            <h3>Notes / Review</h3>
            <textarea readonly>{{ $plan->notes_review }}</textarea>
        </div>
        <div class="plan-section">
            <h3>Weekly Review Questions</h3>
            <div class="field">
                <label>1. Нарратив отработал?</label>
                <textarea readonly>{{ $plan->weekly_review_q1 }}</textarea>
            </div>
            <div class="field">
                <label>2. Как можно было монетизировать фактический нарратив?</label>
                <textarea readonly>{{ $plan->weekly_review_q2 }}</textarea>
            </div>
            <div class="field">
                <label>3. Какие особенности PA можно выделить за неделю?</label>
                <textarea readonly>{{ $plan->weekly_review_q3 }}</textarea>
            </div>
            <div class="field">
                <label>4. Какие сильные стороны открытых позиций можно отметить?</label>
                <textarea readonly>{{ $plan->weekly_review_q4 }}</textarea>
            </div>
            <div class="field">
                <label>5. Насколько эффективным был торговый план? Что можно улучшить?</label>
                <textarea readonly>{{ $plan->weekly_review_q5 }}</textarea>
            </div>
        </div>
    </div>
</div>
@endsection
