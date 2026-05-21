<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'monthly_budget'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function timeBlocks()
    {
        return $this->hasMany(TimeBlock::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function dailyReviews()
    {
        return $this->hasMany(DailyReview::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function workoutPlans()
    {
        return $this->hasMany(WorkoutPlan::class);
    }

    public function habits()
    {
        return $this->hasMany(Habit::class);
    }

    public function exerciseLogs()
    {
        return $this->hasMany(ExerciseLog::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function credentials()
    {
        return $this->hasMany(Credential::class);
    }
}
