<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use Illuminate\Http\Request;
use App\Services\NotionSyncService;

class HabitController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        $habit = Habit::updateOrCreate(
            ['id' => $request->id, 'user_id' => $user->id],
            [
                'time_block_id' => $request->time_block_id,
                'title' => $request->title,
                'target' => $request->target,
            ]
        );

        app(NotionSyncService::class)->syncHabit($habit);

        return response()->json($habit);
    }

    public function toggle(Habit $habit)
    {
        $today = now()->toDateString();
        $completion = $habit->completions()->whereDate('date', $today)->first();

        if ($completion) {
            $completion->delete();
            return response()->json(['status' => 'removed']);
        } else {
            $habit->completions()->create(['date' => $today]);
            return response()->json(['status' => 'added']);
        }
    }

    public function destroy(Habit $habit)
    {
        app(NotionSyncService::class)->deleteEntity($habit);
        $habit->delete();
        return response()->json(['message' => 'Habit deleted']);
    }
}
