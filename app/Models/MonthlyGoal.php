<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyGoal extends Model
{
    protected $fillable = ['user_id', 'month', 'title', 'is_completed'];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
