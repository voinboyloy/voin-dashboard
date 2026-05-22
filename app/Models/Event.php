<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['user_id', 'title', 'event_date', 'start_time', 'end_time', 'description', 'type', 'notion_id'])]
class Event extends Model
{
    protected $casts = [
        'event_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
