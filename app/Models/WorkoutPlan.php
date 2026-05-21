<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'title', 'day_of_week', 'notion_id'])]
class WorkoutPlan extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exercises()
    {
        return $this->belongsToMany(Exercise::class, 'plan_exercise')
                    ->withPivot(['sets', 'reps'])
                    ->withTimestamps();
    }
}
