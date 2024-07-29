import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                "resources/css/app.css",
                'resources/js/app.js',
                'resources/js/pages/organisasi.js',
            ],
            // refresh: true,
            refresh: ["resources/views/**", "resources/css/**", "app/Http/**"],
        }),
    ],
});
