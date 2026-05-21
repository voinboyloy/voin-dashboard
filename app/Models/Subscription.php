<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'title', 'amount', 'billing_cycle', 'next_billing_date', 'notion_id'])]
class Subscription extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
