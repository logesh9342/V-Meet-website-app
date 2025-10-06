<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ChatRoom;
use Illuminate\Support\Facades\Auth;

class ChatRoomController extends Controller
{
    // List all chat rooms
    public function index()
    {
        $user = Auth::user();
        // Return group/meeting rooms plus direct rooms the user is a participant of
        $groups = ChatRoom::whereIn('type', ['group', 'meeting'])->get();
        $directs = ChatRoom::where('type', 'direct')
            ->whereHas('participants', fn($q) => $q->where('users.id', $user->id))
            ->get();
        return $groups->concat($directs)->values();
    }

    // Create a new chat room
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:group,meeting,direct',
        ]);
        $room = ChatRoom::create([
            'name' => $request->name,
            'type' => $request->type,
        ]);
        return response()->json($room, 201);
    }

    // Show a specific chat room
    public function show(ChatRoom $chatRoom)
    {
        return $chatRoom;
    }
}
