@extends('layouts.app')

@section('content')
<div class="jey-meet-index">
    <div class="sidebar">
        <h1 class="logo">V-Meet</h1>
        <nav>
            <a href="/chat">Chat</a>
            <a href="/meeting">Meetings</a>
            <a href="/schedule">Schedule</a>
            <a href="/fileshare">Files</a>
            @if(!empty($sidebarContacts))
            <div style="margin-top:1.5rem;color:#cbd5e1;font-weight:600;">Contacts</div>
            <ul style="list-style:none;padding:0;margin-top:0.5rem;display:flex;flex-direction:column;gap:0.4rem;">
                @foreach($sidebarContacts as $c)
                    <li>
                        <a href="/chat?user={{ $c->id }}" style="color:#fff;text-decoration:none;background:#3a3f5a;padding:0.35rem 0.6rem;border-radius:6px;display:block;">{{ $c->name }}</a>
                    </li>
                @endforeach
            </ul>
            @endif
        </nav>
    </div>
    <div class="main-content">
        <h2>Files</h2>
        <div id="file-share-app">
            <file-share></file-share>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.jey-meet-index { display: flex; min-height: 80vh; background: #f3f6fb; font-family: 'Segoe UI', Arial, sans-serif; }
.sidebar { width: 220px; background: #2b2d42; color: #fff; padding: 2rem 1rem; display: flex; flex-direction: column; align-items: flex-start; }
.logo { font-size: 2rem; font-weight: 700; margin-bottom: 2rem; letter-spacing: 2px; }
nav { display: flex; flex-direction: column; gap: 1.2rem; width: 100%; }
nav a { color: #fff; text-decoration: none; font-size: 1.1rem; padding: 0.5rem 1rem; border-radius: 6px; transition: background 0.2s; }
nav a:hover { background: #3a3f5a; }
.main-content { flex: 1; padding: 3rem; }
</style>
@endpush
