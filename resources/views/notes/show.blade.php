@extends('layouts.app')

@section('content')
<div class="card">
    <div class="row" style="margin-bottom: 16px;">
        <div>
            <h2 style="margin: 0 0 4px 0;">Note — {{ $note->name }}</h2>
            <div class="muted" style="font-size: 13px;">{{ $note->note_date->format('Y-m-d') }}</div>
        </div>
        <div class="spacer"></div>
        <a class="btn light" href="{{ route('notes.index') }}">Back to Notes</a>
    </div>

    <form method="POST" action="{{ route('notes.update', $note) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="plan-section" style="margin-bottom: 16px;">
            <h3>Details</h3>
            <div class="grid two" style="margin-top: 12px;">
                <div class="field">
                    <label for="note_name">Name</label>
                    <input id="note_name" type="text" name="name" value="{{ old('name', $note->name) }}" required>
                </div>
                <div class="field">
                    <label for="note_date">Date</label>
                    <input id="note_date" type="date" name="note_date" value="{{ old('note_date', $note->note_date->format('Y-m-d')) }}" required>
                </div>
            </div>
        </div>

        <div class="plan-section" style="margin-bottom: 16px;">
            <h3>Description</h3>
            <div class="field">
                <label for="description">Notes</label>
                <textarea id="description" name="description" class="tall-textarea">{{ old('description', $note->description) }}</textarea>
            </div>
        </div>

        <div class="plan-section">
            <h3>Screenshots</h3>
            <div class="field" style="margin-bottom: 12px;">
                <label for="screenshots">Upload screenshots</label>
                <input id="screenshots" type="file" name="screenshots[]" accept="image/*" multiple>
            </div>
            @if (!empty($note->screenshots))
                <div class="image-stack" style="margin-top: 12px;">
                    @foreach ($note->screenshots as $path)
                        <img src="{{ Storage::url($path) }}" alt="Note screenshot" style="max-width: 100%; border: 1px solid var(--border); border-radius: 8px;">
                    @endforeach
                </div>
            @endif
        </div>

        <div class="row" style="margin-top: 16px;">
            <button class="btn" type="submit">Save Note</button>
        </div>
    </form>
</div>
@endsection
