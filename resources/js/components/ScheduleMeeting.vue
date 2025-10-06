<template>
  <div class="schedule-container">
    <h2>Schedule a Meeting</h2>
    <form @submit.prevent="createMeeting">
      <input v-model="title" placeholder="Title" required />
      <textarea v-model="description" placeholder="Description"></textarea>
      <input v-model="startTime" type="datetime-local" required />
      <input v-model="endTime" type="datetime-local" />
      <button type="submit">Create Meeting</button>
    </form>
    <h3>My Meetings</h3>
    <ul>
      <li v-for="meeting in meetings" :key="meeting.id">
        <strong>{{ meeting.title }}</strong> - {{ meeting.start_time }}
        <button @click="goToMeeting(meeting.id)">Join</button>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const title = ref('');
const description = ref('');
const startTime = ref('');
const endTime = ref('');
const meetings = ref([]);

const fetchMeetings = async () => {
  const res = await fetch('/api/meetings', { credentials: 'same-origin' });
  meetings.value = await res.json();
};

const createMeeting = async () => {
  await fetch('/api/meetings', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    credentials: 'same-origin',
    body: JSON.stringify({
      title: title.value,
      description: description.value,
      start_time: startTime.value,
      end_time: endTime.value
    })
  });
  title.value = '';
  description.value = '';
  startTime.value = '';
  endTime.value = '';
  fetchMeetings();
};

const goToMeeting = (id) => {
  window.location.href = `/meeting?id=${id}`;
};

onMounted(() => {
  fetchMeetings();
});
</script>

<style scoped>
.schedule-container {
  max-width: 600px;
  margin: 0 auto;
  padding: 2rem;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
form {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-bottom: 2rem;
}
input, textarea {
  padding: 0.5rem;
  border-radius: 4px;
  border: 1px solid #ccc;
}
button {
  padding: 0.5rem 1rem;
  margin-top: 0.5rem;
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
