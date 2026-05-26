<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JulesSession extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'prompt',
        'repo_path',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
