<?php

namespace App\Http\Controllers;

use App\Models\DailyReview;
use App\Models\Exercise;
use App\Models\ExerciseLog;
use App\Models\Habit;
use App\Models\Note;
use App\Models\Task;
use App\Models\TimeBlock;
use App\Models\WorkoutPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $todayStr = now()->toDateString();
        
        $blocks = TimeBlock::with(['tasks' => function($query) use ($todayStr) {
            $query->where(function($q) use ($todayStr) {
                $q->whereNull('carry_over_date')
                  ->where(function($sq) use ($todayStr) {
                      $sq->whereNull('task_date')
                         ->orWhere('task_date', $todayStr);
                  });
            })
            ->orWhere('carry_over_date', $todayStr);
        }])->where('user_id', $user->id)->orderBy('starts_at')->get();

        $tasks = Task::where('user_id', $user->id)
            ->where(function($query) use ($todayStr) {
                $query->where(function($q) use ($todayStr) {
                    $q->whereNull('carry_over_date')
                      ->where(function($sq) use ($todayStr) {
                          $sq->whereNull('task_date')
                             ->orWhere('task_date', $todayStr);
                      });
                })
                ->orWhere('carry_over_date', $todayStr)
                ->orWhere('carry_over_date', '>', $todayStr);
            })
            ->get();

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
        \App\Models\Transaction::truncate();
        \App\Models\WishlistItem::truncate();
        WorkoutPlan::truncate();
        Exercise::truncate();
        \App\Models\Credential::truncate();
        DB::table('plan_exercise')->truncate();

        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        Artisan::call('db:seed', ['--force' => true]);
        
        return response()->json(['message' => 'Sample day loaded']);
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
}
