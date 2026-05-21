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
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotionPullService
{
    protected string $baseUrl = 'https://api.notion.com/v1';

    protected function client()
    {
        return Http::withToken(config('services.notion.token'))
            ->withHeaders(['Notion-Version' => '2022-06-28']);
    }

    public function pullAll()
    {
        // Default to the first user for incoming items, since the Notion token is app-wide
        $user = User::first();
        if (!$user) return [];

        return [
            'tasks' => $this->pullTasks($user->id),
            'time_blocks' => $this->pullTimeBlocks($user->id),
            'daily_reviews' => $this->pullDailyReviews($user->id),
            'transactions' => $this->pullTransactions($user->id),
            'wishlist' => $this->pullWishlistItems($user->id),
            'subscriptions' => $this->pullSubscriptions($user->id),
            'habits' => $this->pullHabits($user->id),
            'workout_plans' => $this->pullWorkoutPlans($user->id),
            'exercises' => $this->pullExercises(),
            'credentials' => $this->pullCredentials($user->id),
            'notes' => $this->pullNotes($user->id),
        ];
    }

    protected function queryDatabase($configKey)
    {
        $dbId = config("services.notion.databases.{$configKey}");
        if (!$dbId) return [];

        $results = [];
        $hasMore = true;
        $cursor = null;

        while ($hasMore) {
            $payload = [];
            if ($cursor) $payload['start_cursor'] = $cursor;

            $response = $this->client()->post("{$this->baseUrl}/databases/{$dbId}/query", $payload);

            if ($response->successful()) {
                $data = $response->json();
                $results = array_merge($results, $data['results'] ?? []);
                $hasMore = $data['has_more'] ?? false;
                $cursor = $data['next_cursor'] ?? null;
            } else {
                Log::error("Failed to pull Notion DB {$configKey}: " . $response->body());
                $hasMore = false;
            }
        }

        return $results;
    }

    // --- Parsers ---

    protected function getTitle($properties, $key)
    {
        return $properties[$key]['title'][0]['text']['content'] ?? null;
    }

    protected function getRichText($properties, $key)
    {
        return $properties[$key]['rich_text'][0]['text']['content'] ?? null;
    }

    protected function getSelect($properties, $key)
    {
        return $properties[$key]['select']['name'] ?? null;
    }

    protected function getCheckbox($properties, $key)
    {
        return $properties[$key]['checkbox'] ?? false;
    }

    protected function getDate($properties, $key)
    {
        $dateStr = $properties[$key]['date']['start'] ?? null;
        return $dateStr ? \Carbon\Carbon::parse($dateStr) : null;
    }

    protected function getNumber($properties, $key)
    {
        return $properties[$key]['number'] ?? null;
    }

    protected function getUrl($properties, $key)
    {
        return $properties[$key]['url'] ?? null;
    }

    protected function getRelation($properties, $key)
    {
        return $properties[$key]['relation'][0]['id'] ?? null;
    }

    // --- Pullers ---

    protected function pullTasks($userId)
    {
        $activeIds = [];
        $pages = $this->queryDatabase('tasks');
        foreach ($pages as $page) {
            $activeIds[] = $page['id'];
            $props = $page['properties'];
            $title = $this->getTitle($props, 'Name');
            if (!$title) continue;

            $tbNotionId = $this->getRelation($props, 'Time Block');
            $tbId = $tbNotionId ? TimeBlock::where('notion_id', $tbNotionId)->value('id') : null;

            $carryDate = $this->getDate($props, 'Carry Over Date');

            $task = Task::firstOrNew(['notion_id' => $page['id']]);
            $task->fill([
                'user_id' => $userId,
                'title' => $title,
                'is_done' => $this->getCheckbox($props, 'Is Completed'),
                'category' => $this->getSelect($props, 'Category'),
                'status' => $this->getSelect($props, 'Status'),
                'review_note' => $this->getRichText($props, 'Review Note'),
                'carry_over_date' => $carryDate ? $carryDate->toDateString() : null,
                'time_block_id' => $tbId ?? $task->time_block_id, // preserve if relation not found
            ]);
            $task->saveQuietly();
        }
        return $activeIds;
    }

    protected function pullTimeBlocks($userId)
    {
        $activeIds = [];
        $pages = $this->queryDatabase('time_blocks');
        foreach ($pages as $page) {
            $activeIds[] = $page['id'];
            $props = $page['properties'];
            $title = $this->getTitle($props, 'Title');
            if (!$title) continue;

            $start = $this->getDate($props, 'Start Time');
            $end = $this->getDate($props, 'End Time');

            $block = TimeBlock::firstOrNew(['notion_id' => $page['id']]);
            $block->fill([
                'user_id' => $userId,
                'title' => $title,
                'block_type' => $this->getSelect($props, 'Block Type') ?? 'work',
                'starts_at' => $start ? $start->format('H:i:s') : '09:00:00',
                'ends_at' => $end ? $end->format('H:i:s') : '10:00:00',
                'notes' => $this->getRichText($props, 'Notes'),
                'sort_order' => $this->getNumber($props, 'Sort Order') ?? 0,
            ]);
            $block->saveQuietly();
        }
        return $activeIds;
    }

    protected function pullDailyReviews($userId)
    {
        $activeIds = [];
        $pages = $this->queryDatabase('daily_reviews');
        foreach ($pages as $page) {
            $activeIds[] = $page['id'];
            $props = $page['properties'];
            $dateStr = $this->getTitle($props, 'Review Date');
            if (!$dateStr) continue;

            $review = DailyReview::firstOrNew(['notion_id' => $page['id']]);
            $review->fill([
                'user_id' => $userId,
                'review_date' => $dateStr,
                'focus_score' => $this->getNumber($props, 'Focus Score'),
                'summary' => $this->getRichText($props, 'Summary'),
            ]);
            $review->saveQuietly();
        }
        return $activeIds;
    }

    protected function pullTransactions($userId)
    {
        $activeIds = [];
        $pages = $this->queryDatabase('transactions');
        foreach ($pages as $page) {
            $activeIds[] = $page['id'];
            $props = $page['properties'];
            $desc = $this->getTitle($props, 'Description') ?? 'Transaction';

            $tx = Transaction::firstOrNew(['notion_id' => $page['id']]);
            $tx->fill([
                'user_id' => $userId,
                'description' => $desc,
                'type' => $this->getSelect($props, 'Type') ?? 'expense',
                'amount' => $this->getNumber($props, 'Amount') ?? 0,
                'category' => $this->getSelect($props, 'Category'),
                'date' => $this->getDate($props, 'Date') ?? now(),
            ]);
            $tx->saveQuietly();
        }
        return $activeIds;
    }

    protected function pullWishlistItems($userId)
    {
        $activeIds = [];
        $pages = $this->queryDatabase('wishlist');
        foreach ($pages as $page) {
            $activeIds[] = $page['id'];
            $props = $page['properties'];
            $title = $this->getTitle($props, 'Title');
            if (!$title) continue;

            $item = WishlistItem::firstOrNew(['notion_id' => $page['id']]);
            $item->fill([
                'user_id' => $userId,
                'title' => $title,
                'price' => $this->getNumber($props, 'Price') ?? 0,
                'is_bought' => $this->getCheckbox($props, 'Is Bought'),
                'priority' => $this->getSelect($props, 'Priority'),
            ]);
            $item->saveQuietly();
        }
        return $activeIds;
    }

    protected function pullSubscriptions($userId)
    {
        $activeIds = [];
        $pages = $this->queryDatabase('subscriptions');
        foreach ($pages as $page) {
            $activeIds[] = $page['id'];
            $props = $page['properties'];
            $title = $this->getTitle($props, 'Title');
            if (!$title) continue;

            $sub = Subscription::firstOrNew(['notion_id' => $page['id']]);
            $sub->fill([
                'user_id' => $userId,
                'title' => $title,
                'amount' => $this->getNumber($props, 'Amount') ?? 0,
                'billing_cycle' => $this->getSelect($props, 'Billing Cycle'),
                'next_billing_date' => $this->getDate($props, 'Next Billing Date'),
            ]);
            $sub->saveQuietly();
        }
        return $activeIds;
    }

    protected function pullHabits($userId)
    {
        $activeIds = [];
        $pages = $this->queryDatabase('habits');
        foreach ($pages as $page) {
            $activeIds[] = $page['id'];
            $props = $page['properties'];
            $title = $this->getTitle($props, 'Title');
            if (!$title) continue;

            $tbNotionId = $this->getRelation($props, 'Time Block');
            $tbId = $tbNotionId ? TimeBlock::where('notion_id', $tbNotionId)->value('id') : null;

            $habit = Habit::firstOrNew(['notion_id' => $page['id']]);
            $habit->fill([
                'user_id' => $userId,
                'title' => $title,
                'target' => $this->getNumber($props, 'Target') ?? 1,
                'time_block_id' => $tbId ?? $habit->time_block_id,
            ]);
            $habit->saveQuietly();
        }
        return $activeIds;
    }

    protected function pullWorkoutPlans($userId)
    {
        $activeIds = [];
        $pages = $this->queryDatabase('workout_plans');
        foreach ($pages as $page) {
            $activeIds[] = $page['id'];
            $props = $page['properties'];
            $title = $this->getTitle($props, 'Title');
            if (!$title) continue;

            $plan = WorkoutPlan::firstOrNew(['notion_id' => $page['id']]);
            $plan->fill([
                'user_id' => $userId,
                'title' => $title,
                'day_of_week' => $this->getSelect($props, 'Day of Week'),
            ]);
            $plan->saveQuietly();
        }
        return $activeIds;
    }

    protected function pullExercises()
    {
        $activeIds = [];
        $pages = $this->queryDatabase('exercises');
        foreach ($pages as $page) {
            $activeIds[] = $page['id'];
            $props = $page['properties'];
            $title = $this->getTitle($props, 'Title');
            if (!$title) continue;

            $exercise = Exercise::firstOrNew(['notion_id' => $page['id']]);
            $exercise->fill([
                'title' => $title,
                'muscle_group' => $this->getSelect($props, 'Muscle Group'),
                'equipment' => $this->getSelect($props, 'Equipment'),
            ]);
            $exercise->saveQuietly();
        }
        return $activeIds;
    }

    protected function pullCredentials($userId)
    {
        $activeIds = [];
        $pages = $this->queryDatabase('credentials');
        foreach ($pages as $page) {
            $activeIds[] = $page['id'];
            $props = $page['properties'];
            $platform = $this->getTitle($props, 'Platform');
            if (!$platform) continue;

            $cred = Credential::firstOrNew(['notion_id' => $page['id']]);
            // Be careful not to overwrite passwords if they exist and are not provided in Notion securely
            $cred->fill([
                'user_id' => $userId,
                'platform' => $platform,
                'username' => $this->getRichText($props, 'Username'),
                'url' => $this->getUrl($props, 'URL'),
                'notes' => $this->getRichText($props, 'Notes'),
            ]);
            if (!$cred->password) {
                $cred->password = ''; // Required field
            }
            $cred->saveQuietly();
        }
        return $activeIds;
    }

    protected function pullNotes($userId)
    {
        $activeIds = [];
        $pages = $this->queryDatabase('notes');
        foreach ($pages as $page) {
            $activeIds[] = $page['id'];
            $props = $page['properties'];
            $content = $this->getTitle($props, 'Content');
            if (!$content) continue;

            $note = Note::firstOrNew(['notion_id' => $page['id']]);
            $note->fill([
                'user_id' => $userId,
                'content' => $content,
            ]);

            $createdDate = $this->getDate($props, 'Created Date');
            if ($createdDate) {
                $note->created_at = $createdDate;
            }

            $note->saveQuietly();
        }
        return $activeIds;
    }
}
