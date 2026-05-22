<?php

namespace App\Http\Controllers;

use App\Models\DailyReview;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Services\NotionSyncService;

class DailyReviewController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        $review = DailyReview::updateOrCreate(
            ['user_id' => $user->id, 'review_date' => now()->toDateString()],
            [
                'daily_focus' => $request->daily_focus,
                'focus_score' => $request->focus_score,
                'summary' => $request->summary
            ]
        );

        app(NotionSyncService::class)->syncDailyReviewToNotion($review);

        // Auto-collect unfinished tasks
        $tasksToUpdate = Task::where('user_id', $user->id)
            ->where('is_done', false)
            ->whereNull('carry_over_date')
            ->get();

        foreach ($tasksToUpdate as $task) {
            $task->update(['carry_over_date' => now()->addDay()->toDateString()]);
            app(NotionSyncService::class)->syncTaskToNotion($task);
        }

        return response()->json($review);
    }
}
