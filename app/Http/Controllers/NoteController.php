<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use App\Services\NotionSyncService;

class NoteController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        $note = Note::create([
            'user_id' => $user->id,
            'content' => $request->content,
        ]);

        app(NotionSyncService::class)->syncNote($note);

        return response()->json($note);
    }

    public function destroy(Note $note)
    {
        app(NotionSyncService::class)->deleteEntity($note);
        $note->delete();
        return response()->json(['message' => 'Note deleted']);
    }
}
