<?php

namespace App\Http\Controllers;

use App\Models\DailyReview;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Services\NotionSyncService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DailyReviewController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        $review = DailyReview::updateOrCreate(
            ['user_id' => $user->id, 'review_date' => now()->toDateString()],
            [
                'daily_focus' => $request->daily_focus,
                'focus_score' => $request->focus_score, 
                'summary' => $request->summary
            ]
        );
        
        app(NotionSyncService::class)->syncDailyReviewToNotion($review);

        // Auto-collect unfinished tasks
        $tasksToUpdate = Task::where('user_id', $user->id)
            ->where('is_done', false)
            ->whereNull('carry_over_date')
            ->get();

        foreach ($tasksToUpdate as $task) {
            $task->update(['carry_over_date' => now()->addDay()->toDateString()]);
            app(NotionSyncService::class)->syncTaskToNotion($task);
        }

        return response()->json($review);
    }

    public function generateWeeklySummary(Request $request)
    {
        $user = auth()->user();
        $sevenDaysAgo = now()->subDays(7)->toDateString();

        // Get reviews
        $reviews = DailyReview::where('user_id', $user->id)
            ->where('review_date', '>=', $sevenDaysAgo)
            ->orderBy('review_date', 'asc')
            ->get();

        // Get task metrics
        $tasks = Task::where('user_id', $user->id)
            ->where(function($query) use ($sevenDaysAgo) {
                $query->where('task_date', '>=', $sevenDaysAgo)
                      ->orWhere('carry_over_date', '>=', $sevenDaysAgo);
            })
            ->get();

        $totalTasks = $tasks->count();
        $completedTasks = $tasks->where('is_done', true)->count();
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        $reviewsData = [];
        $totalFocus = 0;
        $focusCount = 0;
        foreach ($reviews as $r) {
            $reviewsData[] = "- Date: {$r->review_date}, Focus: {$r->daily_focus}, Score: {$r->focus_score}/10, Notes: {$r->summary}";
            if ($r->focus_score !== null) {
                $totalFocus += $r->focus_score;
                $focusCount++;
            }
        }
        $avgFocus = $focusCount > 0 ? round($totalFocus / $focusCount, 1) : null;

        $reviewsText = implode("\n", $reviewsData);

        // Check if API key is present
        $apiKey = config('services.gemini.key');

        if (!$apiKey) {
            // Provide a rich, helpful Mock response showing potential insights
            $mockResponse = "### 🧠 AI Weekly Performance Summary (Sample Mode)\n\n" .
                "*(Note: Configure `GEMINI_API_KEY` in your `.env` file to generate real-time AI summaries.)*\n\n" .
                "#### 📈 Focus Score Patterns\n" .
                "- Your average focus score for the last 7 days is **" . ($avgFocus ?? 'N/A') . "/10** based on the logged entries.\n" .
                "- Focus appears stable, peaking during the mid-week blocks.\n\n" .
                "#### ⚠️ Fatigue Risk\n" .
                "- Moderate. Ensure you schedule regular breaks between deep focus blocks to prevent burnout.\n\n" .
                "#### ⏱️ Task Completion Bottlenecks\n" .
                "- Completed **{$completedTasks} out of {$totalTasks} tasks** ({$completionRate}% completion rate).\n" .
                "- Carry-over actions were high on tasks related to secondary categories. Try breaking larger tasks down into smaller, actionable sub-tasks next week.";

            return response()->json([
                'summary' => $mockResponse,
                'is_mock' => true
            ]);
        }

        $prompt = "You are an expert productivity coach. Analyze the user's daily reviews and task completion data for the last 7 days and generate a concise weekly performance summary with three clear sections:\n" .
            "1. Focus Score Patterns (analyze average focus score and trends, average focus: " . ($avgFocus ?? 'N/A') . "/10)\n" .
            "2. Fatigue Risk (assess burnout or fatigue based on notes/focus patterns)\n" .
            "3. Task Completion Bottlenecks (analyze completion rate: {$completedTasks} completed out of {$totalTasks} total tasks ({$completionRate}%), and suggest improvements)\n\n" .
            "Here is the daily reviews data:\n{$reviewsText}\n\n" .
            "Provide the output in clean Markdown formatting.";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Could not parse summary content.';
                return response()->json([
                    'summary' => $text,
                    'is_mock' => false
                ]);
            }

            throw new \Exception("Gemini API Error: " . $response->body());

        } catch (\Exception $e) {
            Log::error("Gemini weekly summary failed: " . $e->getMessage());
            return response()->json([
                'summary' => "### ⚠️ Error Generating AI Summary\n\nCould not fetch summary from Gemini API. Error details: " . $e->getMessage(),
                'is_mock' => false
            ], 500);
        }
    }
}
