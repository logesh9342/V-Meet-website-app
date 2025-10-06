<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\FileShare;
// ...existing code...
use Illuminate\Support\Facades\Auth;

class FileShareController extends Controller
{
    // List files for a chat room or meeting
    public function index(Request $request)
    {
        $files = FileShare::query();
        if ($request->chat_room_id) {
            $files->where('chat_room_id', $request->chat_room_id);
        }
        if ($request->meeting_id) {
            $files->where('meeting_id', $request->meeting_id);
        }
        return $files->orderBy('created_at')->get();
    }

    // Upload a file to a chat room or meeting
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'chat_room_id' => 'nullable|exists:chat_rooms,id',
            'meeting_id' => 'nullable|exists:meetings,id',
        ]);
        $path = $request->file('file')->store('uploads', 'public');
        $fileShare = FileShare::create([
            'user_id' => Auth::id(),
            'chat_room_id' => $request->chat_room_id,
            'meeting_id' => $request->meeting_id,
            'file_path' => $path,
            'file_name' => $request->file('file')->getClientOriginalName(),
        ]);
        return response()->json($fileShare, 201);
    }
}
