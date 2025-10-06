import { createApp } from 'vue';
import ScheduleMeeting from './components/ScheduleMeeting.vue';

const mountSchedule = () => {
	const el = document.getElementById('schedule-app');
	if (!el) {
		console.warn('[schedule] #schedule-app not found on page, skipping mount');
		return;
	}
	const app = createApp({});
	app.component('schedule-meeting', ScheduleMeeting);
	app.mount(el);
	console.info('[schedule] Vue schedule app mounted');
};

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', mountSchedule);
} else {
	mountSchedule();
}
