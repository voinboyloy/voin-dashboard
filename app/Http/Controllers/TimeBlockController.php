<?php

namespace App\Http\Controllers;

use App\Models\TimeBlock;
use Illuminate\Http\Request;
use App\Services\NotionSyncService;

class TimeBlockController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        $block = TimeBlock::updateOrCreate(
            ['id' => $request->id, 'user_id' => $user->id],
            [
                'title' => $request->title,
                'block_type' => $request->block_type,
                'starts_at' => $request->starts_at,
                'ends_at' => $request->ends_at,
                'notes' => $request->notes,
            ]
        );

        app(NotionSyncService::class)->syncTimeBlockToNotion($block);

        return response()->json($block);
    }

    public function destroy(TimeBlock $block)
    {
        app(NotionSyncService::class)->deleteEntity($block);
        $block->delete();
        return response()->json(['message' => 'Block deleted']);
    }
}
