<?php

namespace App\Http\Controllers;

use App\Models\JulesSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JulesController extends Controller
{
    protected string $baseUrl = 'https://jules.googleapis.com/v1alpha';

    public function index()
    {
        $user = auth()->user();
        
        // Fetch sessions safely checking if table exists, else empty collection
        try {
            $sessions = JulesSession::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        } catch (\Exception $e) {
            $sessions = collect();
        }

        $defaultRepo = config('services.jules.default_repo');

        return view('jules-console', compact('user', 'sessions', 'defaultRepo'));
    }

    public function createSession(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string',
            'repo_path' => 'nullable|string',
        ]);

        $user = auth()->user();
        $repoPath = $request->repo_path ?: config('services.jules.default_repo');
        $apiKey = config('services.jules.key');

        if (!$apiKey || str_contains($apiKey, 'YOUR_API_KEY')) {
            // Mock Fallback
            $sessionId = 'sessions/mock_' . uniqid();
            try {
                $session = JulesSession::create([
                    'user_id' => $user->id,
                    'session_id' => $sessionId,
                    'prompt' => $request->prompt,
                    'repo_path' => $repoPath,
                    'status' => 'RUNNING (Mock Mode)',
                ]);
            } catch (\Exception $e) {
                // If migration hasn't been run yet, return the mock object directly
                $session = (object) [
                    'id' => 999,
                    'session_id' => $sessionId,
                    'prompt' => $request->prompt,
                    'repo_path' => $repoPath,
                    'status' => 'RUNNING (Mock Mode)',
                ];
            }

            return response()->json([
                'session' => $session,
                'is_mock' => true,
                'message' => 'Created mock session (No API key set)'
            ]);
        }

        try {
            $response = Http::withHeaders([
                'X-Goog-Api-Key' => $apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/sessions", [
                'prompt' => $request->prompt,
                'sourceContext' => [
                    'source' => $repoPath,
                    'githubRepoContext' => [
                        'startingBranch' => 'main'
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $sessionId = $data['name'] ?? 'sessions/' . uniqid();
                
                $session = JulesSession::create([
                    'user_id' => $user->id,
                    'session_id' => $sessionId,
                    'prompt' => $request->prompt,
                    'repo_path' => $repoPath,
                    'status' => $data['status'] ?? 'RUNNING',
                ]);

                return response()->json([
                    'session' => $session,
                    'is_mock' => false
                ]);
            }

            throw new \Exception("Jules API Error: " . $response->body());

        } catch (\Exception $e) {
            Log::error("Jules Session Creation Failed: " . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create session: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $user = auth()->user();
        
        try {
            $session = JulesSession::where('user_id', $user->id)->findOrFail($id);
        } catch (\Exception $e) {
            // Fallback for mock if database table not ready or record is custom
            $session = (object) [
                'id' => $id,
                'session_id' => 'sessions/mock_' . $id,
                'prompt' => 'Sample coding prompt',
                'repo_path' => 'sources/github/username/repo',
                'status' => 'RUNNING (Mock Mode)',
            ];
        }

        if (str_starts_with($session->session_id, 'sessions/mock_')) {
            // Generate elegant mock activities for demo
            $mockActivities = [
                [
                    'name' => 'activities/1',
                    'timestamp' => now()->subMinutes(5)->toIso8601String(),
                    'type' => 'agent_started',
                    'description' => 'Jules AI Agent started working on task.',
                ],
                [
                    'name' => 'activities/2',
                    'timestamp' => now()->subMinutes(4)->toIso8601String(),
                    'type' => 'plan_generated',
                    'description' => 'Plan generated: 1) Locate TaskController.php 2) Refactor task assignment validation 3) Add model tests.',
                ],
                [
                    'name' => 'activities/3',
                    'timestamp' => now()->subMinutes(2)->toIso8601String(),
                    'type' => 'code_modification',
                    'description' => 'Modified c:\Project\dashboard\app\Http\Controllers\TaskController.php validation rules.',
                ],
                [
                    'name' => 'activities/4',
                    'timestamp' => now()->toIso8601String(),
                    'type' => 'pull_request_created',
                    'description' => 'Pull Request #42 created successfully on main branch.',
                ]
            ];

            return response()->json([
                'session' => $session,
                'activities' => $mockActivities,
                'is_mock' => true
            ]);
        }

        $apiKey = config('services.jules.key');

        try {
            $response = Http::withHeaders([
                'X-Goog-Api-Key' => $apiKey,
            ])->get("{$this->baseUrl}/{$session->session_id}/activities");

            if ($response->successful()) {
                return response()->json([
                    'session' => $session,
                    'activities' => $response->json('activities') ?? [],
                    'is_mock' => false
                ]);
            }

            throw new \Exception("Jules API Error: " . $response->body());

        } catch (\Exception $e) {
            Log::error("Jules Fetch Activities Failed: " . $e->getMessage());
            return response()->json(['message' => 'Failed to fetch details: ' . $e->getMessage()], 500);
        }
    }

    public function sendMessage(Request $request, $id)
    {
        $request->validate(['message' => 'required|string']);
        
        $user = auth()->user();
        try {
            $session = JulesSession::where('user_id', $user->id)->findOrFail($id);
        } catch (\Exception $e) {
            $session = (object) [
                'session_id' => 'sessions/mock_' . $id,
            ];
        }

        if (str_starts_with($session->session_id, 'sessions/mock_')) {
            return response()->json([
                'message' => 'Message delivered (Mock Mode)',
                'reply' => 'Jules (Mock): I have received your message: "' . $request->message . '". I will adjust the plan accordingly.'
            ]);
        }

        $apiKey = config('services.jules.key');

        try {
            $response = Http::withHeaders([
                'X-Goog-Api-Key' => $apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/{$session->session_id}:sendMessage", [
                'message' => $request->message
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            throw new \Exception("Jules API Error: " . $response->body());

        } catch (\Exception $e) {
            Log::error("Jules Send Message Failed: " . $e->getMessage());
            return response()->json(['message' => 'Failed to send message: ' . $e->getMessage()], 500);
        }
    }
}
