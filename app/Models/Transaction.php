<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'type', 'amount', 'category', 'description', 'date', 'notion_id'])]
class Transaction extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
