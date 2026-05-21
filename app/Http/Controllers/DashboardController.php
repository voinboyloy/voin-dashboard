<?php

namespace App\Http\Controllers;

use App\Models\Credential;
use App\Models\DailyReview;
use App\Models\Exercise;
use App\Models\ExerciseLog;
use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Models\Note;
use App\Models\Subscription;
use App\Models\Task;
use App\Models\TimeBlock;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WishlistItem;
use App\Models\WorkoutPlan;
use Illuminate\Http\Request;
use App\Services\NotionSyncService;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $blocks = TimeBlock::with('tasks')->where('user_id', $user->id)->orderBy('starts_at')->get();
        $tasks = Task::where('user_id', $user->id)->get();
        $review = DailyReview::where('user_id', $user->id)->whereDate('review_date', now()->toDateString())->first();
        $habits = Habit::with(['completions' => function($q) {
            $q->whereDate('date', now()->toDateString());
        }])->where('user_id', $user->id)->get();

        $todayName = now()->format('l');
        $workoutPlan = WorkoutPlan::with('exercises')->where('user_id', $user->id)->where('day_of_week', $todayName)->first();
        $exerciseLogs = ExerciseLog::where('user_id', $user->id)->whereDate('date', now()->toDateString())->pluck('exercise_id')->toArray();
        $allExercises = Exercise::all()->groupBy('muscle_group');
        
        $notes = Note::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        
        return view('dashboard', compact('user', 'blocks', 'tasks', 'review', 'habits', 'workoutPlan', 'exerciseLogs', 'allExercises', 'notes'));
    }

    public function loadSample()
    {
        // This is a simplified way to reset for the prototype
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        
        TimeBlock::truncate();
        Task::truncate();
        DailyReview::truncate();
        Transaction::truncate();
        WishlistItem::truncate();
        WorkoutPlan::truncate();
        Exercise::truncate();
        Credential::truncate();
        DB::table('plan_exercise')->truncate();

        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        Artisan::call('db:seed', ['--force' => true]);
        
        return response()->json(['message' => 'Sample day loaded']);
    }

    public function storeTask(Request $request)
    {
        $user = auth()->user();
        $task = Task::create([
            'user_id' => $user->id,
            'time_block_id' => $request->time_block_id,
            'title' => $request->title,
            'category' => $request->category,
            'is_done' => false,
        ]);

        app(NotionSyncService::class)->syncTaskToNotion($task);

        return response()->json($task);
    }

    public function toggleTask(Task $task)
    {
        $task->update(['is_done' => !$task->is_done]);

        app(NotionSyncService::class)->syncTaskToNotion($task);

        return response()->json($task);
    }

    public function updateTask(Request $request, Task $task)
    {
        $task->update([
            'title' => $request->title,
            'time_block_id' => $request->time_block_id,
            'category' => $request->category,
        ]);

        app(NotionSyncService::class)->syncTaskToNotion($task);

        return response()->json($task);
    }

    public function destroyTask(Task $task)
    {
        $task->delete();
        return response()->json(['message' => 'Task deleted']);
    }

    public function carryOverTask(Task $task)
    {
        $task->update([
            'carry_over_date' => now()->addDay()->toDateString(),
        ]);

        app(NotionSyncService::class)->syncTaskToNotion($task);

        return response()->json($task);
    }

    public function storeBlock(Request $request)
    {
        $user = auth()->user();
        $block = TimeBlock::updateOrCreate(
            ['id' => $request->id, 'user_id' => $user->id],
            [
                'title' => $request->title,
                'block_type' => $request->block_type,
                'starts_at' => $request->starts_at,
                'ends_at' => $request->ends_at,
                'notes' => $request->notes,
            ]
        );

        app(NotionSyncService::class)->syncTimeBlockToNotion($block);

        return response()->json($block);
    }

    public function destroyBlock(TimeBlock $block)
    {
        $block->delete();
        return response()->json(['message' => 'Block deleted']);
    }

    public function saveReview(Request $request)
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

    public function weeklyLoad()
    {
        $user = auth()->user();
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        
        $blocks = TimeBlock::where('user_id', $user->id)->get();
        $tasks = Task::where('user_id', $user->id)->get();
        
        return view('weekly-load', compact('user', 'blocks', 'tasks'));
    }

    public function reviewLog()
    {
        $user = auth()->user();
        $reviews = DailyReview::where('user_id', $user->id)->orderBy('review_date', 'desc')->get();
        
        return view('review-log', compact('user', 'reviews'));
    }

    public function savingsTracker()
    {
        $user = auth()->user();
        $transactions = Transaction::where('user_id', $user->id)->orderBy('date', 'desc')->get();
        $income = $transactions->where('type', 'income')->sum('amount');
        $expenses = $transactions->where('type', 'expense')->sum('amount');
        $savings = $income - $expenses;
        
        $wishlist = WishlistItem::where('user_id', $user->id)->orderBy('is_bought')->get();
        $subscriptions = $user->subscriptions;
        
        return view('savings-tracker', compact('user', 'transactions', 'income', 'expenses', 'savings', 'wishlist', 'subscriptions'));
    }

    public function workoutPlanner()
    {
        $user = auth()->user();
        $plans = WorkoutPlan::with('exercises')->where('user_id', $user->id)->get();
        $allExercises = Exercise::all()->groupBy('muscle_group');
        
        return view('workout-planner', compact('user', 'plans', 'allExercises'));
    }

    public function credentialsVault()
    {
        $user = auth()->user();
        $credentials = Credential::where('user_id', $user->id)->orderBy('platform')->get();
        
        return view('credentials-vault', compact('user', 'credentials'));
    }

    public function storeCredential(Request $request)
    {
        $user = auth()->user();
        $credential = Credential::updateOrCreate(
            ['id' => $request->id, 'user_id' => $user->id],
            [
                'platform' => $request->platform,
                'username' => $request->username,
                'password' => $request->password,
                'url' => $request->url,
                'notes' => $request->notes,
            ]
        );

        app(NotionSyncService::class)->syncCredential($credential);

        return response()->json($credential);
    }

    public function destroyCredential(Credential $credential)
    {
        $credential->delete();
        return response()->json(['message' => 'Credential deleted']);
    }

    public function addExerciseToPlan(Request $request)
    {
        $plan = WorkoutPlan::findOrFail($request->workout_plan_id);
        $plan->exercises()->attach($request->exercise_id, [
            'sets' => $request->sets,
            'reps' => $request->reps,
        ]);
        return response()->json(['message' => 'Exercise added to plan']);
    }

    public function storeTransaction(Request $request)
    {
        $user = auth()->user();
        $tx = Transaction::updateOrCreate(
            ['id' => $request->id, 'user_id' => $user->id],
            [
                'type' => $request->type,
                'amount' => $request->amount,
                'category' => $request->category,
                'description' => $request->description,
                'date' => $request->date ?? now()->toDateString(),
            ]
        );

        app(NotionSyncService::class)->syncTransaction($tx);

        return response()->json($tx);
    }

    public function destroyTransaction(Transaction $transaction)
    {
        $transaction->delete();
        return response()->json(['message' => 'Transaction deleted']);
    }

    public function storeWishlist(Request $request)
    {
        $user = auth()->user();
        $item = WishlistItem::updateOrCreate(
            ['id' => $request->id, 'user_id' => $user->id],
            [
                'title' => $request->title,
                'price' => $request->price,
                'priority' => $request->priority,
            ]
        );

        app(NotionSyncService::class)->syncWishlist($item);

        return response()->json($item);
    }

    public function toggleWishlist(WishlistItem $item)
    {
        $item->update(['is_bought' => !$item->is_bought]);

        app(NotionSyncService::class)->syncWishlist($item);

        return response()->json($item);
    }

    public function destroyWishlist(WishlistItem $item)
    {
        $item->delete();
        return response()->json(['message' => 'Item deleted']);
    }

    public function storeHabit(Request $request)
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

    public function toggleHabit(Habit $habit)
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

    public function destroyHabit(Habit $habit)
    {
        $habit->delete();
        return response()->json(['message' => 'Habit deleted']);
    }

    public function storeNote(Request $request)
    {
        $user = auth()->user();
        $note = Note::create([
            'user_id' => $user->id,
            'content' => $request->content,
        ]);

        app(NotionSyncService::class)->syncNote($note);

        return response()->json($note);
    }

    public function destroyNote(Note $note)
    {
        $note->delete();
        return response()->json(['message' => 'Note deleted']);
    }

    public function updateBudget(Request $request)
    {
        $user = auth()->user();
        $user->update(['monthly_budget' => $request->monthly_budget]);
        return response()->json(['message' => 'Budget updated']);
    }

    public function storeSubscription(Request $request)
    {
        $user = auth()->user();
        $sub = Subscription::updateOrCreate(
            ['id' => $request->id, 'user_id' => $user->id],
            [
                'title' => $request->title,
                'amount' => $request->amount,
                'billing_cycle' => $request->billing_cycle,
                'next_billing_date' => $request->next_billing_date,
            ]
        );

        app(NotionSyncService::class)->syncSubscription($sub);

        return response()->json($sub);
    }

    public function destroySubscription(Subscription $subscription)
    {
        $subscription->delete();
        return response()->json(['message' => 'Subscription deleted']);
    }

    public function getAnalyticsData()
    {
        $user = auth()->user();

        // Savings Chart Data
        $txs = Transaction::where('user_id', $user->id)->orderBy('date')->get();
        $savingsLabels = $txs->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))->toArray();
        $incomeData = $txs->where('type', 'income')->pluck('amount')->toArray();
        $expenseData = $txs->where('type', 'expense')->pluck('amount')->toArray();

        // Habit Streaks Data
        $habits = Habit::with('completions')->where('user_id', $user->id)->get();
        $streakData = $habits->map(fn($h) => [
            'name' => $h->title,
            'streak' => $this->calculateStreak($h)
        ]);

        return response()->json([
            'savings' => ['labels' => $savingsLabels, 'income' => $incomeData, 'expenses' => $expenseData],
            'streaks' => $streakData
        ]);
    }

    private function calculateStreak($habit)
    {
        $count = 0;
        $date = now();
        // Simple logic: count backwards from today
        while ($habit->completions()->whereDate('date', $date->toDateString())->exists()) {
            $count++;
            $date = $date->copy()->subDay();
        }
        return $count;
    }

    public function toggleExercise(Request $request)
    {
        $user = auth()->user();
        $today = now()->toDateString();
        
        $log = ExerciseLog::where('user_id', $user->id)
            ->where('workout_plan_id', $request->workout_plan_id)
            ->where('exercise_id', $request->exercise_id)
            ->whereDate('date', $today)
            ->first();

        if ($log) {
            $log->delete();
            return response()->json(['status' => 'removed']);
        } else {
            ExerciseLog::create([
                'user_id' => $user->id,
                'workout_plan_id' => $request->workout_plan_id,
                'exercise_id' => $request->exercise_id,
                'date' => $today
            ]);
            return response()->json(['status' => 'added']);
        }
    }
}
