
<template>
  <div class="meeting-container">
    <h2>Video/Audio Meeting Room</h2>
    <div style="margin-bottom:1rem;">
      <label style="font-weight:600;">Meeting Room Code:</label>
      <input v-model="roomCode" placeholder="Enter or paste room code" style="margin-left:1rem;padding:0.3rem 0.7rem;border-radius:6px;border:1px solid #e0e7ef;" />
      <span style="margin-left:1rem;font-size:0.95rem;color:#6b7280;">Status: <strong :style="{color: statusColor}">{{ statusText }}</strong></span>
    </div>
    <div>
      <video ref="localVideo" autoplay playsinline muted class="video"></video>
      <video ref="remoteVideo" autoplay playsinline class="video"></video>
    </div>
  <div style="margin-top:0.75rem;">
    <button @click="startMeeting" :disabled="busy || connected">Start Meeting</button>
    <button @click="joinMeeting" :disabled="busy || connected">Join Meeting</button>
    <button @click="shareScreen" :disabled="!connected" style="background:#10b981;color:#fff;">Share Screen</button>
    <button @click="toggleMute" :disabled="!connected">{{ muted ? 'Unmute' : 'Mute' }}</button>
    <button @click="toggleVideo" :disabled="!connected">{{ videoOff ? 'Video On' : 'Video Off' }}</button>
    <button @click="leave" :disabled="!connected" style="background:#ef4444;color:#fff;">Leave</button>
  </div>

    <!-- Meeting Room Chat -->
    <div style="margin-top:2rem;background:#f9f9f9;padding:1rem;border-radius:8px;max-width:500px;margin-left:auto;margin-right:auto;">
      <h3 style="margin-bottom:1rem;color:#2563eb;">Meeting Chat</h3>
      <div style="max-height:180px;overflow-y:auto;margin-bottom:1rem;">
        <div v-for="msg in messages" :key="msg.id" style="margin-bottom:0.5rem;">
          <strong>{{ msg.user?.name || 'User' }}:</strong> {{ msg.content }}
        </div>
      </div>
      <form @submit.prevent="sendMessage" style="display:flex;gap:0.5rem;">
        <input v-model="newMessage" placeholder="Type a message..." style="flex:1;padding:0.5rem;border-radius:6px;border:1px solid #e0e7ef;" />
        <button type="submit" style="background:#3b82f6;color:#fff;padding:0.5rem 1rem;border-radius:6px;font-weight:500;border:none;">Send</button>
      </form>
    </div>
  </div>
  <!-- Toasts -->
  <div v-if="toasts.length" style="position:fixed;right:1rem;bottom:1rem;display:flex;flex-direction:column;gap:0.5rem;z-index:9999;">
    <div v-for="t in toasts" :key="t.id" :style="{background:t.bg,color:'#fff',padding:'0.5rem 0.75rem',borderRadius:'6px',minWidth:'200px',boxShadow:'0 2px 8px rgba(0,0,0,0.15)'}">
      {{ t.msg }}
    </div>
  </div>
</template>

<script setup>


import { ref, onMounted, onBeforeUnmount } from 'vue';
import Peer from 'simple-peer';

const localVideo = ref(null);
const remoteVideo = ref(null);
const roomCode = ref('');
let peer = null;
const busy = ref(false);
const connected = ref(false);
const statusText = ref('idle');
const statusColor = ref('#6b7280');
const muted = ref(false);
const videoOff = ref(false);

// Chat state
const messages = ref([]);
const newMessage = ref('');
let chatPoller = null;
let signalPoller = null;
let lastSignalLen = 0;

const fetchMessages = async () => {
  if (!roomCode.value) return;
  const res = await fetch(`/api/meeting/${roomCode.value}/messages`);
  messages.value = await res.json();
};

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

const sendMessage = async () => {
  if (!newMessage.value || !roomCode.value) return;
  await fetch(`/api/meeting/${roomCode.value}/messages`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken || ''
    },
    body: JSON.stringify({ content: newMessage.value })
  });
  newMessage.value = '';
  fetchMessages();
};

onMounted(() => {
  // Preload saved room code
  try {
    const saved = localStorage.getItem('jeymeet_room');
    if (saved) roomCode.value = saved;
  } catch (_) {}
  chatPoller = setInterval(fetchMessages, 2000);
  signalPoller = setInterval(fetchSignals, 1500);
});

onBeforeUnmount(() => {
  if (chatPoller) clearInterval(chatPoller);
  if (signalPoller) clearInterval(signalPoller);
  stopMediaAndPeer();
});


// Video/Audio logic
const startMeeting = async () => {
  if (!roomCode.value) {
    alert('Please enter a meeting room code.');
    return;
  }
  if (connected.value || busy.value) return;
  setStatus('starting', '#2563eb');
  busy.value = true;
  const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
  localVideo.value.srcObject = stream;
  peer = new Peer({ initiator: true, trickle: false, stream });
  try { localStorage.setItem('jeymeet_room', roomCode.value); } catch (_) {}
  peer.on('signal', async data => {
    await fetch('/api/signaling/send', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken || ''
      },
      body: JSON.stringify({ room_id: roomCode.value, type: 'offer', data })
    });
  });
  peer.on('stream', remoteStream => {
    remoteVideo.value.srcObject = remoteStream;
    setStatus('connected', '#10b981');
    connected.value = true;
    busy.value = false;
    pausePolling();
  });
  peer.on('connect', () => {
    setStatus('connected', '#10b981');
    connected.value = true;
    busy.value = false;
    // clear accumulated signals once connected
    clearSignals();
    pausePolling();
  });
  peer.on('close', () => {
    setStatus('disconnected', '#ef4444');
    connected.value = false;
  });
  peer.on('error', (err) => {
    console.error('peer error', err);
    setStatus('error', '#ef4444');
    busy.value = false;
  });
};

const joinMeeting = async () => {
  if (!roomCode.value) {
    alert('Please enter a meeting room code.');
    return;
  }
  if (connected.value || busy.value) return;
  setStatus('joining', '#2563eb');
  busy.value = true;
  const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
  localVideo.value.srcObject = stream;
  peer = new Peer({ initiator: false, trickle: false, stream });
  try { localStorage.setItem('jeymeet_room', roomCode.value); } catch (_) {}
  peer.on('signal', async data => {
    await fetch('/api/signaling/send', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken || ''
      },
      body: JSON.stringify({ room_id: roomCode.value, type: 'answer', data })
    });
  });
  peer.on('stream', remoteStream => {
    remoteVideo.value.srcObject = remoteStream;
    setStatus('connected', '#10b981');
    connected.value = true;
    busy.value = false;
    pausePolling();
  });
  peer.on('connect', () => {
    setStatus('connected', '#10b981');
    connected.value = true;
    busy.value = false;
    // clear accumulated signals once connected
    clearSignals();
    pausePolling();
  });
  peer.on('close', () => {
    setStatus('disconnected', '#ef4444');
    connected.value = false;
  });
  peer.on('error', (err) => {
    console.error('peer error', err);
    setStatus('error', '#ef4444');
    busy.value = false;
  });
};

// Screen sharing logic
const shareScreen = async () => {
  if (!peer) {
    alert('Start or join a meeting first.');
    return;
  }
  try {
    const screenStream = await navigator.mediaDevices.getDisplayMedia({ video: true });
    // Replace video track in peer connection
    const sender = peer._pc.getSenders().find(s => s.track && s.track.kind === 'video');
    if (sender) {
      sender.replaceTrack(screenStream.getVideoTracks()[0]);
      localVideo.value.srcObject = screenStream;
    }
    // When screen sharing ends, restore camera
    const screenTrack = screenStream.getVideoTracks()[0];
    screenTrack.addEventListener('ended', async () => {
      try {
        const camStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
        const camTrack = camStream.getVideoTracks()[0];
        sender && sender.replaceTrack(camTrack);
        localVideo.value.srcObject = camStream;
        toast('Screen sharing ended', '#6b7280');
      } catch (e) { toast('Failed to restore camera', '#ef4444'); }
    });
    toast('Screen sharing started', '#10b981');
  } catch (err) {
    toast('Screen sharing failed: ' + err.message, '#ef4444');
  }
};

const fetchSignals = async () => {
  if (!roomCode.value) {
    return; // no room yet
  }
  const res = await fetch(`/api/signaling/receive?room_id=${roomCode.value}`);
  const data = await res.json();
  const msgs = Array.isArray(data) ? data : (data?.messages || []);
  const newMsgs = msgs.slice(lastSignalLen);
  newMsgs.forEach(msg => {
    if (peer) {
      peer.signal(msg.data);
    }
  });
  lastSignalLen = msgs.length;
};

async function clearSignals() {
  try {
    await fetch('/api/signaling/clear', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken || ''
      },
      body: JSON.stringify({ room_id: roomCode.value })
    });
    lastSignalLen = 0;
  } catch (e) {
    console.warn('Failed to clear signals', e);
  }
}

function setStatus(text, color) {
  statusText.value = text;
  statusColor.value = color;
}

function stopMediaAndPeer() {
  try {
    const lv = localVideo.value?.srcObject;
    lv && lv.getTracks().forEach(t => t.stop());
  } catch (_) {}
  try {
    const rv = remoteVideo.value?.srcObject;
    rv && rv.getTracks().forEach(t => t.stop());
  } catch (_) {}
  try {
    peer && peer.destroy();
  } catch (_) {}
  peer = null;
  connected.value = false;
  busy.value = false;
  setStatus('idle', '#6b7280');
}

function leave() {
  stopMediaAndPeer();
  resumePolling();
  toast('Left meeting', '#6b7280');
}

function toggleMute() {
  try {
    const stream = localVideo.value?.srcObject;
    stream?.getAudioTracks().forEach(t => t.enabled = !t.enabled);
    muted.value = !muted.value;
  } catch (e) { toast('Failed to toggle mute', '#ef4444'); }
}

function toggleVideo() {
  try {
    const stream = localVideo.value?.srcObject;
    stream?.getVideoTracks().forEach(t => t.enabled = !t.enabled);
    videoOff.value = !videoOff.value;
  } catch (e) { toast('Failed to toggle video', '#ef4444'); }
}

function pausePolling() {
  if (signalPoller) {
    clearInterval(signalPoller);
    signalPoller = null;
  }
}

function resumePolling() {
  if (!signalPoller) {
    signalPoller = setInterval(fetchSignals, 1500);
  }
}

// Toasts
const toasts = ref([]);
let toastSeq = 0;
function toast(msg, bg = '#2563eb', ttl = 3000) {
  const id = ++toastSeq;
  toasts.value.push({ id, msg, bg });
  setTimeout(() => {
    toasts.value = toasts.value.filter(t => t.id !== id);
  }, ttl);
}
</script>

<style scoped>
.meeting-container {
  max-width: 600px;
  margin: 0 auto;
  padding: 2rem;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.video {
  width: 280px;
  height: 180px;
  margin: 0.5rem;
  background: #222;
  border-radius: 4px;
}
button {
  margin: 1rem 0.5rem 0 0;
  padding: 0.5rem 1rem;
}
</style>
