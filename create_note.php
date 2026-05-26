<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Note;
use App\Models\Task;
use App\Services\NotionSyncService;

foreach (User::all() as $user) {
    // 1. Create monthly todo list note if it doesn't already exist
    $noteText = "📅 May 2026 Todo List:\n" .
                "- [ ] Complete 4 workouts per week\n" .
                "- [ ] Achieve 90% weekly task completion\n" .
                "- [ ] Sync and align Notion notes database\n" .
                "- [ ] Review savings goals and budget allocation";
                
    $existingNote = $user->notes()->where('content', 'like', '%May 2026 Todo List%')->first();
    if (!$existingNote) {
        $note = $user->notes()->create(['content' => $noteText]);
        echo "Created monthly note for user: {$user->email}\n";
        try {
            app(NotionSyncService::class)->syncNote($note);
        } catch (\Exception $e) {
            echo "Notion sync skipped/failed for note: {$e->getMessage()}\n";
        }
    } else {
        echo "Monthly note already exists for user: {$user->email}\n";
    }

    // 2. Create monthly task if it doesn't already exist
    $coreWork = $user->timeBlocks()->where('title', 'Core Work Block')->first();
    if ($coreWork) {
        $existingTask = $coreWork->tasks()->where('title', 'Review monthly budget and savings')->first();
        if (!$existingTask) {
            $task = $coreWork->tasks()->create([
                'user_id' => $user->id,
                'title' => 'Review monthly budget and savings',
                'category' => 'work',
                'task_date' => '2026-05-25',
                'is_done' => false
            ]);
            echo "Created monthly task for user: {$user->email}\n";
            try {
                app(NotionSyncService::class)->syncTaskToNotion($task);
            } catch (\Exception $e) {
                echo "Notion sync skipped/failed for task: {$e->getMessage()}\n";
            }
        } else {
            echo "Monthly task already exists for user: {$user->email}\n";
        }
    }
}
