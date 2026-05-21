<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
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
use Illuminate\Support\Facades\Log;

class NotionSyncService
{
    protected string $baseUrl = 'https://api.notion.com/v1';

    protected function client()
    {
        return Http::withToken(config('services.notion.token'))
            ->withHeaders(['Notion-Version' => '2022-06-28']);
    }

    protected function syncEntity($entity, $databaseConfigKey, $properties)
    {
        $databaseId = config("services.notion.databases.{$databaseConfigKey}");
        if (!config('services.notion.token') || !$databaseId) {
            return null;
        }

        if ($entity->notion_id) {
            $response = $this->client()->patch("{$this->baseUrl}/pages/{$entity->notion_id}", [
                'properties' => $properties
            ]);
        } else {
            $response = $this->client()->post("{$this->baseUrl}/pages", [
                'parent' => ['database_id' => $databaseId],
                'properties' => $properties
            ]);

            if ($response->successful()) {
                $entity->notion_id = $response->json('id');
                $entity->saveQuietly();
            }
        }

        if ($response->failed()) {
            Log::error("Notion {$databaseConfigKey} Sync Failed: " . $response->body());
        }

        return $response;
    }

    public function deleteEntity($entity)
    {
        if (!config('services.notion.token') || !$entity->notion_id) {
            return null;
        }

        $response = $this->client()->patch("{$this->baseUrl}/pages/{$entity->notion_id}", [
            'archived' => true
        ]);

        if ($response->failed()) {
            Log::error("Notion Entity Deletion Failed: " . $response->body());
        }

        return $response;
    }

    public function syncTaskToNotion(Task $task)
    {
        $properties = [
            'Name' => ['title' => [['text' => ['content' => $task->title]]]],
            'Is Completed' => ['checkbox' => $task->is_done]
        ];

        if ($task->category) $properties['Category'] = ['select' => ['name' => $task->category]];
        if ($task->status) $properties['Status'] = ['select' => ['name' => $task->status]];
        if ($task->carry_over_date) $properties['Carry Over Date'] = ['date' => ['start' => $task->carry_over_date]];
        if ($task->review_note) $properties['Review Note'] = ['rich_text' => [['text' => ['content' => $task->review_note]]]];

        if ($task->timeBlock && $task->timeBlock->notion_id) {
            $properties['Time Block'] = ['relation' => [['id' => $task->timeBlock->notion_id]]];
        }

        $this->syncEntity($task, 'tasks', $properties);
    }

    public function syncTimeBlockToNotion(TimeBlock $block)
    {
        $today = now()->toDateString();
        $startTime = "{$today}T{$block->starts_at}";
        $endTime = "{$today}T{$block->ends_at}";

        $properties = [
            'Title' => ['title' => [['text' => ['content' => $block->title]]]],
            'Block Type' => ['select' => ['name' => $block->block_type]],
            'Start Time' => ['date' => ['start' => $startTime]],
            'End Time' => ['date' => ['start' => $endTime]],
            'Sort Order' => ['number' => $block->sort_order]
        ];

        if ($block->notes) {
            $properties['Notes'] = ['rich_text' => [['text' => ['content' => $block->notes]]]];
        }

        $this->syncEntity($block, 'time_blocks', $properties);
    }

    public function syncDailyReviewToNotion(DailyReview $review)
    {
        $properties = [
            'Review Date' => ['title' => [['text' => ['content' => \Carbon\Carbon::parse($review->review_date)->toDateString()]]]]
        ];

        if ($review->focus_score !== null) $properties['Focus Score'] = ['number' => (int) $review->focus_score];
        if ($review->summary) $properties['Summary'] = ['rich_text' => [['text' => ['content' => $review->summary]]]];

        $this->syncEntity($review, 'daily_reviews', $properties);
    }

    public function syncTransaction(Transaction $tx)
    {
        $properties = [
            'Description' => ['title' => [['text' => ['content' => $tx->description ?? 'Transaction']]]],
            'Type' => ['select' => ['name' => $tx->type]],
            'Amount' => ['number' => (float) $tx->amount],
            'Date' => ['date' => ['start' => \Carbon\Carbon::parse($tx->date)->toDateString()]]
        ];

        if ($tx->category) {
            $properties['Category'] = ['select' => ['name' => $tx->category]];
        }

        $this->syncEntity($tx, 'transactions', $properties);
    }

    public function syncWishlist(WishlistItem $item)
    {
        $properties = [
            'Title' => ['title' => [['text' => ['content' => $item->title]]]],
            'Price' => ['number' => (float) $item->price],
            'Is Bought' => ['checkbox' => (bool) $item->is_bought]
        ];

        if ($item->priority) {
            $properties['Priority'] = ['select' => ['name' => $item->priority]];
        }

        $this->syncEntity($item, 'wishlist', $properties);
    }

    public function syncSubscription(Subscription $sub)
    {
        $properties = [
            'Title' => ['title' => [['text' => ['content' => $sub->title]]]],
            'Amount' => ['number' => (float) $sub->amount]
        ];

        if ($sub->billing_cycle) {
            $properties['Billing Cycle'] = ['select' => ['name' => $sub->billing_cycle]];
        }
        if ($sub->next_billing_date) {
            $properties['Next Billing Date'] = ['date' => ['start' => \Carbon\Carbon::parse($sub->next_billing_date)->toDateString()]];
        }

        $this->syncEntity($sub, 'subscriptions', $properties);
    }

    public function syncHabit(Habit $habit)
    {
        $properties = [
            'Title' => ['title' => [['text' => ['content' => $habit->title]]]],
            'Target' => ['number' => (int) $habit->target]
        ];

        if ($habit->timeBlock && $habit->timeBlock->notion_id) {
            $properties['Time Block'] = ['relation' => [['id' => $habit->timeBlock->notion_id]]];
        }

        $this->syncEntity($habit, 'habits', $properties);
    }

    public function syncWorkoutPlan(WorkoutPlan $plan)
    {
        $properties = [
            'Title' => ['title' => [['text' => ['content' => $plan->title]]]],
        ];

        if ($plan->day_of_week) {
            $properties['Day of Week'] = ['select' => ['name' => $plan->day_of_week]];
        }

        $this->syncEntity($plan, 'workout_plans', $properties);
    }

    public function syncExercise(Exercise $exercise)
    {
        $properties = [
            'Title' => ['title' => [['text' => ['content' => $exercise->title]]]],
        ];

        if ($exercise->muscle_group) {
            $properties['Muscle Group'] = ['select' => ['name' => $exercise->muscle_group]];
        }
        if ($exercise->equipment) {
            $properties['Equipment'] = ['select' => ['name' => $exercise->equipment]];
        }

        $this->syncEntity($exercise, 'exercises', $properties);
    }

    public function syncCredential(Credential $cred)
    {
        $properties = [
            'Platform' => ['title' => [['text' => ['content' => $cred->platform]]]],
            'Username' => ['rich_text' => [['text' => ['content' => $cred->username ?? '']]]],
        ];

        if ($cred->url) {
            $properties['URL'] = ['url' => $cred->url];
        }
        if ($cred->notes) {
            $properties['Notes'] = ['rich_text' => [['text' => ['content' => $cred->notes]]]];
        }

        $this->syncEntity($cred, 'credentials', $properties);
    }

    public function syncNote(Note $note)
    {
        $properties = [
            'Content' => ['title' => [['text' => ['content' => substr($note->content, 0, 100)]]]],
        ];

        $properties['Created Date'] = ['date' => ['start' => $note->created_at->toDateString()]];

        $this->syncEntity($note, 'notes', $properties);
    }

    public function syncEvent(Event $event)
    {
        $properties = [
            'Name' => ['title' => [['text' => ['content' => $event->title]]]],
            'Event Date' => ['date' => ['start' => $event->event_date->toDateString()]]
        ];

        if ($event->description) {
            $properties['Description'] = ['rich_text' => [['text' => ['content' => $event->description]]]];
        }

        if ($event->type) {
            $properties['Type'] = ['select' => ['name' => $event->type]];
        }

        $this->syncEntity($event, 'events', $properties);
    }
}
