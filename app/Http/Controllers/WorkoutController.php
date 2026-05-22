<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\ExerciseLog;
use App\Models\WorkoutPlan;
use Illuminate\Http\Request;

class WorkoutController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $plans = WorkoutPlan::with('exercises')->where('user_id', $user->id)->get();
        $allExercises = Exercise::all()->groupBy('muscle_group');

        return view('workout-planner', compact('user', 'plans', 'allExercises'));
    }

    public function addExercise(Request $request)
    {
        $plan = WorkoutPlan::findOrFail($request->workout_plan_id);
        $plan->exercises()->attach($request->exercise_id, [
            'sets' => $request->sets,
            'reps' => $request->reps,
        ]);
        return response()->json(['message' => 'Exercise added to plan']);
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
