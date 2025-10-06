<!-- resources/views/meetings/prejoin.blade.php -->

@extends('layouts.app')

@section('content')
<div class="meeting-container">
    <div class="header"><h2>Meeting with Logu M</h2></div>

    <div class="content">
        <!-- Video Section -->
        <div class="video-section">
            <div class="video-preview">
                <video id="preview-video" autoplay muted playsinline></video>
                <div id="no-camera" class="no-camera" style="display:none;">📷 Camera not available</div>
            </div>
            <div class="video-controls">
                <button id="camera-btn" class="control-btn active">📹 Camera</button>
                <button id="mic-btn" class="control-btn active">🎤 Mic</button>
            </div>
        </div>

        <!-- Audio Section -->
        <div class="audio-section">
            <div class="audio-title">Audio settings</div>

            <div class="audio-option">
                <input type="radio" id="computer-audio" name="audioMode" value="use" checked>
                <label for="computer-audio">Computer audio</label>
            </div>

            <div class="device-selector">
                <div class="device-row">
                    🎤 <select id="micDevice" class="device-dropdown"><option>Loading...</option></select>
                </div>
                <div class="device-row">
                    🔊 <select id="speakerDevice" class="device-dropdown"><option>Loading...</option></select>
                </div>
            </div>

            <div class="audio-option">
                <input type="radio" id="no-audio" name="audioMode" value="none">
                <label for="no-audio">Don't use audio</label>
            </div>
        </div>
    </div>

    <div class="actions">
        <button class="btn btn-cancel">Cancel</button>
        <a href="{{ route('share.screen') }}" class="btn btn-join">Join now</a>
    </div>
</div>
@endsection

@push('styles')
<style>
:root {
    --primary: #6200ea;
    --primary-hover: #7c4dff;
    --bg-dark: #121212;
    --bg-darker: #1a1a1a;
    --card-bg: #232323;
    --text: #ffffff;
    --text-secondary: #bbbbbb;
    --border: #333333;
    --shadow: rgba(0, 0, 0, 0.5);
    --transition: all 0.2s ease;
}

* { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }

body { background: var(--bg-dark); color: var(--text); }

.meeting-container {
    width: 100%;
    max-width: 1180px;
    background: var(--bg-darker);
    border-radius: 20px;
    box-shadow: 0 12px 35px var(--shadow);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    margin: 20px auto;
}

.header { text-align:center; padding:20px; background: linear-gradient(135deg,#1f1f2e,#151530); border-bottom:1px solid var(--border);}
.header h2 { font-size:24px; font-weight:600; }

.content { display:grid; grid-template-columns:3fr 2fr; gap:24px; padding:24px; }
@media (max-width:900px) { .content { grid-template-columns:1fr; } }

.video-section { display:flex; flex-direction:column; gap:16px; }
.video-preview {
    position:relative;
    background:#2d2d2d;
    border-radius:14px;
    overflow:hidden;
    aspect-ratio:16/9;
    width:100%;
    box-shadow:0 5px 15px rgba(0,0,0,0.4);
}
#preview-video { width:100%; height:100%; object-fit:cover; }
.no-camera {
    position:absolute; top:0; left:0; width:100%; height:100%;
    display:flex; align-items:center; justify-content:center;
    background: rgba(0,0,0,0.6);
    color: var(--text-secondary);
    font-size:14px;
}

.video-controls { display:flex; align-items:center; gap:16px; padding:12px 16px; background: var(--card-bg); border-radius:10px; }
.control-btn {
    background:#333; border:none; color:white; padding:10px 16px; border-radius:8px; cursor:pointer; font-size:14px;
    display:flex; align-items:center; gap:6px; transition: var(--transition);
}
.control-btn:hover { background:#444; }
.control-btn.active { background: var(--primary); }

.audio-section {
    background: var(--card-bg);
    border-radius:14px;
    padding:20px;
    display:flex;
    flex-direction:column;
    gap:20px;
}
.audio-title { font-size:17px; font-weight:600; }
.audio-option { display:flex; align-items:center; gap:8px; }
.audio-option input[type="radio"] { accent-color: var(--primary); }
.device-selector { display:flex; flex-direction:column; gap:14px; }
.device-row {
    display:flex; align-items:center; gap:10px;
    background: rgba(255,255,255,0.05);
    padding:10px 12px;
    border-radius:8px;
}
.device-dropdown {
    flex:1;
    padding:6px 8px;
    background:#2d2d2d;
    border:1px solid var(--border);
    border-radius:6px;
    color:var(--text);
    font-size:14px;
}

.actions {
    display:flex; justify-content:center; gap:24px;
    padding:18px;
    border-top:1px solid var(--border);
    background: var(--bg-darker);
}
.btn { padding:14px 28px; border:none; border-radius:10px; cursor:pointer; font-size:16px; font-weight:600; min-width:130px; transition: var(--transition);}
.btn-cancel { background:#333; color: var(--text); }
.btn-cancel:hover { background:#444; }
.btn-join { background: var(--primary); color:#fff; }
.btn-join:hover { background: var(--primary-hover); }
</style>
@endpush

@push('scripts')
<script>
const previewVideo = document.getElementById('preview-video');
const noCameraDiv = document.getElementById('no-camera');
const cameraBtn = document.getElementById('camera-btn');
const micBtn = document.getElementById('mic-btn');
const micDevice = document.getElementById('micDevice');
const speakerDevice = document.getElementById('speakerDevice');
let currentStream = null;
let micEnabled = true;

// Start camera and microphone
async function startCamera() {
    try {
        const audioEnabled = document.querySelector('input[name="audioMode"]:checked').value !== 'none';
        const stream = await navigator.mediaDevices.getUserMedia({ video:true, audio:audioEnabled });
        previewVideo.srcObject = stream;
        currentStream = stream;
        noCameraDiv.style.display = 'none';
        await populateAudioDevices();
        if(micDevice.value) switchMic(micDevice.value);
        if(speakerDevice.value) switchSpeaker(speakerDevice.value);
    } catch(err) {
        console.error(err);
        noCameraDiv.style.display='flex';
        noCameraDiv.textContent='🚫 Camera or Mic permission denied';
        cameraBtn.classList.remove("active");
    }
}

// Stop camera
function stopCamera() {
    if(currentStream) currentStream.getTracks().forEach(track=>track.stop());
    previewVideo.srcObject = null;
    noCameraDiv.style.display='flex';
    noCameraDiv.textContent='📷 Camera off';
}

// Toggle camera
cameraBtn.addEventListener("click", () => {
    if(cameraBtn.classList.contains("active")) { stopCamera(); cameraBtn.classList.remove("active"); }
    else { startCamera(); cameraBtn.classList.add("active"); }
});

// Toggle microphone
micBtn.addEventListener("click", ()=>{
    micEnabled = !micEnabled;
    if(currentStream) currentStream.getAudioTracks().forEach(track => track.enabled = micEnabled);
    micBtn.classList.toggle("active", micEnabled);
    console.log("Microphone " + (micEnabled ? "ON" : "OFF"));
});

// Populate audio devices
async function populateAudioDevices() {
    try {
        const devices = await navigator.mediaDevices.enumerateDevices();
        const mics = devices.filter(d=>d.kind==="audioinput");
        const speakers = devices.filter(d=>d.kind==="audiooutput");

        micDevice.innerHTML = mics.length? "" : "<option>No mic found</option>";
        mics.forEach((d,i)=> micDevice.innerHTML += `<option value="${d.deviceId}">${d.label||'Microphone '+(i+1)}</option>`);

        speakerDevice.innerHTML = speakers.length? "" : "<option>No speaker</option>";
        speakers.forEach((d,i)=> speakerDevice.innerHTML += `<option value="${d.deviceId}">${d.label||'Speaker '+(i+1)}</option>`);
    } catch(err) {
        console.error(err);
        micDevice.innerHTML="<option>Mic access denied</option>";
        speakerDevice.innerHTML="<option>Speaker access denied</option>";
    }
}

// Switch microphone
async function switchMic(deviceId) {
    if(!currentStream) return;
    const audioTrack = currentStream.getAudioTracks()[0];
    if(audioTrack) audioTrack.stop();
    const newStream = await navigator.mediaDevices.getUserMedia({ video:false, audio:{deviceId:{exact:deviceId}} });
    currentStream.addTrack(newStream.getAudioTracks()[0]);
}

// Switch speaker
function switchSpeaker(deviceId) {
    if(typeof previewVideo.sinkId !== 'undefined') {
        previewVideo.setSinkId(deviceId).catch(err => console.error(err));
    }
}

// Join meeting
document.querySelector(".btn-join").addEventListener("click", ()=>{
    alert("✅ Joined meeting with camera and mic enabled (simulation)");
});

// Cancel meeting
document.querySelector(".btn-cancel").addEventListener("click", ()=>{
    stopCamera();
    window.location.href="{{ route('meeting') }}";
});

// Disable audio controls if "Don't use audio" is selected
document.querySelectorAll('input[name="audioMode"]').forEach(radio=>{
    radio.addEventListener('change', function(){
        const micRow = document.querySelector('.device-row:nth-child(1)');
        const speakerRow = document.querySelector('.device-row:nth-child(2)');
        if(this.value==='none'){
            micRow.style.opacity='0.5'; 
            speakerRow.style.opacity='0.5'; 
            micRow.style.pointerEvents='none'; 
            speakerRow.style.pointerEvents='none';
        } else {
            micRow.style.opacity='1'; 
            speakerRow.style.opacity='1'; 
            micRow.style.pointerEvents='auto'; 
            speakerRow.style.pointerEvents='auto';
        }
    });
});

window.addEventListener("DOMContentLoaded", ()=>{
    startCamera();
});
</script>
@endpush
