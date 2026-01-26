@extends('layouts.app')

@section('content')
<div class="card">
    <div class="row" style="margin-bottom: 16px;">
        <div>
            <h2 style="margin: 0 0 4px 0;">Add Trade</h2>
            <div class="muted" style="font-size: 13px;">Create the trade idea first, then add accounts below to record results.</div>
        </div>
        <div class="spacer"></div>
        <a class="btn light" href="{{ route('trades.index') }}">Back to List</a>
    </div>

    <form method="POST" action="{{ route('trades.store') }}" class="grid" enctype="multipart/form-data">
        @csrf

        <div class="grid two">
            <div class="field">
                <label for="start_date">Start Date</label>
                <input id="start_date" type="date" name="start_date" value="{{ old('start_date') }}" required>
                @error('start_date')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="end_date">End Date</label>
                <input id="end_date" type="date" name="end_date" value="{{ old('end_date') }}">
                @error('end_date')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="grid two">
            <div class="field">
                <label for="pair">Pair</label>
                <select id="pair" name="pair" required>
                    @foreach ($pairCategories as $key => $label)
                        @php $pairs = $pairsByCategory->get($key, collect()); @endphp
                        @if ($pairs->isNotEmpty())
                            <optgroup label="{{ $label }}">
                                @foreach ($pairs as $pair)
                                    <option value="{{ $pair->name }}" {{ old('pair') === $pair->name ? 'selected' : '' }}>{{ $pair->name }}</option>
                                @endforeach
                            </optgroup>
                        @endif
                    @endforeach
                </select>
                @error('pair')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="grid three">
            <div class="field">
                <label for="direction">Direction</label>
                <select id="direction" name="direction" required>
                    <option value="" disabled {{ old('direction') ? '' : 'selected' }}>Select</option>
                    <option value="long" {{ old('direction') === 'long' ? 'selected' : '' }}>Long</option>
                    <option value="short" {{ old('direction') === 'short' ? 'selected' : '' }}>Short</option>
                </select>
                @error('direction')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="result">Result</label>
                <select id="result" name="result" required>
                    <option value="in_progress" {{ old('result', 'in_progress') === 'in_progress' ? 'selected' : '' }}>In progress</option>
                    <option value="win" {{ old('result') === 'win' ? 'selected' : '' }}>Win</option>
                    <option value="loss" {{ old('result') === 'loss' ? 'selected' : '' }}>Loss</option>
                    <option value="be" {{ old('result') === 'be' ? 'selected' : '' }}>BE</option>
                </select>
                @error('result')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="grid two">
            <div class="field">
                <label for="execution">Execution</label>
                <input id="execution" type="text" name="execution" value="{{ old('execution') }}" placeholder="FVG, IDM, etc.">
                @error('execution')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="entry_tf">Entry TF</label>
                <input id="entry_tf" type="text" name="entry_tf" value="{{ old('entry_tf') }}" placeholder="4H, Daily">
                @error('entry_tf')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="grid two">
            <div class="field">
                <label for="idea_screenshot">IDEA Screenshot</label>
                <input id="idea_screenshot" type="file" name="idea_screenshot" accept="image/*">
                @error('idea_screenshot')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="exit_screenshot">EXIT MOMENT Screenshot</label>
                <input id="exit_screenshot" type="file" name="exit_screenshot" accept="image/*">
                @error('exit_screenshot')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="grid two">
            <div class="field">
                <label for="idea_notes">IDEA Notes</label>
                <textarea id="idea_notes" name="idea_notes" placeholder="Why you took the trade...">{{ old('idea_notes') }}</textarea>
                @error('idea_notes')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="conclusion_screenshot">CONCLUSIONS Screenshot</label>
                <input id="conclusion_screenshot" type="file" name="conclusion_screenshot" accept="image/*">
                @error('conclusion_screenshot')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="field">
            <label for="conclusions_notes">CONCLUSIONS Notes</label>
            <textarea id="conclusions_notes" name="conclusions_notes" placeholder="Thoughts after the trade...">{{ old('conclusions_notes') }}</textarea>
            @error('conclusions_notes')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="row" style="margin-top: 8px;">
            <button class="btn" type="submit">Save Trade</button>
        </div>
    </form>
</div>
@endsection
