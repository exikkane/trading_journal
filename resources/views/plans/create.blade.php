@extends('layouts.app')

@section('content')
<div class="card">
    <div class="row" style="margin-bottom: 16px;">
        <div>
            <h2 style="margin: 0 0 4px 0;">Add Trading Plan</h2>
            <div class="muted" style="font-size: 13px;">Capture your market narrative, charts, and weekly review.</div>
        </div>
        <div class="spacer"></div>
        <a class="btn light" href="{{ route('plans.index') }}">Back to List</a>
    </div>

    <form method="POST" action="{{ route('plans.store') }}" class="grid" enctype="multipart/form-data">
        @csrf

        <div class="grid three">
            <div class="field">
                <label for="plan_date">Date</label>
                <input id="plan_date" type="date" name="plan_date" value="{{ old('plan_date') }}" required>
                @error('plan_date')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="pair">Pair</label>
                <input id="pair" type="text" name="pair" value="{{ old('pair') }}" placeholder="EUR/USD" required>
                @error('pair')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="narrative">Narrative</label>
                <select id="narrative" name="narrative" required>
                    <option value="" disabled {{ old('narrative') ? '' : 'selected' }}>Select</option>
                    <option value="bullish" {{ old('narrative') === 'bullish' ? 'selected' : '' }}>Bullish</option>
                    <option value="bearish" {{ old('narrative') === 'bearish' ? 'selected' : '' }}>Bearish</option>
                    <option value="neutral" {{ old('narrative') === 'neutral' ? 'selected' : '' }}>Neutral</option>
                </select>
                @error('narrative')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        @php
            // Update these paths to your static reference images.
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
                <div class="muted" style="font-size: 12px; margin-top: 8px;">Update the image paths in this file.</div>
            </div>
            <div class="plan-section">
                <h3>Chart Analysis</h3>
                <div class="field">
                    <label for="weekly_chart_screenshot">Weekly Screenshot</label>
                    <input id="weekly_chart_screenshot" type="file" name="weekly_chart_screenshot" accept="image/*">
                    @error('weekly_chart_screenshot')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="weekly_chart_notes">Weekly Description</label>
                    <textarea id="weekly_chart_notes" name="weekly_chart_notes" placeholder="Weekly narrative...">{{ old('weekly_chart_notes') }}</textarea>
                    @error('weekly_chart_notes')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="daily_chart_screenshot">Daily Screenshot</label>
                    <input id="daily_chart_screenshot" type="file" name="daily_chart_screenshot" accept="image/*">
                    @error('daily_chart_screenshot')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="daily_chart_notes">Daily Description</label>
                    <textarea id="daily_chart_notes" name="daily_chart_notes" placeholder="Daily narrative...">{{ old('daily_chart_notes') }}</textarea>
                    @error('daily_chart_notes')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="plan-section">
                <h3>Trading Plan</h3>
                <div class="field">
                    <label for="plan_a_screenshot">Plan A Screenshot</label>
                    <input id="plan_a_screenshot" type="file" name="plan_a_screenshot" accept="image/*">
                    @error('plan_a_screenshot')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="plan_a_notes">Plan A Description</label>
                    <textarea id="plan_a_notes" name="plan_a_notes" placeholder="Plan A details...">{{ old('plan_a_notes') }}</textarea>
                    @error('plan_a_notes')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="plan_b_screenshot">Plan B Screenshot</label>
                    <input id="plan_b_screenshot" type="file" name="plan_b_screenshot" accept="image/*">
                    @error('plan_b_screenshot')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="plan_b_notes">Plan B Description</label>
                    <textarea id="plan_b_notes" name="plan_b_notes" placeholder="Plan B details...">{{ old('plan_b_notes') }}</textarea>
                    @error('plan_b_notes')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="cancel_condition">Plan's cancel condition</label>
                    <input id="cancel_condition" type="text" name="cancel_condition" value="{{ old('cancel_condition') }}" placeholder="What invalidates the plan?">
                    @error('cancel_condition')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="grid split">
            <div class="plan-section">
                <h3>Notes / Review</h3>
                <div class="field">
                    <label for="notes_review">Notes / Review</label>
                    <textarea id="notes_review" name="notes_review" placeholder="Weekly notes and reflections...">{{ old('notes_review') }}</textarea>
                    @error('notes_review')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="plan-section">
                <h3>Weekly Review Questions</h3>
                <div class="field">
                    <label for="weekly_review_q1">1. Нарратив отработал?</label>
                    <textarea id="weekly_review_q1" name="weekly_review_q1">{{ old('weekly_review_q1') }}</textarea>
                    @error('weekly_review_q1')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="weekly_review_q2">2. Как можно было монетизировать фактический нарратив?</label>
                    <textarea id="weekly_review_q2" name="weekly_review_q2">{{ old('weekly_review_q2') }}</textarea>
                    @error('weekly_review_q2')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="weekly_review_q3">3. Какие особенности PA можно выделить за неделю?</label>
                    <textarea id="weekly_review_q3" name="weekly_review_q3">{{ old('weekly_review_q3') }}</textarea>
                    @error('weekly_review_q3')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="weekly_review_q4">4. Какие сильные стороны открытых позиций можно отметить?</label>
                    <textarea id="weekly_review_q4" name="weekly_review_q4">{{ old('weekly_review_q4') }}</textarea>
                    @error('weekly_review_q4')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="weekly_review_q5">5. Насколько эффективным был торговый план? Что можно улучшить?</label>
                    <textarea id="weekly_review_q5" name="weekly_review_q5">{{ old('weekly_review_q5') }}</textarea>
                    @error('weekly_review_q5')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 8px;">
            <button class="btn" type="submit">Save Plan</button>
        </div>
    </form>
</div>
@endsection
