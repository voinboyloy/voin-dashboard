<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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
use App\Models\Event;
use App\Services\NotionSyncService;

class SyncNotionEntitiesCommand extends Command
{
    protected $signature = 'notion:sync-all';
    protected $description = 'Sync all unsynced entities to Notion';

    public function handle(NotionSyncService $syncService)
    {
        $this->info('Starting sync...');

        // Exercises
        foreach (Exercise::whereNull('notion_id')->get() as $item) {
            $syncService->syncExercise($item);
        }

        // Time Blocks
        foreach (TimeBlock::whereNull('notion_id')->get() as $item) {
            $syncService->syncTimeBlockToNotion($item);
        }

        // Tasks
        foreach (Task::whereNull('notion_id')->get() as $item) {
            $syncService->syncTaskToNotion($item);
        }

        // Daily Reviews
        foreach (DailyReview::whereNull('notion_id')->get() as $item) {
            $syncService->syncDailyReviewToNotion($item);
        }

        // Transactions
        foreach (Transaction::whereNull('notion_id')->get() as $item) {
            $syncService->syncTransaction($item);
        }

        // Wishlist Items
        foreach (WishlistItem::whereNull('notion_id')->get() as $item) {
            $syncService->syncWishlist($item);
        }

        // Subscriptions
        foreach (Subscription::whereNull('notion_id')->get() as $item) {
            $syncService->syncSubscription($item);
        }

        // Habits
        foreach (Habit::whereNull('notion_id')->get() as $item) {
            $syncService->syncHabit($item);
        }

        // Workout Plans
        foreach (WorkoutPlan::whereNull('notion_id')->get() as $item) {
            $syncService->syncWorkoutPlan($item);
        }

        // Credentials
        foreach (Credential::whereNull('notion_id')->get() as $item) {
            $syncService->syncCredential($item);
        }

        // Notes
        foreach (Note::whereNull('notion_id')->get() as $item) {
            $syncService->syncNote($item);
        }

        // Events
        foreach (Event::whereNull('notion_id')->get() as $item) {
            $syncService->syncEvent($item);
        }

        $this->info('Sync complete!');
    }
}
