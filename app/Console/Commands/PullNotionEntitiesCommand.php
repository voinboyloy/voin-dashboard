<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotionPullService;
use App\Models\Task;
use App\Models\TimeBlock;
use App\Models\DailyReview;
use App\Models\Transaction;
use App\Models\WishlistItem;
use App\Models\Subscription;
use App\Models\Habit;
use App\Models\WorkoutPlan;
use App\Models\Exercise;
use App\Models\Credential;
use App\Models\Note;

class PullNotionEntitiesCommand extends Command
{
    protected $signature = 'notion:pull';
    protected $description = 'Pull changes from Notion into the local database (Two-Way Sync)';

    public function handle(NotionPullService $pullService)
    {
        $this->info('Starting Notion pull sync...');

        // Track current IDs to detect deletions
        $beforeSync = [
            'tasks' => Task::whereNotNull('notion_id')->pluck('notion_id')->toArray(),
            'time_blocks' => TimeBlock::whereNotNull('notion_id')->pluck('notion_id')->toArray(),
            'daily_reviews' => DailyReview::whereNotNull('notion_id')->pluck('notion_id')->toArray(),
            'transactions' => Transaction::whereNotNull('notion_id')->pluck('notion_id')->toArray(),
            'wishlist' => WishlistItem::whereNotNull('notion_id')->pluck('notion_id')->toArray(),
            'subscriptions' => Subscription::whereNotNull('notion_id')->pluck('notion_id')->toArray(),
            'habits' => Habit::whereNotNull('notion_id')->pluck('notion_id')->toArray(),
            'workout_plans' => WorkoutPlan::whereNotNull('notion_id')->pluck('notion_id')->toArray(),
            'exercises' => Exercise::whereNotNull('notion_id')->pluck('notion_id')->toArray(),
            'credentials' => Credential::whereNotNull('notion_id')->pluck('notion_id')->toArray(),
            'notes' => Note::whereNotNull('notion_id')->pluck('notion_id')->toArray(),
        ];

        // Pull creates and updates. Returns arrays of active Notion IDs.
        $activeIds = $pullService->pullAll();

        if (empty($activeIds)) {
            $this->warn('No user found to pull for, or Notion config is missing.');
            return;
        }

        // Detect deletions: if it was in our DB before the sync, but wasn't returned by Notion API (it's archived), delete it locally.
        $this->cleanup(Task::class, $beforeSync['tasks'], $activeIds['tasks'] ?? []);
        $this->cleanup(TimeBlock::class, $beforeSync['time_blocks'], $activeIds['time_blocks'] ?? []);
        $this->cleanup(DailyReview::class, $beforeSync['daily_reviews'], $activeIds['daily_reviews'] ?? []);
        $this->cleanup(Transaction::class, $beforeSync['transactions'], $activeIds['transactions'] ?? []);
        $this->cleanup(WishlistItem::class, $beforeSync['wishlist'], $activeIds['wishlist'] ?? []);
        $this->cleanup(Subscription::class, $beforeSync['subscriptions'], $activeIds['subscriptions'] ?? []);
        $this->cleanup(Habit::class, $beforeSync['habits'], $activeIds['habits'] ?? []);
        $this->cleanup(WorkoutPlan::class, $beforeSync['workout_plans'], $activeIds['workout_plans'] ?? []);
        $this->cleanup(Exercise::class, $beforeSync['exercises'], $activeIds['exercises'] ?? []);
        $this->cleanup(Credential::class, $beforeSync['credentials'], $activeIds['credentials'] ?? []);
        $this->cleanup(Note::class, $beforeSync['notes'], $activeIds['notes'] ?? []);

        $this->info('Pull sync complete!');
    }

    protected function cleanup($modelClass, $beforeIds, $activeIds)
    {
        $deletedIds = array_diff($beforeIds, $activeIds);
        if (!empty($deletedIds)) {
            // Delete them quietly so we don't trigger our local observers/delete syncs back to Notion
            $modelClass::whereIn('notion_id', $deletedIds)->delete();
        }
    }
}
