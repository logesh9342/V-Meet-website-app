<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MeetingController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'meetingName' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first()
            ], 400);
        }

        // Generate unique meeting ID
        do {
            $meetingId = strtoupper(Str::random(6));
            $exists = Meeting::where('meeting_id', $meetingId)->exists();
        } while ($exists);

        // Save to DB
        $meeting = Meeting::create([
            'user_id' => auth()->id(),
            'meeting_name' => $request->meetingName,
            'meeting_id' => $meetingId,
            'meeting_link' => url("/join/{$meetingId}"),
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $meeting->id,
                'meetingName' => $meeting->meeting_name,
                'meetingId' => $meeting->meeting_id,
                'meetingLink' => $meeting->meeting_link,
            ]
        ]);
    }

    public function index()
    {
        $meetings = Meeting::where('user_id', auth()->id())->latest()->get();
        return response()->json(['meetings' => $meetings]);
    }
}