<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NotesController extends Controller
{
    public function index(Request $request)
    {
        $notes = Note::query()
            ->orderByDesc('note_date')
            ->orderByDesc('id')
            ->get();

        $prefillDate = $request->query('date');
        if (! $prefillDate || ! strtotime($prefillDate)) {
            $prefillDate = Carbon::now()->toDateString();
        }

        return view('notes.index', [
            'notes' => $notes,
            'prefillDate' => $prefillDate,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'note_date' => ['required', 'date'],
        ]);

        $note = Note::create($data);

        return redirect()->route('notes.show', $note);
    }

    public function show(Note $note)
    {
        return view('notes.show', [
            'note' => $note,
        ]);
    }

    public function update(Request $request, Note $note)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'note_date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
            'screenshots' => ['nullable', 'array'],
            'screenshots.*' => ['image', 'max:5120'],
        ]);

        if ($request->hasFile('screenshots')) {
            $existing = $note->screenshots ?? [];
            foreach ($request->file('screenshots') as $file) {
                $existing[] = $file->store('notes', 'public');
            }
            $data['screenshots'] = $existing;
        } else {
            unset($data['screenshots']);
        }

        $note->update($data);

        return redirect()->route('notes.show', $note);
    }

    public function destroy(Note $note)
    {
        if (! empty($note->screenshots)) {
            foreach ($note->screenshots as $path) {
                Storage::disk('public')->delete($path);
            }
        }

        $note->delete();

        return redirect()->route('notes.index');
    }
}
