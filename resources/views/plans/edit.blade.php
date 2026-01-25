@extends('layouts.app')

@section('content')
<div class="card">
    <div class="row" style="margin-bottom: 16px;">
        <div>
            <h2 style="margin: 0 0 4px 0;">Edit Trading Plan</h2>
            <div class="muted" style="font-size: 13px;">Update your narrative, charts, and review answers.</div>
        </div>
        <div class="spacer"></div>
        <a class="btn light" href="{{ route('plans.index') }}">Back to List</a>
    </div>

    <form method="POST" action="{{ route('plans.update', $plan) }}" class="grid" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid three">
            <div class="field">
                <label for="plan_date">Date</label>
                <input id="plan_date" type="date" name="plan_date" value="{{ old('plan_date', $plan->plan_date->format('Y-m-d')) }}" required>
                @error('plan_date')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="pair">Pair</label>
                <select id="pair" name="pair" required>
                    @foreach ($pairCategories as $key => $label)
                        @php $pairs = $pairsByCategory->get($key, collect()); @endphp
                        @if ($pairs->isNotEmpty())
                            <optgroup label="{{ $label }}">
                                @foreach ($pairs as $pair)
                                    <option value="{{ $pair->name }}" {{ old('pair', $plan->pair) === $pair->name ? 'selected' : '' }}>{{ $pair->name }}</option>
                                @endforeach
                            </optgroup>
                        @endif
                    @endforeach
                </select>
                @error('pair')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="narrative">Narrative</label>
                <select id="narrative" name="narrative" required>
                    <option value="bullish" {{ old('narrative', $plan->narrative) === 'bullish' ? 'selected' : '' }}>Bullish</option>
                    <option value="bearish" {{ old('narrative', $plan->narrative) === 'bearish' ? 'selected' : '' }}>Bearish</option>
                    <option value="neutral" {{ old('narrative', $plan->narrative) === 'neutral' ? 'selected' : '' }}>Neutral</option>
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
                        <img src="{{ $path }}" alt="Reference image {{ $index + 1 }}" style="width: 90%; border: 1px solid var(--border); border-radius: 8px;">
                    @endforeach
                </div>
                <div class="muted" style="font-size: 12px; margin-top: 8px;">Update the image paths in this file.</div>
            </div>
            <div class="plan-section">
                <h3>Chart Analysis</h3>
                <div class="field">
                    <label for="weekly_chart_screenshot">Weekly Screenshot</label>
                    <input id="weekly_chart_screenshot" type="file" name="weekly_chart_screenshot" accept="image/*">
                    @if ($plan->weekly_chart_screenshot_path)
                        <div style="margin-top: 8px;">
                            <img src="{{ Storage::url($plan->weekly_chart_screenshot_path) }}" alt="Weekly chart screenshot" style="max-width: 90%; border: 1px solid var(--border); border-radius: 8px;">
                        </div>
                    @endif
                    @error('weekly_chart_screenshot')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="weekly_chart_notes">Weekly Description</label>
                    <textarea id="weekly_chart_notes" name="weekly_chart_notes" placeholder="Weekly narrative...">{{ old('weekly_chart_notes', $plan->weekly_chart_notes) }}</textarea>
                    @error('weekly_chart_notes')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="daily_chart_screenshot">Daily Screenshot</label>
                    <input id="daily_chart_screenshot" type="file" name="daily_chart_screenshot" accept="image/*">
                    @if ($plan->daily_chart_screenshot_path)
                        <div style="margin-top: 8px;">
                            <img src="{{ Storage::url($plan->daily_chart_screenshot_path) }}" alt="Daily chart screenshot" style="max-width: 90%; border: 1px solid var(--border); border-radius: 8px;">
                        </div>
                    @endif
                    @error('daily_chart_screenshot')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="daily_chart_notes">Daily Description</label>
                    <textarea id="daily_chart_notes" name="daily_chart_notes" placeholder="Daily narrative...">{{ old('daily_chart_notes', $plan->daily_chart_notes) }}</textarea>
                    @error('daily_chart_notes')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="dxy_chart_screenshot">DXY Screenshot</label>
                    <input id="dxy_chart_screenshot" type="file" name="dxy_chart_screenshot" accept="image/*">
                    @if ($plan->dxy_chart_screenshot_path)
                        <div style="margin-top: 8px;">
                            <img src="{{ Storage::url($plan->dxy_chart_screenshot_path) }}" alt="DXY chart screenshot" style="max-width: 90%; border: 1px solid var(--border); border-radius: 8px;">
                        </div>
                    @endif
                    @error('dxy_chart_screenshot')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="dxy_chart_notes">DXY Description</label>
                    <textarea id="dxy_chart_notes" name="dxy_chart_notes" placeholder="DXY narrative...">{{ old('dxy_chart_notes', $plan->dxy_chart_notes) }}</textarea>
                    @error('dxy_chart_notes')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="plan-section">
                <h3>Trading Plan</h3>
                <div class="field">
                    <label for="plan_a_screenshot">Plan A Screenshot</label>
                    <input id="plan_a_screenshot" type="file" name="plan_a_screenshot" accept="image/*">
                    @if ($plan->plan_a_screenshot_path)
                        <div style="margin-top: 8px;">
                            <img src="{{ Storage::url($plan->plan_a_screenshot_path) }}" alt="Plan A screenshot" style="max-width: 100%; border: 1px solid var(--border); border-radius: 8px;">
                        </div>
                    @endif
                    @error('plan_a_screenshot')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="plan_a_notes">Plan A Description</label>
                    <textarea id="plan_a_notes" name="plan_a_notes" placeholder="Plan A details...">{{ old('plan_a_notes', $plan->plan_a_notes) }}</textarea>
                    @error('plan_a_notes')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="plan_b_screenshot">Plan B Screenshot</label>
                    <input id="plan_b_screenshot" type="file" name="plan_b_screenshot" accept="image/*">
                    @if ($plan->plan_b_screenshot_path)
                        <div style="margin-top: 8px;">
                            <img src="{{ Storage::url($plan->plan_b_screenshot_path) }}" alt="Plan B screenshot" style="max-width: 100%; border: 1px solid var(--border); border-radius: 8px;">
                        </div>
                    @endif
                    @error('plan_b_screenshot')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="plan_b_notes">Plan B Description</label>
                    <textarea id="plan_b_notes" name="plan_b_notes" placeholder="Plan B details...">{{ old('plan_b_notes', $plan->plan_b_notes) }}</textarea>
                    @error('plan_b_notes')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="cancel_condition">Plan's invalidation condition</label>
                    <textarea id="cancel_condition" name="cancel_condition" placeholder="What invalidates the plan?">{{ old('cancel_condition', $plan->cancel_condition) }}</textarea>
                    @error('cancel_condition')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="plan-section" style="margin-top: 16px;">
            <div class="row" style="margin-bottom: 12px;">
                <h3 style="margin: 0;">Updates</h3>
                <div class="spacer"></div>
                <button class="btn" type="button" id="toggle-updates">Updates</button>
            </div>
            <form id="plan-update-form" method="POST" action="{{ route('plans.updates.store', $plan) }}" enctype="multipart/form-data" class="grid" style="display: none;">
                @csrf
                <div class="field">
                    <label for="update_screenshots">Update Screenshots</label>
                    <input id="update_screenshots" type="file" name="update_screenshots[]" accept="image/*" multiple>
                    @error('update_screenshots')
                        <div class="error">{{ $message }}</div>
                    @enderror
                    @error('update_screenshots.*')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="row">
                    <button class="btn" type="submit">Save Update</button>
                </div>
            </form>

            @if (! empty($updates) && $updates->isNotEmpty())
                <div class="image-stack" style="margin-top: 16px;">
                    @foreach ($updates as $update)
                        <div class="plan-section">
                            <div class="perf-title">Update - {{ $update->update_date->format('Y-m-d') }}</div>
                            <div class="field" style="margin-top: 8px;">
                                <textarea class="plan-textarea" readonly>{{ $update->update_notes }}</textarea>
                            </div>
                            @if (! empty($update->update_screenshots))
                                <div class="image-stack" style="margin-top: 12px;">
                                    @foreach ($update->update_screenshots as $path)
                                        <img src="{{ Storage::url($path) }}" alt="Plan update screenshot" style="max-width: 100%; border: 1px solid var(--border); border-radius: 8px;">
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="muted" style="font-size: 12px; margin-top: 8px;">No updates yet.</div>
            @endif
        </div>

        <div class="grid split">
            <div class="plan-section">
                <h3>Notes / Review</h3>
                <div class="field">
                    <label for="notes_review">Notes / Review</label>
                    <textarea id="notes_review" name="notes_review" placeholder="Weekly notes and reflections...">{{ old('notes_review', $plan->notes_review) }}</textarea>
                    @error('notes_review')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="plan-section">
                <h3>Weekly Review Questions</h3>
                <div class="field">
                    <label for="weekly_review_q1">1. Нарратив отработал?</label>
                    <textarea id="weekly_review_q1" name="weekly_review_q1">{{ old('weekly_review_q1', $plan->weekly_review_q1) }}</textarea>
                    @error('weekly_review_q1')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="weekly_review_q2">2. Как можно было монетизировать фактический нарратив?</label>
                    <textarea id="weekly_review_q2" name="weekly_review_q2">{{ old('weekly_review_q2', $plan->weekly_review_q2) }}</textarea>
                    @error('weekly_review_q2')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="weekly_review_q3">3. Какие особенности PA можно выделить за неделю?</label>
                    <textarea id="weekly_review_q3" name="weekly_review_q3">{{ old('weekly_review_q3', $plan->weekly_review_q3) }}</textarea>
                    @error('weekly_review_q3')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="weekly_review_q4">4. Какие сильные стороны открытых позиций можно отметить?</label>
                    <textarea id="weekly_review_q4" name="weekly_review_q4">{{ old('weekly_review_q4', $plan->weekly_review_q4) }}</textarea>
                    @error('weekly_review_q4')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="weekly_review_q5">5. Насколько эффективным был торговый план? Что можно улучшить?</label>
                    <textarea id="weekly_review_q5" name="weekly_review_q5">{{ old('weekly_review_q5', $plan->weekly_review_q5) }}</textarea>
                    @error('weekly_review_q5')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 8px;">
            <button class="btn" type="submit">Save Changes</button>
        </div>
    </form>
</div>
<script>
    const toggleButton = document.getElementById('toggle-updates');
    const updateForm = document.getElementById('plan-update-form');
    if (toggleButton && updateForm) {
        toggleButton.addEventListener('click', () => {
            const visible = updateForm.style.display !== 'none';
            updateForm.style.display = visible ? 'none' : 'grid';
        });
    }
</script>
@endsection
