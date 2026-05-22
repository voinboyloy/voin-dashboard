<?php

namespace App\Http\Controllers;

use App\Models\Credential;
use Illuminate\Http\Request;
use App\Services\NotionSyncService;

class CredentialController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $credentials = Credential::where('user_id', $user->id)->orderBy('platform')->get();

        return view('credentials-vault', compact('user', 'credentials'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $credential = Credential::updateOrCreate(
            ['id' => $request->id, 'user_id' => $user->id],
            [
                'platform' => $request->platform,
                'username' => $request->username,
                'password' => $request->password,
                'url' => $request->url,
                'notes' => $request->notes,
            ]
        );

        app(NotionSyncService::class)->syncCredential($credential);

        return response()->json($credential);
    }

    public function destroy(Credential $credential)
    {
        app(NotionSyncService::class)->deleteEntity($credential);
        $credential->delete();
        return response()->json(['message' => 'Credential deleted']);
    }
}
