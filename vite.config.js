import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/chat.js',
                'resources/js/meeting.js',
                'resources/js/schedule.js',
                'resources/js/fileshare.js'
            ],
            refresh: true,
        }),
    ],
});