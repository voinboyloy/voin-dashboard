<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Habit;
use App\Models\HabitCompletion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HabitStreakTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculate_streak_uses_loaded_completions()
    {
        $user = User::factory()->create();
        $habit = Habit::create([
            'user_id' => $user->id,
            'title' => 'Test Habit',
            'target' => 'Daily',
        ]);

        // Create a 5-day streak
        for ($i = 0; $i < 5; $i++) {
            HabitCompletion::create([
                'habit_id' => $habit->id,
                'date' => now()->subDays($i)->toDateString(),
            ]);
        }

        // Test without loading relation
        $this->assertEquals(5, $habit->calculateStreak());

        // Test with loading relation
        $habit->load('completions');
        $this->assertEquals(5, $habit->calculateStreak());

        // Ensure it works if today is missing (streak should be 0 or 1 depending on logic,
        // current logic counts backwards from today and requires today to be present for streak > 0)

        $habit2 = Habit::create([
            'user_id' => $user->id,
            'title' => 'Test Habit 2',
        ]);
        HabitCompletion::create([
            'habit_id' => $habit2->id,
            'date' => now()->subDay()->toDateString(),
        ]);

        $this->assertEquals(0, $habit2->calculateStreak());
        $habit2->load('completions');
        $this->assertEquals(0, $habit2->calculateStreak());
    }

    public function test_completed_today_uses_loaded_completions()
    {
        $user = User::factory()->create();
        $habit = Habit::create([
            'user_id' => $user->id,
            'title' => 'Test Habit',
        ]);

        $this->assertFalse($habit->completedToday());

        HabitCompletion::create([
            'habit_id' => $habit->id,
            'date' => now()->toDateString(),
        ]);

        $this->assertTrue($habit->completedToday());

        $habit->load('completions');
        $this->assertTrue($habit->completedToday());
    }
}
