import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
    input: [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/css/bootstrap.css',
        'resources/js/bootstrap.js',
        'resources/css/welcome.css',
        'resources/js/welcome.js',
        'resources/css/guest.css',
        'resources/css/admin.css',
        'resources/css/pos.css',
        'resources/css/pelanggan.css',
    ],
            refresh: true,
        }),
    ],
});
