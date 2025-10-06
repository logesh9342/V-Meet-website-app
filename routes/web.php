<?php
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CallSignalingController;
use App\Http\Controllers\DirectChatController;
use App\Http\Controllers\ChatRoomController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MeetingController;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware('auth')->get('/jeymeet', function () {
    return view('jeymeet');
})->name('jeymeet');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/chat', function () {
        $user = app('auth')->user();
        // Get recent contacts: users the current user has chatted with (simplified)
        $recentContacts = \App\Models\User::where('id', '!=', $user->id)
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();
        return view('chat', compact('recentContacts'));
    })->name('chat');
    Route::get('/meeting', function () {
        return view('meeting');
    })->name('meeting');
    Route::get('/schedule', function () {
        return view('schedule');
    })->name('schedule');
    Route::get('/fileshare', function () {
        return view('fileshare');
    })->name('fileshare');

    // Direct chat helper: ensure/find a room with another user
    Route::post('/chat/ensure-direct', [DirectChatController::class, 'ensureRoom'])->name('chat.ensureDirect');

    // Chat API under web auth
    Route::get('/api/chat-rooms', [ChatRoomController::class, 'index']);
    Route::post('/api/chat-rooms', [ChatRoomController::class, 'store']);
    Route::get('/api/chat-rooms/{chatRoom}', [ChatRoomController::class, 'show']);
    Route::get('/api/chat-rooms/{chatRoom}/messages', [MessageController::class, 'index']);
    Route::post('/api/chat-rooms/{chatRoom}/messages', [MessageController::class, 'store']);
});

require __DIR__.'/auth.php';
// Signaling API routes for video/audio calls
Route::middleware('auth')->group(function () {
    Route::post('/api/signaling/send', [CallSignalingController::class, 'send']);
    Route::get('/api/signaling/receive', [CallSignalingController::class, 'receive']);
    Route::post('/api/signaling/clear', [CallSignalingController::class, 'clear']);

    // Meeting chat API
    Route::get('/api/meeting/{meetingId}/messages', [\App\Http\Controllers\MeetingChatController::class, 'index']);
    Route::post('/api/meeting/{meetingId}/messages', [\App\Http\Controllers\MeetingChatController::class, 'store']);
});

use App\Models\Message;
use Illuminate\Http\Request;

Route::post('/messages', function (Request $request) {
    $message = Message::create([
        'user_id' => auth()->id() ?? 1, // fallback user_id = 1
        'content' => $request->input('content'),
    ]);
    return response()->json($message);
});

Route::get('/dashboard', function () {
    return view('meet.dashboard');
});




Route::middleware('auth')->group(function () {
    Route::get('/meeting', [MeetingController::class, 'create'])->name('meeting');
    Route::get('/meet/meeting', [MeetingController::class, 'meetlink']);
    Route::get('/join/meeting', [MeetingController::class, 'joinmeeting'])->name('join.meeting');;
    Route::get('/create/meeting', [MeetingController::class, 'createlink'])->name('create.meeting');
    Route::get('/prejoin/meeting', [MeetingController::class, 'prejoin'])->name('prejoin.screen');
    Route::get('/shareview/meeting', [MeetingController::class, 'shareview'])->name('share.screen');
    Route::post('/meetings', [MeetingController::class, 'store'])->name('meetings.store');
    Route::get('/meeting/create', [MeetingController::class, 'create'])->name('meeting.create');
    
});
