<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Task;
use App\Models\TimeBlock;
use App\Models\DailyReview;
use Illuminate\Support\Facades\Log;

class NotionSyncService
{
    protected string $baseUrl = 'https://api.notion.com/v1';

    protected function client()
    {
        return Http::withToken(config('services.notion.token'))
            ->withHeaders(['Notion-Version' => '2022-06-28']);
    }

    public function syncTaskToNotion(Task $task)
    {
        if (!config('services.notion.token') || !config('services.notion.databases.tasks')) {
            return;
        }

        $properties = [
            'Name' => [
                'title' => [
                    ['text' => ['content' => $task->title]]
                ]
            ],
            'Is Completed' => [
                'checkbox' => $task->is_done
            ]
        ];

        if ($task->category) {
            $properties['Category'] = [
                'select' => ['name' => $task->category]
            ];
        }

        if ($task->status) {
            $properties['Status'] = [
                'select' => ['name' => $task->status]
            ];
        }

        if ($task->carry_over_date) {
            $properties['Carry Over Date'] = [
                'date' => ['start' => $task->carry_over_date]
            ];
        }

        if ($task->review_note) {
            $properties['Review Note'] = [
                'rich_text' => [
                    ['text' => ['content' => $task->review_note]]
                ]
            ];
        }

        // Only link time block if it has been synced and has a notion_id
        if ($task->timeBlock && $task->timeBlock->notion_id) {
            $properties['Time Block'] = [
                'relation' => [
                    ['id' => $task->timeBlock->notion_id]
                ]
            ];
        }

        if ($task->notion_id) {
            // Update existing page
            $response = $this->client()->patch("{$this->baseUrl}/pages/{$task->notion_id}", [
                'properties' => $properties
            ]);
        } else {
            // Create new page
            $response = $this->client()->post("{$this->baseUrl}/pages", [
                'parent' => ['database_id' => config('services.notion.databases.tasks')],
                'properties' => $properties
            ]);

            if ($response->successful()) {
                $task->notion_id = $response->json('id');
                // Save quietly to avoid infinite loops if an observer triggers this service
                $task->saveQuietly();
            }
        }

        if ($response->failed()) {
            Log::error('Notion Task Sync Failed: ' . $response->body());
        }
    }

    public function syncTimeBlockToNotion(TimeBlock $block)
    {
        if (!config('services.notion.token') || !config('services.notion.databases.time_blocks')) {
            return;
        }

        $today = now()->toDateString();
        $startTime = "{$today}T{$block->starts_at}";
        $endTime = "{$today}T{$block->ends_at}";

        $properties = [
            'Title' => [
                'title' => [
                    ['text' => ['content' => $block->title]]
                ]
            ],
            'Block Type' => [
                'select' => ['name' => $block->block_type]
            ],
            'Start Time' => [
                'date' => ['start' => $startTime]
            ],
            'End Time' => [
                'date' => ['start' => $endTime]
            ],
            'Sort Order' => [
                'number' => $block->sort_order
            ]
        ];

        if ($block->notes) {
            $properties['Notes'] = [
                'rich_text' => [
                    ['text' => ['content' => $block->notes]]
                ]
            ];
        }

        if ($block->notion_id) {
            $response = $this->client()->patch("{$this->baseUrl}/pages/{$block->notion_id}", [
                'properties' => $properties
            ]);
        } else {
            $response = $this->client()->post("{$this->baseUrl}/pages", [
                'parent' => ['database_id' => config('services.notion.databases.time_blocks')],
                'properties' => $properties
            ]);

            if ($response->successful()) {
                $block->notion_id = $response->json('id');
                $block->saveQuietly();
            }
        }

        if ($response->failed()) {
            Log::error('Notion Time Block Sync Failed: ' . $response->body());
        }
    }

    public function syncDailyReviewToNotion(DailyReview $review)
    {
        if (!config('services.notion.token') || !config('services.notion.databases.daily_reviews')) {
            return;
        }

        $properties = [
            'Review Date' => [
                'title' => [
                    ['text' => ['content' => $review->review_date->toDateString()]]
                ]
            ]
        ];

        if ($review->focus_score !== null) {
            $properties['Focus Score'] = [
                'number' => (int) $review->focus_score
            ];
        }

        if ($review->summary) {
            $properties['Summary'] = [
                'rich_text' => [
                    ['text' => ['content' => $review->summary]]
                ]
            ];
        }

        if ($review->notion_id) {
            $response = $this->client()->patch("{$this->baseUrl}/pages/{$review->notion_id}", [
                'properties' => $properties
            ]);
        } else {
            $response = $this->client()->post("{$this->baseUrl}/pages", [
                'parent' => ['database_id' => config('services.notion.databases.daily_reviews')],
                'properties' => $properties
            ]);

            if ($response->successful()) {
                $review->notion_id = $response->json('id');
                $review->saveQuietly();
            }
        }

        if ($response->failed()) {
            Log::error('Notion Daily Review Sync Failed: ' . $response->body());
        }
    }
}
