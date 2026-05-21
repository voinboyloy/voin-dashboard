<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'time_block_id', 'title', 'target'])]
class Habit extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function timeBlock()
    {
        return $this->belongsTo(TimeBlock::class);
    }

    public function completions()
    {
        return $this->hasMany(HabitCompletion::class);
    }

    public function completedToday()
    {
        return $this->completions()->whereDate('date', now()->toDateString())->exists();
    }

    public function calculateStreak()
    {
        $count = 0;
        $date = now();
        while ($this->completions()->whereDate('date', $date->toDateString())->exists()) {
            $count++;
            $date = $date->copy()->subDay();
        }
        return $count;
    }
}
