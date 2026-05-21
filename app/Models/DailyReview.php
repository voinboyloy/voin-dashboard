<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'review_date', 'focus_score', 'summary', 'notion_id', 'daily_focus'])]
class DailyReview extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
