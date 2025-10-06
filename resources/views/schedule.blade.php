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


    <!-- Main Content -->
    <div class="main-content">
        <header class="calendar-header">
            <div>JEY CALENDAR</div>
            <div class="nav-buttons">
                <button onclick="calendar.changeView('dayGridMonth')">Month</button>
                <button onclick="calendar.changeView('timeGridWeek')">Week</button>
                <button onclick="calendar.changeView('timeGridDay')">Day</button>
                <button onclick="calendar.today()">Today</button>
            </div>
        </header>

        <div class="wrapper">
            <!-- Sidebar Tools -->
            <div class="calendar-tools">
                <h3>Mini Calendar</h3>
                <div class="mini-calendar">
                    <input type="date" id="miniCal">
                </div>

                <div class="form">
                    <h4>Add Meeting</h4>
                    <input id="title" placeholder="Event Title">
                    <input type="date" id="date">
                    <input type="time" id="start">
                    <input type="time" id="end">
                    <select id="color">
                        <option value="fc-event-green">Green</option>
                        <option value="fc-event-blue">Blue</option>
                        <option value="fc-event-orange">Orange</option>
                        <option value="fc-event-red">Red</option>
                        <option value="fc-event-purple">Purple</option>
                    </select>
                    <button onclick="addEvent()">Add Event</button>
                </div>
            </div>

            <!-- Calendar -->
            <div id="calendar"></div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">

<style>
.jey-meet-index { display: flex; min-height: 100vh; background: #f3f6fb; font-family: 'Segoe UI', Arial, sans-serif; }


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

/* Main content */
.main-content { flex: 1; padding: 1rem 2rem; }

/* Header */
.calendar-header { background: linear-gradient(135deg,#0b6470,#0ba29d); color:#fff;
    display:flex; justify-content:space-between; align-items:center;
    padding:15px 25px; font-size:24px; font-weight:700;
    box-shadow:0 4px 12px rgba(0,0,0,.3); border-radius:12px; margin-bottom:20px; }
.nav-buttons button { background:#fff; color:#0b6470; border:1px solid #0b6470;
    padding:6px 12px; margin-left:6px; border-radius:6px; font-weight:600; cursor:pointer; }
.nav-buttons button:hover { background:#0b6470; color:#fff; }

/* Layout wrapper */
.wrapper { display:flex; gap:20px; }

/* Tools sidebar */
.calendar-tools { width:240px; background:#fff; border-radius:12px; padding:15px;
    box-shadow:0 6px 15px rgba(0,0,0,.2); text-align:center; }
.calendar-tools h3 { margin:10px 0; }
.mini-calendar, .form { background:#f9ffff; border-radius:10px; padding:12px; margin-bottom:15px;
    box-shadow:0 4px 10px rgba(0,0,0,.15); }
.mini-calendar input, .form input, .form select, .form button {
    width:90%; margin:6px auto; padding:7px; border-radius:6px; border:1px solid #ccc;
    font-size:14px; text-align:center; }
.form button { background:#0b6470; color:#fff; border:none; font-weight:bold; cursor:pointer; }
.form button:hover { background:#09585b; }

/* Calendar */
#calendar { flex:1; background:#fff; border-radius:12px; padding:10px;
    box-shadow:0 10px 20px rgba(0,0,0,.2); }
.fc-event { border-radius:6px!important; box-shadow:0 2px 6px rgba(0,0,0,.2); color:#fff!important; }
.fc-event-green { background:#4CAF50; }
.fc-event-blue { background:#2196F3; }
.fc-event-orange { background:#FF9800; }
.fc-event-red { background:#F44336; }
.fc-event-purple { background:#9C27B0; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('calendar');
    window.calendar = new FullCalendar.Calendar(el, {
        initialView: 'dayGridMonth',
        headerToolbar: false,
        editable: true,
        selectable: true,
        events: []
    });
    calendar.render();
});

function addEvent() {
    let t = title.value, d = date.value, s = start.value, e = end.value, c = color.value;
    if (!t || !d || !s || !e) { alert('Fill all fields!'); return }
    calendar.addEvent({ title: t, start: d+'T'+s, end: d+'T'+e, classNames:[c] });
    title.value = date.value = start.value = end.value = '';
}

miniCal.addEventListener('change', () => calendar.gotoDate(miniCal.value));
</script>
@endpush
