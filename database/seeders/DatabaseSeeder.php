<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Ensure Exercises exist first (Global library)
        if (\App\Models\Exercise::count() === 0) {
            $exerciseData = [
                // Push
                ['title' => 'Dumbbell Floor Press', 'muscle_group' => 'Chest', 'equipment' => 'Dumbbells'],
                ['title' => 'Dumbbell Overhead Press', 'muscle_group' => 'Shoulders', 'equipment' => 'Dumbbells'],
                ['title' => 'Incline Floor Press', 'muscle_group' => 'Chest', 'equipment' => 'Dumbbells'],
                ['title' => 'Lateral Raises', 'muscle_group' => 'Shoulders', 'equipment' => 'Dumbbells'],
                ['title' => 'Overhead Triceps Extension', 'muscle_group' => 'Triceps', 'equipment' => 'Dumbbells'],
                ['title' => 'Push-ups', 'muscle_group' => 'Chest', 'equipment' => 'Bodyweight'],
                // Lower
                ['title' => 'Goblet Squat', 'muscle_group' => 'Legs', 'equipment' => 'Dumbbells'],
                ['title' => 'Romanian Deadlift', 'muscle_group' => 'Legs', 'equipment' => 'Dumbbells'],
                ['title' => 'Reverse Lunge', 'muscle_group' => 'Legs', 'equipment' => 'Dumbbells'],
                ['title' => 'Hip Thrust', 'muscle_group' => 'Glutes', 'equipment' => 'Dumbbells'],
                ['title' => 'Sumo Squat', 'muscle_group' => 'Legs', 'equipment' => 'Dumbbells'],
                ['title' => 'Calf Raise', 'muscle_group' => 'Calves', 'equipment' => 'Dumbbells'],
                // Pull
                ['title' => 'One-arm Row', 'muscle_group' => 'Back', 'equipment' => 'Dumbbells'],
                ['title' => 'Bent-over Row', 'muscle_group' => 'Back', 'equipment' => 'Dumbbells'],
                ['title' => 'Reverse Fly', 'muscle_group' => 'Back', 'equipment' => 'Dumbbells'],
                ['title' => 'Bicep Curl', 'muscle_group' => 'Arms', 'equipment' => 'Dumbbells'],
                ['title' => 'Hammer Curl', 'muscle_group' => 'Arms', 'equipment' => 'Dumbbells'],
                ['title' => 'Dumbbell Pullover', 'muscle_group' => 'Back', 'equipment' => 'Dumbbells'],
                // Core/Cond
                ['title' => 'Russian Twist', 'muscle_group' => 'Core', 'equipment' => 'Dumbbells'],
                ['title' => 'Dead Bug', 'muscle_group' => 'Core', 'equipment' => 'Dumbbells'],
                ['title' => 'Side Plank', 'muscle_group' => 'Core', 'equipment' => 'Bodyweight'],
                ['title' => 'Thruster', 'muscle_group' => 'Full Body', 'equipment' => 'Dumbbells'],
                ['title' => 'Dumbbell Swing', 'muscle_group' => 'Full Body', 'equipment' => 'Dumbbells'],
                ['title' => 'Mountain Climbers', 'muscle_group' => 'Core', 'equipment' => 'Bodyweight'],
                // Cardio
                ['title' => 'Brisk Walking', 'muscle_group' => 'Cardio', 'equipment' => 'Bodyweight'],
                ['title' => 'Shadow Boxing', 'muscle_group' => 'Cardio', 'equipment' => 'Bodyweight'],
                ['title' => 'Mobility Flow', 'muscle_group' => 'Mobility', 'equipment' => 'Bodyweight'],
            ];
            foreach ($exerciseData as $ex) {
                \App\Models\Exercise::create($ex);
            }
        }

        // 2. Define users to seed
        $userDefinitions = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'password'
            ],
            [
                'name' => 'pagnavoin',
                'email' => 'pagnavoin@voin.me',
                'password' => 'Pagnavoin069.$'
            ]
        ];

        foreach ($userDefinitions as $def) {
            $user = User::updateOrCreate(
                ['email' => $def['email']],
                ['name' => $def['name'], 'password' => bcrypt($def['password'])]
            );

            // Skip data seeding if the user already has content (idempotency)
            if ($user->timeBlocks()->exists()) continue;

            // Seed Time Blocks
            $blocks = [
                ['title' => 'Morning Setup', 'block_type' => 'routine', 'starts_at' => '08:00', 'ends_at' => '09:00'],
                ['title' => 'Core Work Block', 'block_type' => 'work', 'starts_at' => '09:00', 'ends_at' => '12:00'],
                ['title' => 'Afternoon Work Block', 'block_type' => 'work', 'starts_at' => '13:00', 'ends_at' => '17:00'],
                ['title' => 'Evening Study Sprint', 'block_type' => 'study', 'starts_at' => '19:00', 'ends_at' => '21:00'],
                ['title' => 'Daily Review', 'block_type' => 'review', 'starts_at' => '21:00', 'ends_at' => '22:00'],
            ];

            foreach ($blocks as $block) {
                $user->timeBlocks()->create($block);
            }

            // Seed Tasks for specific blocks
            $coreWork = $user->timeBlocks()->where('title', 'Core Work Block')->first();
            if ($coreWork) {
                $coreWork->tasks()->createMany([
                    ['user_id' => $user->id, 'title' => 'Ship API fix', 'category' => 'work', 'is_done' => true],
                    ['user_id' => $user->id, 'title' => 'Review PR', 'category' => 'work', 'is_done' => false],
                ]);
            }

            $studySprint = $user->timeBlocks()->where('title', 'Evening Study Sprint')->first();
            if ($studySprint) {
                $studySprint->tasks()->createMany([
                    ['user_id' => $user->id, 'title' => 'Complete Laravel lesson', 'category' => 'study', 'is_done' => false],
                ]);
            }

            // Workout Plans & Schedule
            $schedule = [
                ['day' => 'Monday', 'title' => 'Upper Body Push', 'exercises' => [
                    'Dumbbell Floor Press' => [3, 10], 'Dumbbell Overhead Press' => [3, 10], 
                    'Incline Floor Press' => [3, 10], 'Lateral Raises' => [3, 12], 
                    'Overhead Triceps Extension' => [3, 12], 'Push-ups' => [2, 15]
                ]],
                ['day' => 'Tuesday', 'title' => 'Lower Body', 'exercises' => [
                    'Goblet Squat' => [3, 10], 'Romanian Deadlift' => [3, 10], 
                    'Reverse Lunge' => [3, 10], 'Hip Thrust' => [3, 12], 
                    'Sumo Squat' => [3, 12], 'Calf Raise' => [3, 15]
                ]],
                ['day' => 'Wednesday', 'title' => 'Rest or Light Cardio', 'exercises' => [
                    'Brisk Walking' => [1, 30], 'Shadow Boxing' => [1, 15], 'Mobility Flow' => [1, 10]
                ]],
                ['day' => 'Thursday', 'title' => 'Upper Body Pull', 'exercises' => [
                    'One-arm Row' => [3, 10], 'Bent-over Row' => [3, 10], 
                    'Reverse Fly' => [3, 12], 'Bicep Curl' => [3, 12], 
                    'Hammer Curl' => [3, 12], 'Dumbbell Pullover' => [3, 10]
                ]],
                ['day' => 'Friday', 'title' => 'Full Body Circuit', 'exercises' => [
                    'Goblet Squat' => [3, 12], 'Dumbbell Floor Press' => [3, 12], 
                    'One-arm Row' => [3, 10], 'Romanian Deadlift' => [3, 12], 
                    'Dumbbell Overhead Press' => [3, 10]
                ]],
                ['day' => 'Saturday', 'title' => 'Core and Conditioning', 'exercises' => [
                    'Russian Twist' => [3, 15], 'Dead Bug' => [3, 10], 
                    'Side Plank' => [3, 30], 'Thruster' => [3, 12], 
                    'Dumbbell Swing' => [3, 15], 'Mountain Climbers' => [3, 40]
                ]],
                ['day' => 'Sunday', 'title' => 'Rest', 'exercises' => [
                    'Mobility Flow' => [1, 15]
                ]],
            ];

            foreach ($schedule as $s) {
                $plan = $user->workoutPlans()->create([
                    'title' => $s['title'],
                    'day_of_week' => $s['day'],
                ]);
                foreach ($s['exercises'] as $exTitle => $meta) {
                    $ex = \App\Models\Exercise::where('title', $exTitle)->first();
                    if ($ex) {
                        $plan->exercises()->attach($ex->id, ['sets' => $meta[0], 'reps' => $meta[1]]);
                    }
                }
            }

            // Transactions
            $user->transactions()->createMany([
                ['type' => 'income', 'amount' => 1212, 'category' => 'Salary', 'date' => now(), 'description' => 'Monthly Pay'],
                ['type' => 'expense', 'amount' => 4, 'category' => 'Groceries', 'date' => now(), 'description' => 'Weekly shop'],
            ]);

            // Wishlist
            $user->wishlistItems()->createMany([
                ['title' => 'Mechanical Keyboard', 'price' => 30, 'priority' => 'high'],
                ['title' => 'Noise Cancelling Headphones', 'price' => 30, 'priority' => 'medium'],
            ]);
        }
    }
}
