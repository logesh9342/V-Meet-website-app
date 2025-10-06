@extends('layouts.app')

@section('content')
<div class="meet-dashboard">
    <div class="join-card">
        <h2>Join a meeting with an ID</h2>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <div class="meeting-info">
            <label for="meetingCode">Meeting ID <span class="info-icon">ℹ️</span></label>
            <input type="text" id="meetingCode" placeholder="Type a meeting ID">
        </div>

        <div class="meeting-info">
            <label for="meetingPasscode">Type a meeting passcode</label>
            <input type="text" id="meetingPasscode" placeholder="Type a meeting passcode">
        </div>

        <!-- 🚀 Backend should replace href with actual join logic -->
        <a href="{{ route('prejoin.screen') }}" class="btn join-btn" style=" background-color: blue; color: white;">Join meeting</a>
    </div>
</div>
@endsection

@push('styles')
<style>
/* ==== Layout ==== */
.meet-dashboard {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 20px;
    background: #111827; /* Dark background */
    font-family: "Segoe UI", "Poppins", sans-serif;
}
a:hover {
   
    cursor: pointer;
}
/* ==== Card ==== */
.join-card {
    background: #1f1f1f;
    padding: 30px 25px;
    border-radius: 10px;
    width: 100%;
    max-width: 400px;
    color: #e5e7eb;
    box-shadow: 0 8px 30px rgba(0,0,0,0.5);
    display: flex;
    flex-direction: column;
    gap: 18px;
}

/* ==== Heading ==== */
.join-card h2 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
    color: #f3f4f6;
    text-align: left;
}

/* ==== Labels ==== */
.meeting-info label {
    display: block;
    margin-bottom: 6px;
    font-size: 0.85rem;
    font-weight: 600;
    color: #d1d5db;
}

/* Info icon */
.info-icon {
    font-size: 0.8rem;
    color: #9ca3af;
    margin-left: 4px;
}

/* ==== Inputs ==== */
.meeting-info input {
    width: 100%;
    padding: 10px 12px;
    border-radius: 6px;
    border: 1px solid #333;
    background: #1a1a1a;
    color: #e5e7eb;
    font-size: 0.95rem;
    outline: none;
    transition: 0.3s;
}

.meeting-info input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59,130,246,0.5);
}

/* ==== Join Button ==== */
.join-btn {
    display: block;
    padding: 12px 0;
    background: #2d2d2d;
    color: #6b7280;
    font-weight: 600;
    font-size: 0.95rem;
    border-radius: 6px;
    text-align: center;
    text-decoration: none;
    cursor: not-allowed; /* 🔒 default disabled */
}

/* 🔑 Backend should enable it dynamically */
.join-btn.active {
    background: #2563eb;
    color: #fff;
    cursor: pointer;
}

/* ==== Success Alert ==== */
.alert-success {
    background: #064e3b;
    padding: 10px 14px;
    border-radius: 8px;
    font-weight: 600;
    color: #10b981;
    font-size: 0.9rem;
}
</style>
@endpush

@push('scripts')
<script>
// 👉 Backend should validate ID & Passcode before enabling join
document.addEventListener("input", function () {
    const code = document.getElementById("meetingCode").value.trim();
    const pass = document.getElementById("meetingPasscode").value.trim();
    const joinBtn = document.querySelector(".join-btn");

    if (code && pass) {
        joinBtn.classList.add("active");
        joinBtn.removeAttribute("style");
        joinBtn.setAttribute("href", "/join-meeting"); // 👉 Replace with backend route
    } else {
        joinBtn.classList.remove("active");
        joinBtn.removeAttribute("href");
    }
});
document.querySelector(".join-btn").addEventListener("click", function(e) {
    const code = document.getElementById("meetingCode").value.trim();
    const pass = document.getElementById("meetingPasscode").value.trim();
    if (!code || !pass) {
        e.preventDefault();
        alert("Please fill in both Meeting ID and Passcode before joining.");
    }
});

</script>
@endpush
