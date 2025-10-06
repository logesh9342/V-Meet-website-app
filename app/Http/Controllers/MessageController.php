<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
// ...existing code...

use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Events\MessageSent;

class MessageController extends Controller
{
    // Fetch messages for a chat room
    public function index(\App\Models\ChatRoom $chatRoom)
    {
        return Message::where('chat_room_id', $chatRoom->id)->with('user')->orderBy('created_at')->get();
    }

    // Send a message
    public function store(Request $request, \App\Models\ChatRoom $chatRoom)
    {
        $request->validate([
            'content' => 'required|string',
        ]);
        $message = Message::create([
            'chat_room_id' => $chatRoom->id,
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
        ]);
        try {
            broadcast(new MessageSent($message))->toOthers();
        } catch (\Throwable $e) {
            // Swallow broadcasting failures in local/dev if driver isn’t configured
        }
        return response()->json($message->load('user'), 201);
    }
}
