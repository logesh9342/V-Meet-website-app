@extends('layouts.app')

@section('content')
<div class="jey-meet-index" style="display:flex;min-height:100vh;">
    <!-- ✅ Sidebar -->
    <div class="sidebar">
        <!-- ✅ Professional Title Bar -->
        <div class="title-bar">
            <span class="title-text">V-Meet</span>
        </div>

        <!-- ✅ User ID -->
        <div class="user-id">
            ID: {{ Auth::user()->user_id ?? '-----' }}
        </div>

        <!-- ✅ Navigation -->
        <nav>
            <a href="/chat" class="nav-link">
                <i data-lucide="message-square"></i>
                <span>Chat</span>
            </a>
            <a href="/meeting" class="nav-link">
                <i data-lucide="video"></i>
                <span>Meetings</span>
            </a>
            <a href="/schedule" class="nav-link">
                <i data-lucide="calendar"></i>
                <span>Schedule</span>
            </a>
            <a href="/fileshare" class="nav-link">
                <i data-lucide="folder"></i>
                <span>Files</span>
            </a>

            <!-- ✅ Logout -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <i data-lucide="log-out"></i> <span>Logout</span>
                </button>
            </form>

            <!-- ✅ Contacts -->
            @if(!empty($sidebarContacts))
            <div class="contacts-label">Contacts</div>
            <ul class="contacts-list">
                @foreach($sidebarContacts as $c)
                    <li>
                        <a href="/chat?user={{ $c->id }}" class="contact-link">
                            <i data-lucide="user"></i> {{ $c->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
            @endif
        </nav>
    </div>

    <!-- ✅ Main Content -->
    <div class="main-content" style="flex:1;padding:2rem;background:#f3f6fb;">
        <h2 style="margin-bottom:1.5rem;">Welcome, {{ Auth::user()->name }} to Jey Meet</h2>

        <!-- Dashboard Cards -->
        <div style="display:flex;gap:2rem;margin-bottom:2rem;">
            <a href="/chat" class="card">
                <span style="font-size:2.5rem;">💬</span>
                <div class="card-title">Chat</div>
                <div class="card-sub">Message and collaborate in real time</div>
            </a>
            <a href="/meeting" class="card">
                <span style="font-size:2.5rem;">📅</span>
                <div class="card-title">Meetings</div>
                <div class="card-sub">Schedule and join video meetings</div>
            </a>
            <a href="/schedule" class="card">
                <span style="font-size:2.5rem;">🗓️</span>
                <div class="card-title">Schedule</div>
                <div class="card-sub">Plan your events and reminders</div>
            </a>
            <a href="/fileshare" class="card">
                <span style="font-size:2.5rem;">📁</span>
                <div class="card-title">Files</div>
                <div class="card-sub">Share and manage your files</div>
            </a>
        </div>

        <!-- Quick Actions -->
        <div style="display:flex;gap:2rem;margin-bottom:2rem;">
            <a href="/meeting" class="btn-blue">Start Meeting</a>
            <a href="/chat" class="btn-green">New Chat</a>
            <a href="/fileshare" class="btn-orange">Share File</a>
        </div>

        <!-- Tips Box -->
        <div class="tips-box">
            <h3 style="margin-bottom:1rem;color:#2563eb;">Tips to get started</h3>
            <ul style="color:#374151;font-size:1.05rem;line-height:2;">
                <li>💬 Start a chat with your team instantly</li>
                <li>📅 Schedule or join meetings with one click</li>
                <li>🗓️ Set reminders for important events</li>
                <li>📁 Share files securely and easily</li>
            </ul>
        </div>
    </div>
</div>

<!-- ✅ Lucide Icons -->
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<script> lucide.createIcons(); </script>

<!-- ✅ Styles -->
<style>
    body {
        margin: 0;
        font-family: 'Inter', sans-serif;
        
        
    }

    /* Sidebar */
    .sidebar {
        background: #1f2233;
        padding: 1.5rem;
        width: 250px;
        min-height: 100vh;
    }

    /* Title bar */
    .title-bar {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        padding: 0.8rem 1rem;
        border-radius: 12px;
        text-align: center;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.25);
    }
    .title-text {
        font-size: 1.3rem;
        font-weight: 700;
        color: #fff;
        letter-spacing: 0.5px;
    }

    .user-id {
        margin-bottom: 1.5rem;
        font-size: 1.05rem;
        color: #f59e42;
        font-weight: 600;
        text-align: center;
    }

    /* Nav links */
    .nav-link {
        color: #fff;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        font-size: 1rem;
        padding: 0.6rem 0.8rem;
        border-radius: 8px;
        transition: background 0.2s;
    }
    .nav-link:hover {
        background: #2e324a;
    }

    /* Logout button */
    .logout-btn {
        background: #ef4444;
        color: #fff;
        border: none;
        width: 100%;
        padding: 0.6rem;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
        font-size: 1rem;
        margin-top: 1rem;
    }
    .logout-btn:hover {
        background: #dc2626;
    }

    /* Contacts */
    .contacts-label {
        margin-top: 1.5rem;
        color: #cbd5e1;
        font-weight: 600;
    }
    .contacts-list {
        list-style: none;
        padding: 0;
        margin-top: 0.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
    }
    .contact-link {
        color: #fff;
        text-decoration: none;
        background: #3a3f5a;
        padding: 0.45rem 0.7rem;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .contact-link:hover {
        background: #4b526e;
    }

    /* Cards */
    .card {
        flex: 1;
        background: #e0e7ef;
        padding: 2rem;
        border-radius: 12px;
        text-align: center;
        text-decoration: none;
        box-shadow: 0 2px 8px #d1d9e6;
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-3px);
    }
    .card-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-top: 1rem;
    }
    .card-sub {
        color: #6b7280;
        margin-top: 0.5rem;
    }

    /* Buttons */
    .btn-blue, .btn-green, .btn-orange {
        padding: 1rem 2rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        color: #fff;
    }
    .btn-blue { background: #3b82f6; }
    .btn-blue:hover { background: #2563eb; }
    .btn-green { background: #10b981; }
    .btn-green:hover { background: #059669; }
    .btn-orange { background: #f59e42; }
    .btn-orange:hover { background: #d97706; }

    /* Tips box */
    .tips-box {
        background: #fff;
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
</style>
@endsection

