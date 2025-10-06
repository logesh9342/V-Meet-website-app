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
        </nav>
    </div>
    <div class="main-content">
        <h2>Welcome to Jey Meet</h2>
        <p>Collaborate, chat, schedule meetings, and share files just like Microsoft Teams/Meet.</p>
        <div class="features">
            <div class="feature-card">
                <h3>Chat</h3>
                <p>Real-time group and meeting chat rooms.</p>
                <a href="/chat" class="btn">Go to Chat</a>
            </div>
            <div class="feature-card">
                <h3>Meetings</h3>
                <p>Video/audio meetings with scheduling.</p>
                <a href="/meeting" class="btn">Go to Meetings</a>
            </div>
            <div class="feature-card">
                <h3>Schedule</h3>
                <p>Plan and join meetings easily.</p>
                <a href="/schedule" class="btn">Go to Schedule</a>
            </div>
            <div class="feature-card">
                <h3>Files</h3>
                <p>Share and access files in meetings and chats.</p>
                <a href="/fileshare" class="btn">Go to Files</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.jey-meet-index {
    display: flex;
    min-height: 80vh;
    background: #f3f6fb;
    font-family: 'Segoe UI', Arial, sans-serif;
}
.sidebar {
    width: 220px;
    background: #2b2d42;
    color: #fff;
    padding: 2rem 1rem;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}
.logo {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 2rem;
    letter-spacing: 2px;
}
nav {
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
    width: 100%;
}
nav a {
    color: #fff;
    text-decoration: none;
    font-size: 1.1rem;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    transition: background 0.2s;
}
nav a:hover {
    background: #3a3f5a;
}
.main-content {
    flex: 1;
    padding: 3rem;
}
.features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}
.feature-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}
.feature-card h3 {
    margin-bottom: 0.5rem;
    color: #2b2d42;
}
.feature-card p {
    margin-bottom: 1rem;
    color: #555;
}
.btn {
    background: #2b2d42;
    color: #fff;
    padding: 0.5rem 1.2rem;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: background 0.2s;
}
.btn:hover {
    background: #3a3f5a;
}
</style>
@endpush
