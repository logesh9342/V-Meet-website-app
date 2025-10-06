<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MeetingController extends Controller
{
    // GET: Show the create form (optional if you only use popup)
    public function create()
    {
        return view('meeting'); // make a Blade view if you want separate page
    }

    public function meetlink(){
        return view('create-mlink');
    }
    public function prejoin()
   {  
    return view('prejoin');
    }

    public function shareview()
   {  
    return view('sharingview');
    }

    public function createlink(){
        return view('create-mlink');
    }

     public function joinmeeting(){
        return view('join');
    }

    // POST: Store meeting
    public function store(Request $request)
    {
        // validate
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:meetings,code',
        ]);

        // Save meeting in DB (assuming you have a Meeting model)
        $meeting = \App\Models\Meeting::create($validated);

        // redirect to join page
        return redirect()->route('meeting.join', ['code' => $meeting->code]);
    }
}
