<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'workout_plan_id', 'exercise_id', 'date'])]
class ExerciseLog extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workoutPlan()
    {
        return $this->belongsTo(WorkoutPlan::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
}
