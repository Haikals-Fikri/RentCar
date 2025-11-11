import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 
                    'resources/js/app.js',
                    'resources/css/user/layouts.user.css',
                    'resources/css/user/Booking-list.css',
                    'resources/css/user/Booking-form.css',
                    'resources/css/user/review-user.css',
            ],

            refresh: true,
        }),
        tailwindcss(),
    ],
});
