@extends('layouts.app')

@section('content')
<div class="jey-meet-index">
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="title-bar">
      <span class="title-text">v-Meet</span>
    </div>

    <div class="user-id">
      ID: {{ Auth::user()->user_id ?? '-----' }}
    </div>

    <nav>
      <a href="/chat" class="nav-link"><i data-lucide="message-square"></i><span>Chat</span></a>
      <a href="/meeting" class="nav-link"><i data-lucide="video"></i><span>Meetings</span></a>
      <a href="/schedule" class="nav-link"><i data-lucide="calendar"></i><span>Schedule</span></a>
      <a href="/fileshare" class="nav-link"><i data-lucide="folder"></i><span>Files</span></a>

      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-btn">
          <i data-lucide="log-out"></i> <span>Logout</span>
        </button>
      </form>

      @if(!empty($sidebarContacts))
      <div class="contacts-label">Contacts</div>
      <ul class="contacts-list">
        @foreach($sidebarContacts as $c)
        <li><a href="/chat?user={{ $c->id }}" class="contact-link"><i data-lucide="user"></i>{{ $c->name }}</a></li>
        @endforeach
      </ul>
      @endif
    </nav>
  </aside>

  <!-- Main Dashboard -->
  <main class="meet-dashboard">
    <header>Jey Meeting Dashboard</header>

    <!-- Action Buttons -->
    <div class="buttons">
      <button type="button" class="btn btn-primary" onclick="openModal()">Create a meeting link</button>
      <button class="btn btn-secondary" onclick="openScheduleForm()">📅 Schedule a meeting</button>
      <a href="{{route('join.meeting')}}" class="btn btn-primary" >🔑 Join with a meeting ID</a>
    </div>

    <!-- Scheduled Meetings Section -->
    <section>
      <h3>📌 Scheduled Meetings</h3>
      <div class="cards" id="meetingList">
        <div class="card">
          <p class="highlight">No meetings scheduled</p>
        </div>
      </div>
    </section>

    <!-- Meeting Links Section -->
    <section>
      <h3>🔗 Meeting Links</h3>
      <div class="cards" id="linkList">
        <div class="card">
          <p class="highlight">Loading your meetings...</p>
        </div>
      </div>
    </section>
  </main>
</div>

<!-- ✅ Popup Modal -->
<div id="meetingModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center hidden z-50">
  <div class="modal-box animate-fadeIn">

    <!-- Close Button -->
    <button onclick="closeModal()" class="modal-close">&times;</button>

    <!-- Title -->
    <h2 class="modal-title">📅 Create a Meeting</h2>

    <!-- Form -->
    <form action="#" method="" class="modal-form">
      @csrf

      <!-- Meeting Name -->
      <div class="form-group">
        <label for="meeting_name" class="form-label">Meeting Name</label>
        <input type="text" id="meeting_name" name="meeting_name" placeholder="Enter meeting title..."
          class="form-input">
      </div>

      <!-- Auto Generated Link -->
      <div class="form-group">
        <label for="meeting_link" class="form-label">Meeting Link</label>
        <div class="form-link-box">
          <input type="text" id="meeting_link" name="meeting_link"
            value="{{ url('/meet/' . Str::random(10)) }}" readonly class="form-link-input">
          <button type="button" onclick="copyLink()" class="form-link-btn">Copy</button>
        </div>
      </div>

      <!-- Buttons -->
      <div class="form-actions">
        <button type="button" onclick="closeModal()" class="btn-cancel">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="showPreJoinScreen()">create</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('styles')
<style>
  /* keep sidebar + dashboard same */
  body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: #f3f4f6;
   
  }

  .jey-meet-index {
    display: flex;
    height: 100vh;
  }

  .sidebar {
    width: 260px;
    background: linear-gradient(180deg, #1f2233, #111320);
    color: white;
    padding: 25px 20px;
    display: flex;
    flex-direction: column;
    gap: 20px;
    box-shadow: 2px 0 15px rgba(0, 0, 0, 0.3);
  }

  .title-bar {
    background: linear-gradient(135deg, #2563eb, #1e40af);
    padding: 1rem;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 10px rgba(0, 0, 0, .3);
  }

  .title-text {
    font-size: 1.4rem;
    font-weight: 700;
    color: #fff;
  }

  .user-id {
    color: #f59e42;
    font-weight: 600;
    text-align: center;
    font-size: .95rem;
  }

  .nav-link {
    color: #d1d5db;
    display: flex;
    align-items: center;
    gap: .8rem;
    padding: .8rem;
    border-radius: 10px;
    text-decoration: none;
    transition: all .3s;
    font-weight: 500;
  }

  .nav-link:hover {
    background: #2d3748;
    color: #fff;
  }

  .logout-btn {
    background: #ef4444;
    color: #fff;
    border: none;
    padding: .8rem;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    transition: background .3s;
    margin-top: 10px;
  }

  .logout-btn:hover {
    background: #dc2626;
  }

  .meet-dashboard {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #fff;
    padding: 30px;
    overflow-y: auto;
  }

  header {
    padding: 20px;
    background: linear-gradient(90deg, #6366f1, #3b82f6);
    color: #fff;
    font-size: 22px;
    font-weight: 600;
    border-radius: 12px;
    margin-bottom: 25px;
    box-shadow: 0 4px 10px rgba(99, 102, 241, .4);
  }

  .buttons {
    display: flex;
    gap: 15px;
    margin: 20px 0 30px 0;
    flex-wrap: wrap;
  }

  .btn {
    padding: 12px 20px;
    border: none;
    border-radius: 10px;
    font-size: 1rem;
    cursor: pointer;
    transition: all .3s;
    font-weight: 500;
  }

  .btn-primary {
    background: linear-gradient(90deg, #4f46e5, #6366f1);
    color: #fff;
    box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
  }

  .btn-primary:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 20px rgba(79, 70, 229, .5);
  }

  .btn-secondary {
    background: #222;
    color: #ddd;
    border: 1px solid #444;
  }

  .btn-secondary:hover {
    background: #333;
    color: #fff;
    transform: translateY(-3px) scale(1.05);
  }

  section {
    margin-bottom: 30px;
  }

  section h3 {
    margin-bottom: 15px;
    font-size: 1.2rem;
    color: #222;
    font-weight: 600;
  }

  .cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
  }

  .card {
    background: #1e1e1e;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, .4);
    color: #fff;
    transition: all .3s;
  }

  .card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 25px rgba(0, 0, 0, .5);
  }

  .highlight {
    text-align: center;
    color: #bbb;
  }

  /* 🔽 MODAL Styling */
  .modal-box {
    background: #fff;
    border-radius: 18px;
    padding: 2rem;
    width: 100%;
    max-width: 480px;
    position: relative;
    box-shadow: 0 10px 30px rgba(0, 0, 0, .25);
  }

  .modal-close {
    position: absolute;
    top: 12px;
    right: 15px;
    background: none;
    border: none;
    font-size: 26px;
    font-weight: bold;
    color: #888;
    cursor: pointer;
    transition: color .3s;
  }

  .modal-close:hover {
    color: #000;
  }

  .modal-title {
    font-size: 1.5rem;
    font-weight: 700;
    text-align: center;
    color: #374151;
    margin-bottom: 1.5rem;
  }

  .modal-form {
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
  }

  .form-group {
    display: flex;
    flex-direction: column;
    gap: .4rem;
  }

  .form-label {
    font-size: .9rem;
    font-weight: 600;
    color: #374151;
  }

  .form-input {
    padding: .75rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    font-size: .95rem;
    outline: none;
    transition: border .3s, box-shadow .3s;
  }

  .form-input:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .2);
  }

  .form-link-box {
    display: flex;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    overflow: hidden;
  }

  .form-link-input {
    flex: 1;
    padding: .75rem 1rem;
    background: #f9fafb;
    color: #6b7280;
    font-size: .9rem;
    border: none;
  }

  .form-link-btn {
    background: #4f46e5;
    color: #fff;
    padding: 0 1rem;
    font-size: .9rem;
    cursor: pointer;
    transition: background .3s;
    border: none;
  }

  .form-link-btn:hover {
    background: #3730a3;
  }

  .form-actions {
    display: flex;
    justify-content: flex-end;
    gap: .8rem;
    margin-top: .5rem;
  }

  .btn-cancel {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
    padding: .6rem 1.2rem;
    border-radius: 8px;
    font-size: .9rem;
    cursor: pointer;
    transition: background .3s;
  }

  .btn-cancel:hover {
    background: #e5e7eb;
  }

  .btn-create {
    background: linear-gradient(90deg, #4f46e5, #6366f1);
    color: #fff;
    padding: .6rem 1.4rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: .9rem;
    cursor: pointer;
    border: none;
    transition: transform .2s, box-shadow .3s;
  }

  .btn-create:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(79, 70, 229, .4);
  }

  /* Modal Animation */
  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: scale(0.95);
    }

    to {
      opacity: 1;
      transform: scale(1);
    }
  }

  .animate-fadeIn {
    animation: fadeIn 0.25s ease-out;
  }
</style>

<script>
  function openModal() {
    document.getElementById('meetingModal').classList.remove('hidden');
  }

  function closeModal() {
    document.getElementById('meetingModal').classList.add('hidden');
  }

  function copyLink() {
    const linkInput = document.getElementById('meeting_link');
    linkInput.select();
    linkInput.setSelectionRange(0, 99999);
    document.execCommand("copy");
    alert("✅ Link copied:\n" + linkInput.value);
  }
  
  function showPreJoinScreen() {
  // Open prejoin screen in same tab (you can also open in modal if preferred)
  window.location.href = "{{ route('prejoin.screen') }}";
  }
</script>  
@endpush