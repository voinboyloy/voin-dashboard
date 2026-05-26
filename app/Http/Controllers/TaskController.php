<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TimeBlock;
use App\Models\Note;
use App\Models\MonthlyGoal;
use Illuminate\Http\Request;
use App\Services\NotionSyncService;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $taskDate = $request->task_date;
        if ($taskDate && str_contains($taskDate, 'T')) {
            $taskDate = explode('T', $taskDate)[0];
        }

        $task = Task::create([
            'user_id' => $user->id,
            'time_block_id' => $request->time_block_id,
            'title' => $request->title,
            'category' => $request->category,
            'task_date' => $taskDate ?: null,
            'is_done' => false,
            'review_note' => $request->review_note,
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
        $taskDate = $request->task_date;
        if ($taskDate && str_contains($taskDate, 'T')) {
            $taskDate = explode('T', $taskDate)[0];
        }

        $task->update([
            'title' => $request->title,
            'time_block_id' => $request->time_block_id,
            'category' => $request->category,
            'task_date' => $request->has('task_date') ? ($taskDate ?: null) : $task->task_date,
            'review_note' => $request->has('review_note') ? $request->review_note : $task->review_note,
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

    public function monthlyDashboard(Request $request)
    {
        $user = auth()->user();
        
        // Parse active month
        $monthStr = $request->query('month', now()->format('Y-m'));
        try {
            $month = Carbon::parse($monthStr . '-01');
        } catch (\Exception $e) {
            $month = now()->startOfMonth();
        }
        
        // Calculate calendar boundaries (Sunday start, Saturday end)
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();
        $startOfCalendar = $startOfMonth->copy()->startOfWeek(Carbon::SUNDAY);
        $endOfCalendar = $endOfMonth->copy()->endOfWeek(Carbon::SATURDAY);
        
        // Fetch time blocks for task assignment in forms
        $blocks = TimeBlock::where('user_id', $user->id)->orderBy('starts_at')->get();
        
        // Fetch all tasks that fall within the calendar grid range
        // Tasks are grouped by the target date (using carry_over_date if set, otherwise task_date)
        $tasksInGrid = Task::where('user_id', $user->id)
            ->where(function($query) use ($startOfCalendar, $endOfCalendar) {
                $query->whereBetween('task_date', [$startOfCalendar->toDateString(), $endOfCalendar->toDateString()])
                      ->orWhereBetween('carry_over_date', [$startOfCalendar->toDateString(), $endOfCalendar->toDateString()]);
            })
            ->with('timeBlock')
            ->get();
            
        // Group the tasks by the active date on the calendar
        $groupedTasks = $tasksInGrid->groupBy(function($task) {
            return $task->carry_over_date ?: $task->task_date;
        });
        
        // Build the calendar days grid array
        $days = [];
        $current = $startOfCalendar->copy();
        while($current <= $endOfCalendar) {
            $dateStr = $current->format('Y-m-d');
            $dayTasks = $groupedTasks->get($dateStr, collect());
            
            $days[] = [
                'date' => $current->copy(),
                'isCurrentMonth' => $current->month === $month->month,
                'tasks' => $dayTasks
            ];
            $current->addDay();
        }
        
        // Compute monthly metrics (only for tasks target-dated within the current month itself)
        $tasksInMonth = $tasksInGrid->filter(function($task) use ($startOfMonth, $endOfMonth) {
            $dateStr = $task->carry_over_date ?: $task->task_date;
            return $dateStr >= $startOfMonth->toDateString() && $dateStr <= $endOfMonth->toDateString();
        });
        
        $kpiTotal = $tasksInMonth->count();
        $kpiCompleted = $tasksInMonth->where('is_done', true)->count();
        $kpiCompletionRate = $kpiTotal > 0 ? round(($kpiCompleted / $kpiTotal) * 100) : 0;
        $kpiPending = $kpiTotal - $kpiCompleted;
        
        // Category breakdowns inside the month
        $kpiWork = $tasksInMonth->filter(function($t) { return strtolower($t->category) === 'work' || ($t->timeBlock && strtolower($t->timeBlock->block_type) === 'work'); })->count();
        $kpiStudy = $tasksInMonth->filter(function($t) { return strtolower($t->category) === 'study' || ($t->timeBlock && strtolower($t->timeBlock->block_type) === 'study'); })->count();
        $kpiReview = $tasksInMonth->filter(function($t) { return strtolower($t->category) === 'review' || ($t->timeBlock && strtolower($t->timeBlock->block_type) === 'review'); })->count();
        $kpiRoutine = $tasksInMonth->filter(function($t) { return strtolower($t->category) === 'routine' || ($t->timeBlock && strtolower($t->timeBlock->block_type) === 'routine'); })->count();
        
        // Fetch notes for the sidebar scratchpad
        $notes = Note::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        
        // Fetch monthly goals
        $monthlyGoals = MonthlyGoal::where('user_id', $user->id)
            ->where('month', $monthStr)
            ->orderBy('created_at', 'asc')
            ->get();
        
        return view('monthly-tasks', compact(
            'user', 
            'month', 
            'days', 
            'blocks', 
            'notes', 
            'monthlyGoals',
            'kpiTotal', 
            'kpiCompleted', 
            'kpiCompletionRate', 
            'kpiPending',
            'kpiWork',
            'kpiStudy',
            'kpiReview',
            'kpiRoutine'
        ));
    }
}
