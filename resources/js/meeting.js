import { createApp } from 'vue';
import MeetingPage from './components/MeetingPage.vue';

// Ensure the DOM exists before mounting and only mount on pages that have the target element
const mountMeeting = () => {
	const el = document.getElementById('meeting-app');
	if (!el) {
		console.warn('[meeting] #meeting-app not found on page, skipping mount');
		return;
	}
	const app = createApp({});
	app.component('meeting-page', MeetingPage);
	app.mount(el);
	console.info('[meeting] Vue meeting app mounted');
};

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', mountMeeting);
} else {
	mountMeeting();
}
