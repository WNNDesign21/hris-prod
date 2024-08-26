import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                "resources/css/app.css",
                'resources/js/app.js',
                'resources/js/pages/master-data-dashboard.js',
                'resources/js/pages/organisasi.js',
                'resources/js/pages/departemen.js',
                'resources/js/pages/divisi.js',
                'resources/js/pages/grup.js',
                'resources/js/pages/jabatan.js',
                'resources/js/pages/karyawan.js',
                'resources/js/pages/kontrak.js',
                'resources/js/pages/posisi.js',
                'resources/js/pages/seksi.js',
            ],
            // refresh: true,
            refresh: ["resources/views/**", "resources/css/**", "app/Http/**"],
        }),
    ],
});
