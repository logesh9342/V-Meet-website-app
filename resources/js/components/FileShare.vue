<template>
  <div class="file-share-container">
    <h2>File Sharing</h2>
    <form @submit.prevent="uploadFile">
      <input type="file" ref="fileInput" />
      <select v-model="roomOrMeeting">
        <option value="chat">Chat Room</option>
        <option value="meeting">Meeting</option>
      </select>
      <input v-model="targetId" placeholder="Room/Meeting ID" required />
      <button type="submit">Upload</button>
    </form>
    <h3>Shared Files</h3>
    <ul>
      <li v-for="file in files" :key="file.id">
        <a :href="getFileUrl(file.file_path)" target="_blank">{{ file.file_name }}</a>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const fileInput = ref(null);
const roomOrMeeting = ref('chat');
const targetId = ref('');
const files = ref([]);

const fetchFiles = async () => {
  const params = new URLSearchParams();
  if (roomOrMeeting.value === 'chat') params.append('chat_room_id', targetId.value);
  if (roomOrMeeting.value === 'meeting') params.append('meeting_id', targetId.value);
  const res = await fetch(`/api/files?${params.toString()}`, { credentials: 'same-origin' });
  files.value = await res.json();
};

const uploadFile = async () => {
  const file = fileInput.value.files[0];
  if (!file || !targetId.value) return;
  const formData = new FormData();
  formData.append('file', file);
  if (roomOrMeeting.value === 'chat') formData.append('chat_room_id', targetId.value);
  if (roomOrMeeting.value === 'meeting') formData.append('meeting_id', targetId.value);
  await fetch('/api/files', {
    method: 'POST',
    credentials: 'same-origin',
    body: formData
  });
  fileInput.value.value = '';
  fetchFiles();
};

const getFileUrl = (path) => `/storage/${path}`;
</script>

<style scoped>
.file-share-container {
  max-width: 600px;
  margin: 0 auto;
  padding: 2rem;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
form {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 2rem;
}
input, select {
  padding: 0.5rem;
  border-radius: 4px;
  border: 1px solid #ccc;
}
button {
  padding: 0.5rem 1rem;
}
ul {
  list-style: none;
  padding: 0;
}
li {
  margin-bottom: 1rem;
  background: #f9f9f9;
  padding: 1rem;
  border-radius: 4px;
}
</style>
