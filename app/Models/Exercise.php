<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'muscle_group', 'equipment', 'description'])]
class Exercise extends Model
{
    public function workoutPlans()
    {
        return $this->belongsToMany(WorkoutPlan::class, 'plan_exercise')
                    ->withPivot(['sets', 'reps'])
                    ->withTimestamps();
    }
}
