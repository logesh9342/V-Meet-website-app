<template>
  <div class="call-container">
    <div v-if="!inCall" class="call-setup">
    import { ref, onMounted, onUnmounted } from 'vue';
      <button @click="startCall('video')" class="call-btn">Start Video Call</button>
    // Room ID for signaling (should be set from parent or route)
    const roomId = ref('demo-room'); // TODO: Replace with dynamic room/meeting/chat id
      <button @click="startCall('audio')" class="call-btn">Start Audio Call</button>
    </div>
    <div v-else class="call-active">
      <div class="video-section">
        <video ref="localVideo" autoplay playsinline muted class="video-box"></video>
        <div v-for="(stream, id) in remoteStreams" :key="id" class="video-box">
          <video :ref="'remoteVideo_' + id" autoplay playsinline></video>
        </div>
// WebRTC peer connections for each remote user
const peerConnections = {};
      </div>
function startCall(type) {
  inCall.value = true;
  navigator.mediaDevices.getUserMedia({
    video: type === 'video',
    audio: true
  }).then(stream => {
    localStream = stream;
    document.querySelector('video').srcObject = stream;
    startPollingSignaling();
    // For demo, immediately send offer (in real app, wait for other users to join)
    createAndSendOffer('group');
  });
}

function createAndSendOffer(peerId) {
  // Create peer connection for each peer (for demo, use 'group')
  const pc = new RTCPeerConnection();
  peerConnections[peerId] = pc;
  localStream.getTracks().forEach(track => pc.addTrack(track, localStream));
  pc.onicecandidate = event => {
    if (event.candidate) {
      sendSignaling('candidate', { candidate: event.candidate, peerId });
    }
  };
  pc.ontrack = event => {
    remoteStreams.value[peerId] = event.streams[0];
    // Attach to remote video element
    const remoteVideo = document.querySelector(`[ref='remoteVideo_${peerId}']`);
    if (remoteVideo) remoteVideo.srcObject = event.streams[0];
  };
  pc.createOffer().then(offer => {
    pc.setLocalDescription(offer);
    sendSignaling('offer', { sdp: offer.sdp, peerId });
  });
}

function handleSignalingMessage(msg) {
  const { type, data, user_id } = msg;
  const peerId = data.peerId || user_id || 'group';
  if (!peerConnections[peerId]) {
    peerConnections[peerId] = new RTCPeerConnection();
    localStream.getTracks().forEach(track => peerConnections[peerId].addTrack(track, localStream));
    peerConnections[peerId].onicecandidate = event => {
      if (event.candidate) {
        sendSignaling('candidate', { candidate: event.candidate, peerId });
      }
    };
    peerConnections[peerId].ontrack = event => {
      remoteStreams.value[peerId] = event.streams[0];
      const remoteVideo = document.querySelector(`[ref='remoteVideo_${peerId}']`);
      if (remoteVideo) remoteVideo.srcObject = event.streams[0];
    };
  }
  const pc = peerConnections[peerId];
  if (type === 'offer') {
    pc.setRemoteDescription(new RTCSessionDescription({ type: 'offer', sdp: data.sdp })).then(() => {
      pc.createAnswer().then(answer => {
        pc.setLocalDescription(answer);
        sendSignaling('answer', { sdp: answer.sdp, peerId });
      });
    });
  } else if (type === 'answer') {
    pc.setRemoteDescription(new RTCSessionDescription({ type: 'answer', sdp: data.sdp }));
  } else if (type === 'candidate') {
    if (data.candidate) {
      pc.addIceCandidate(new RTCIceCandidate(data.candidate));
    }
  }
}
</template>

<script setup>
import { ref } from 'vue';

const inCall = ref(false);
const screenSharing = ref(false);
const recording = ref(false);
const recordedUrl = ref(null);
const remoteStreams = ref({});
let localStream = null;
let mediaRecorder = null;
let recordedChunks = [];
      stopPollingSignaling();

function startCall(type) {
  inCall.value = true;
  navigator.mediaDevices.getUserMedia({
    video: type === 'video',
    audio: true
  }).then(stream => {
    localStream = stream;
    document.querySelector('video').srcObject = stream;
    // TODO: signaling logic for group call
  });
}

function endCall() {
  inCall.value = false;
  if (localStream) {
    localStream.getTracks().forEach(track => track.stop());
  }
  Object.values(remoteStreams.value).forEach(stream => {
    stream.getTracks().forEach(track => track.stop());
  });
  remoteStreams.value = {};
  stopRecording();
}

function toggleScreenShare() {
  if (!screenSharing.value) {
    navigator.mediaDevices.getDisplayMedia({ video: true }).then(screenStream => {
      // Replace video track in localStream
      const videoTrack = screenStream.getVideoTracks()[0];
      localStream.removeTrack(localStream.getVideoTracks()[0]);
      localStream.addTrack(videoTrack);
      document.querySelector('video').srcObject = localStream;
      screenSharing.value = true;
      videoTrack.onended = () => {
        screenSharing.value = false;
        // Restore camera
        navigator.mediaDevices.getUserMedia({ video: true, audio: true }).then(stream => {
          localStream = stream;
          document.querySelector('video').srcObject = stream;
        });
      };
    });
  } else {
    // Stop screen sharing
    endCall();
    startCall('video');
    screenSharing.value = false;
  }
}

function toggleRecording() {
  if (!recording.value) {
    // --- Signaling API integration ---
    async function sendSignaling(type, data) {
      await fetch('/api/signaling/send', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({
          room_id: roomId.value,
          type,
          data,
        }),
      });
    }

    async function pollSignaling() {
      const res = await fetch(`/api/signaling/receive?room_id=${roomId.value}`);
      const json = await res.json();
      if (json.messages) {
        json.messages.forEach(msg => {
          handleSignalingMessage(msg);
        });
      }
    }

    function startPollingSignaling() {
      signalingPoller = setInterval(pollSignaling, 2000); // Poll every 2s
    }

    function stopPollingSignaling() {
      if (signalingPoller) {
        clearInterval(signalingPoller);
        signalingPoller = null;
      }
    }

    onMounted(() => {
      // Optionally start polling if already in call
      if (inCall.value) startPollingSignaling();
    });
    onUnmounted(() => {
      stopPollingSignaling();
    });
    recordedChunks = [];
    mediaRecorder = new MediaRecorder(localStream);
    mediaRecorder.ondataavailable = e => {
      if (e.data.size > 0) recordedChunks.push(e.data);
    };
    mediaRecorder.onstop = () => {
      const blob = new Blob(recordedChunks, { type: 'video/webm' });
      recordedUrl.value = URL.createObjectURL(blob);
    };
    mediaRecorder.start();
    recording.value = true;
  } else {
    stopRecording();
  }
}

function stopRecording() {
  if (mediaRecorder && recording.value) {
    mediaRecorder.stop();
    recording.value = false;
  }
}
</script>

<style scoped>
.call-container {
  max-width: 700px;
  margin: 2rem auto;
  background: #f3f6fb;
  border-radius: 16px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.08);
  padding: 2rem;
}
.call-setup {
  text-align: center;
}
.call-btn {
  background: #2563eb;
  color: #fff;
  border: none;
  padding: 0.8rem 2rem;
  border-radius: 8px;
  font-size: 1.1rem;
  margin: 1rem;
  cursor: pointer;
  transition: background 0.2s;
}
.call-btn:hover {
  background: #1d4ed8;
}
.call-active {
  display: flex;
  flex-direction: column;
  align-items: center;
}
.video-section {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
}
.video-box {
  width: 220px;
  height: 160px;
  background: #222;
  border-radius: 8px;
  overflow: hidden;
}
.call-controls {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
}
.control-btn {
  background: #2563eb;
  color: #fff;
  border: none;
  padding: 0.6rem 1.5rem;
  border-radius: 8px;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.2s;
}
.control-btn.end {
  background: #ef4444;
}
.control-btn:hover {
  background: #1d4ed8;
}
.recording-link {
  margin-top: 1rem;
}
</style>
