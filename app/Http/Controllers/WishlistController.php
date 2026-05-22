<?php

namespace App\Http\Controllers;

use App\Models\WishlistItem;
use Illuminate\Http\Request;
use App\Services\NotionSyncService;

class WishlistController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        $item = WishlistItem::updateOrCreate(
            ['id' => $request->id, 'user_id' => $user->id],
            [
                'title' => $request->title,
                'price' => $request->price,
                'priority' => $request->priority,
            ]
        );

        app(NotionSyncService::class)->syncWishlist($item);

        return response()->json($item);
    }

    public function toggle(WishlistItem $item)
    {
        $item->update(['is_bought' => !$item->is_bought]);
        app(NotionSyncService::class)->syncWishlist($item);
        return response()->json($item);
    }

    public function destroy(WishlistItem $item)
    {
        app(NotionSyncService::class)->deleteEntity($item);
        $item->delete();
        return response()->json(['message' => 'Item deleted']);
    }
}
