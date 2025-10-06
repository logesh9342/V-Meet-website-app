<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class MeetingChatController extends Controller
{
    // Fetch messages for a meeting room
    public function index($meetingId)
    {
        return Message::where('meeting_id', $meetingId)->with('user')->orderBy('created_at')->get();
    }

    // Send a message in a meeting room
    public function store(Request $request, $meetingId)
    {
        $request->validate([
            'content' => 'required|string',
        ]);
        $message = Message::create([
            'meeting_id' => $meetingId,
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
        ]);
        return response()->json($message, 201);
    }
}
