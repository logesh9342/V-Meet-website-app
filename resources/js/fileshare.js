import { createApp } from 'vue';
import FileShare from './components/FileShare.vue';

const mountFileShare = () => {
	const el = document.getElementById('file-share-app');
	if (!el) {
		console.warn('[fileshare] #file-share-app not found on page, skipping mount');
		return;
	}
	const app = createApp({});
	app.component('file-share', FileShare);
	app.mount(el);
	console.info('[fileshare] Vue fileshare app mounted');
};

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', mountFileShare);
} else {
	mountFileShare();
}
