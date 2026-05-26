<?php

namespace App\Http\Controllers;

use App\Models\MonthlyGoal;
use Illuminate\Http\Request;

class MonthlyGoalController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        $goal = MonthlyGoal::create([
            'user_id' => $user->id,
            'month' => $request->month,
            'title' => $request->title,
            'is_completed' => false,
        ]);

        return response()->json($goal);
    }

    public function toggle(MonthlyGoal $goal)
    {
        $goal->update(['is_completed' => !$goal->is_completed]);
        return response()->json($goal);
    }

    public function destroy(MonthlyGoal $goal)
    {
        $goal->delete();
        return response()->json(['message' => 'Goal deleted']);
    }
}
