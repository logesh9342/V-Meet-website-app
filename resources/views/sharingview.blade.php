@extends('layouts.app')

@section('content')
<div class="meeting-container">
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="meeting-title">Meeting with Team 🚀</div>
        <div id="time">10:44 AM</div>
    </div>

    <!-- Video Grid -->
    <div class="video-grid" id="videoGrid">
        <div class="video-tile main-video" id="mainVideoTile">
            <video id="myVideo" autoplay muted playsinline></video>
            <div class="participant-name">You</div>
        </div>
        <div class="video-tile">
            <img src="https://via.placeholder.com/400x250/000000/ffffff?text=Participant+1" />
            <div class="participant-name">Logu M</div>
        </div>
        <div class="video-tile">
            <img src="https://via.placeholder.com/400x250/000000/ffffff?text=Participant+2" />
            <div class="participant-name">John D</div>
        </div>
    </div>

    <!-- Control Bar -->
    <div class="control-bar">
        <button class="control-btn" id="micBtn" title="Mute/Unmute">
            <svg viewBox="0 0 24 24"><path d="M12 14a3 3 0 0 0 3-3V5a3 3 0 0 0-6 0v6a3 3 0 0 0 3 3zm5-3a5 5 0 0 1-10 0H5a7 7 0 0 0 14 0h-2z"/></svg>
        </button>

        <button class="control-btn" id="camBtn" title="Turn camera on/off">
            <svg viewBox="0 0 24 24"><path d="M17 10.5V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3.5l4 4v-11l-4 4z"/></svg>
        </button>

        <button class="control-btn" id="shareBtn" title="Share screen">
            <svg viewBox="0 0 24 24"><path d="M21 17a1 1 0 0 0 1-1V5a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h18zM4 5h16v10H4V5zm5 14h6v2H9v-2z"/></svg>
        </button>

        <button class="control-btn leave" id="leaveBtn" title="Leave Call">
            <svg viewBox="0 0 24 24"><path d="M21 8.5l-1.5 1.5a10.46 10.46 0 0 0-15 0L3 8.5A12.46 12.46 0 0 1 12 5a12.46 12.46 0 0 1 9 3.5zM7.88 13.12a8.46 8.46 0 0 1 8.24 0l-2.12 2.12a4.46 4.46 0 0 0-4 0l-2.12-2.12z"/></svg>
        </button>
    </div>
</div>
@endsection

@push('styles')
<style>
:root {
    --bg-dark: #202124;
    --bg-darker: #171717;
    --btn-bg: #3c4043;
    --btn-hover: #5f6368;
    --danger: #ea4335;
    --danger-hover: #d93025;
    --text-light: #fff;
}

body {
    margin: 0;
    font-family: "Google Sans", "Segoe UI", Roboto, sans-serif;
    background: var(--bg-dark);
    color: var(--text-light);
}

.meeting-container {
    display: flex;
    flex-direction: column;
    height: 100vh;
}

.top-bar {
    height: 50px;
    background: var(--bg-darker);
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 1.5rem;
    border-bottom: 1px solid #2a2a2a;
    font-weight: 500;
}

.video-grid {
    flex: 1;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 8px;
    padding: 10px;
    transition: all 0.3s ease;
}

.video-tile {
    position: relative;
    background: #000;
    border-radius: 12px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.video-tile video,
.video-tile img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.3s ease;
}

.participant-name {
    position: absolute;
    bottom: 8px;
    left: 8px;
    background: rgba(0,0,0,0.65);
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 0.75rem;
}

.control-bar {
    height: 80px;
    background: var(--bg-darker);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    border-top: 1px solid #2a2a2a;
}

.control-btn {
    background: var(--btn-bg);
    border: none;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s ease;
}

.control-btn svg {
    width: 24px;
    height: 24px;
    fill: var(--text-light);
}

.control-btn:hover {
    background: var(--btn-hover);
}

.control-btn.leave {
    background: var(--danger);
    width: 90px;
    border-radius: 28px;
}

.control-btn.leave:hover {
    background: var(--danger-hover);
}

.control-btn.active {
    background: var(--danger);
}

.main-video.fullscreen {
    grid-column: 1 / -1;
    grid-row: 1 / -1;
    height: 100%;
}
</style>
@endpush

@push('scripts')
<script>
const videoEl = document.getElementById("myVideo");
let currentStream;

// Live Clock
function updateTime() {
    const now = new Date();
    document.getElementById("time").textContent = now.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
}
setInterval(updateTime, 1000);
updateTime();

// Start Camera
async function startCamera() {
    try {
        currentStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
        videoEl.srcObject = currentStream;
    } catch (err) {
        console.error("Camera/Mic error:", err);
    }
}
startCamera();

// Mic toggle
document.getElementById("micBtn").addEventListener("click", function(){
    if(!currentStream) return;
    const audioTracks = currentStream.getAudioTracks();
    audioTracks.forEach(track => track.enabled = !track.enabled);
    this.classList.toggle("active", !audioTracks[0].enabled);
});

// Camera toggle
document.getElementById("camBtn").addEventListener("click", function(){
    if(!currentStream) return;
    const videoTracks = currentStream.getVideoTracks();
    videoTracks.forEach(track => track.enabled = !track.enabled);
    this.classList.toggle("active", !videoTracks[0].enabled);
});

// Screen share toggle with full-width
const shareBtn = document.getElementById("shareBtn");
let isSharing = false;
let cameraStream;
const mainVideoTile = document.getElementById("mainVideoTile");

shareBtn.addEventListener("click", async () => {
    if(!isSharing) {
        try {
            cameraStream = currentStream;
            const screenStream = await navigator.mediaDevices.getDisplayMedia({ video: true });
            currentStream = screenStream;
            videoEl.srcObject = screenStream;
            isSharing = true;
            shareBtn.classList.add("active");
            mainVideoTile.classList.add("fullscreen");

            screenStream.getVideoTracks()[0].addEventListener("ended", stopSharing);
        } catch(err) {
            console.error("Screen share error:", err);
        }
    } else stopSharing();
});

function stopSharing() {
    if(cameraStream) {
        currentStream = cameraStream;
        videoEl.srcObject = cameraStream;
    }
    isSharing = false;
    shareBtn.classList.remove("active");
    mainVideoTile.classList.remove("fullscreen");
}

// Leave button
document.getElementById("leaveBtn").addEventListener("click", () => {
    if(currentStream) {
        currentStream.getTracks().forEach(track => track.stop());
    }
    window.location.href = "/"; // Redirect to home or meetings list
});
</script>
@endpush
