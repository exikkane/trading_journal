@extends('layouts.app')

@section('content')
<div class="card" style="margin-bottom: 16px;">
    <div class="row" style="margin-bottom: 12px;">
        <div>
            <h2 style="margin: 0 0 4px 0;">Notes</h2>
            <div class="muted" style="font-size: 13px;">Track notes linked to a performance timeframe.</div>
        </div>
    </div>
    <form method="POST" action="{{ route('notes.store') }}" class="row" style="gap: 12px; align-items: flex-end;">
        @csrf
        <div class="field" style="min-width: 220px;">
            <label for="note_name">Name</label>
            <input id="note_name" type="text" name="name" value="{{ old('name') }}" placeholder="Monthly recap" required>
        </div>
        <div class="field">
            <label for="note_date">Date</label>
            <input id="note_date" type="date" name="note_date" value="{{ old('note_date', $prefillDate) }}" required>
        </div>
        <div style="margin-top: 22px;">
            <button class="btn" type="submit">Add Note</button>
        </div>
    </form>
</div>

<div class="card">
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($notes as $note)
                    @php
                        $deleteFormId = 'delete-note-' . $note->id;
                    @endphp
                    <tr>
                        <td>{{ $note->name }}</td>
                        <td>{{ $note->note_date->format('Y-m-d') }}</td>
                        <td class="actions">
                            <form id="{{ $deleteFormId }}" method="POST" action="{{ route('notes.destroy', $note) }}" onsubmit="return confirm('Delete this note?')">
                                @csrf
                                @method('DELETE')
                            </form>
                            <div class="row">
                                <a class="btn light" href="{{ route('notes.show', $note) }}">Details</a>
                                <button class="btn light" type="submit" form="{{ $deleteFormId }}">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="muted">No notes yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
