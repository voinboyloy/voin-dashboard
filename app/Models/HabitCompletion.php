<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['habit_id', 'date'])]
class HabitCompletion extends Model
{
    public function habit()
    {
        return $this->belongsTo(Habit::class);
    }
}
