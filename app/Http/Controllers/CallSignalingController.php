<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class CallSignalingController extends Controller
{
    // Send signaling message to a room
    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'room_id' => 'required|string',
            'type' => 'required|string', // offer, answer, candidate
            'data' => 'required|array',
        ]);
        $user = Auth::user();
        // Broadcast to other users in the room (simple cache for demo)
        $messages = Cache::get('signaling_' . $validated['room_id'], []);
        $messages[] = [
            'user_id' => $user ? $user->id : null,
            'type' => $validated['type'],
            'data' => $validated['data'],
            'timestamp' => now(),
        ];
        Cache::put('signaling_' . $validated['room_id'], $messages, 60);
        return response()->json(['status' => 'sent']);
    }

    // Receive signaling messages for a room
    public function receive(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'room_id' => 'required|string',
        ]);
        $messages = Cache::get('signaling_' . $validated['room_id'], []);
        return response()->json(['messages' => $messages]);
    }

    // Clear signaling messages (optional, for cleanup)
    public function clear(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'room_id' => 'required|string',
        ]);
        Cache::forget('signaling_' . $validated['room_id']);
        return response()->json(['status' => 'cleared']);
    }
}
