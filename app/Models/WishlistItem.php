<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'title', 'price', 'priority', 'is_bought'])]
class WishlistItem extends Model
{
    protected $casts = [
        'is_bought' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
