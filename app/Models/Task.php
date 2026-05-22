<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'time_block_id', 'title', 'category', 'status', 'is_done', 'carry_over_date', 'review_note', 'notion_id', 'task_date'])]
class Task extends Model
{
    protected $casts = [
        'is_done' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function timeBlock()
    {
        return $this->belongsTo(TimeBlock::class);
    }
}
