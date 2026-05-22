<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Services\NotionSyncService;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        $task = Task::create([
            'user_id' => $user->id,
            'time_block_id' => $request->time_block_id,
            'title' => $request->title,
            'category' => $request->category,
            'task_date' => $request->task_date ?: null,
            'is_done' => false,
        ]);

        app(NotionSyncService::class)->syncTaskToNotion($task);

        return response()->json($task);
    }

    public function toggle(Task $task)
    {
        $task->update(['is_done' => !$task->is_done]);
        app(NotionSyncService::class)->syncTaskToNotion($task);
        return response()->json($task);
    }

    public function update(Request $request, Task $task)
    {
        $task->update([
            'title' => $request->title,
            'time_block_id' => $request->time_block_id,
            'category' => $request->category,
            'task_date' => $request->has('task_date') ? ($request->task_date ?: null) : $task->task_date,
        ]);
        app(NotionSyncService::class)->syncTaskToNotion($task);
        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        app(NotionSyncService::class)->deleteEntity($task);
        $task->delete();
        return response()->json(['message' => 'Task deleted']);
    }

    public function carryOver(Task $task)
    {
        $task->update([
            'carry_over_date' => now()->addDay()->toDateString(),
        ]);
        app(NotionSyncService::class)->syncTaskToNotion($task);
        return response()->json($task);
    }
}
