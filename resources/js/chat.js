import { createApp } from 'vue';
import ChatPage from './components/ChatPage.vue';

const mountChat = () => {
	const el = document.getElementById('chat-app');
	if (!el) {
		console.warn('[chat] #chat-app not found on page, skipping mount');
		return;
	}
	const app = createApp({});
	app.component('chat-page', ChatPage);
	app.mount(el);
	console.info('[chat] Vue chat app mounted');
};

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', mountChat);
} else {
	mountChat();
}
