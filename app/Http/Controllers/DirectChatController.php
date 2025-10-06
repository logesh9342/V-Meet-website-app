<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DirectChatController extends Controller
{
    // Ensure a direct room exists for the authenticated user and target user
    public function ensureRoom(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $authId = Auth::id();
        $otherId = (int) $request->input('user_id');
        if ($authId === $otherId) {
            return response()->json(['message' => 'Cannot create direct chat with yourself'], 422);
        }

        // Try to find existing direct room with exactly these two participants
        $room = ChatRoom::where('type', 'direct')
            ->whereHas('participants', function ($q) use ($authId) { $q->where('users.id', $authId); })
            ->whereHas('participants', function ($q) use ($otherId) { $q->where('users.id', $otherId); })
            ->withCount('participants')
            ->get()
            ->firstWhere('participants_count', 2);

        if (! $room) {
            // Create new direct room with a friendly name
            $other = User::findOrFail($otherId);
            $room = ChatRoom::create([
                'name' => 'Chat: '.Auth::user()->name.' & '.$other->name,
                'type' => 'direct',
            ]);
            $room->participants()->sync([$authId, $otherId]);
        }

        $messages = Message::where('chat_room_id', $room->id)
            ->with('user')
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'room' => $room,
            'messages' => $messages,
        ]);
    }
}
