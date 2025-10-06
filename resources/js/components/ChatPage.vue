<template>
  <div class="chat-container">
    <h2>Group & Meeting Chat</h2>
    <div>
      <label>Select Room:</label>
      <select v-model="selectedRoom" @change="fetchMessages">
        <option v-for="room in rooms" :key="room.id" :value="room.id">
          {{ room.name }} ({{ room.type }})
        </option>
      </select>
    </div>
    <div class="messages">
      <div v-for="msg in messages" :key="msg.id" class="message">
        <strong>{{ msg.user.name }}:</strong> {{ msg.content }}
      </div>
    </div>
    <form @submit.prevent="sendMessage">
      <input v-model="newMessage" placeholder="Type a message..." />
      <button type="submit">Send</button>
    </form>

    <!-- Debug Output -->
    <div class="debug-box" style="margin-top:2rem; background:#fbeee6; padding:1rem; border-radius:8px;">
      <h3>Debug Info</h3>
      <div><strong>Rooms:</strong> {{ rooms }}</div>
      <div><strong>Selected Room:</strong> {{ selectedRoom }}</div>
      <div><strong>Messages:</strong> {{ messages }}</div>
    </div>
  </div>
</template>

<script setup>

import { ref, onMounted, onUnmounted } from 'vue';

const rooms = ref([]);
const selectedRoom = ref(null);
const messages = ref([]);
const newMessage = ref('');
let poller = null;

function getCsrf() {
  const meta = document.querySelector('meta[name="csrf-token"]');
  if (meta && meta.getAttribute('content')) return meta.getAttribute('content');
  // Fallback to XSRF-TOKEN cookie
  const m = document.cookie.match(/(?:^|; )XSRF-TOKEN=([^;]+)/);
  if (m) try { return decodeURIComponent(m[1]); } catch (e) { /* noop */ }
  return '';
}

const fetchRooms = async () => {
  const res = await fetch('/api/chat-rooms', {
    credentials: 'same-origin',
    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
  });
  if (!res.ok) {
    console.error('[chat] Failed to load rooms', res.status, await res.text());
    return;
  }
  rooms.value = await res.json();
  const params = new URLSearchParams(window.location.search);
  const targetUser = params.get('user');
  if (targetUser) {
    // Ensure/find a direct room with the target user
    const ensure = await fetch('/chat/ensure-direct', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': getCsrf(),
      },
      credentials: 'same-origin',
      body: JSON.stringify({ user_id: parseInt(targetUser, 10) }),
    });
    if (ensure.ok) {
      const payload = await ensure.json();
      // Inject room into list if missing
      const idx = rooms.value.findIndex(r => r.id === payload.room.id);
      if (idx === -1) rooms.value.unshift(payload.room);
      selectedRoom.value = payload.room.id;
      messages.value = payload.messages || [];
      return;
    }
    console.error('[chat] ensure-direct failed', ensure.status, await ensure.text());
  }
  if (rooms.value.length) {
    selectedRoom.value = rooms.value[0].id;
    fetchMessages();
  }
};

const fetchMessages = async () => {
  if (!selectedRoom.value) return;
  const res = await fetch(`/api/chat-rooms/${selectedRoom.value}/messages`, {
    credentials: 'same-origin',
    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
  });
  if (!res.ok) {
    console.error('[chat] Failed to fetch messages', res.status, await res.text());
    return;
  }
  messages.value = await res.json();
};

const sendMessage = async () => {
  if (!newMessage.value || !selectedRoom.value) return;
  const resp = await fetch(`/api/chat-rooms/${selectedRoom.value}/messages`, {
    method: 'POST',
  headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': getCsrf() },
    credentials: 'same-origin',
    body: JSON.stringify({ content: newMessage.value })
  });
  if (!resp.ok) {
    console.error('[chat] Send failed', resp.status, await resp.text());
    return;
  }
  newMessage.value = '';
  fetchMessages();
};

function startPolling() {
  poller = setInterval(() => {
    fetchMessages();
  }, 2000); // Poll every 2 seconds
}

function stopPolling() {
  if (poller) {
    clearInterval(poller);
    poller = null;
  }
}

onMounted(() => {
  fetchRooms();
  startPolling();
});

onUnmounted(() => {
  stopPolling();
});
</script>

<style scoped>
.chat-container {
  max-width: 600px;
  margin: 0 auto;
  padding: 2rem;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.messages {
  min-height: 200px;
  margin-bottom: 1rem;
  background: #f9f9f9;
  padding: 1rem;
  border-radius: 4px;
}
.message {
  margin-bottom: 0.5rem;
}
form {
  display: flex;
  gap: 0.5rem;
}
input {
  flex: 1;
  padding: 0.5rem;
}
button {
  padding: 0.5rem 1rem;
}
</style>
