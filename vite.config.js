import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css',
                    'resources/js/app.js',
                    //welcome
                    'resources/css/welcome.css',
                    //layouts user
                    'resources/css/user/Layouts_user.css',

                    //user
                    'resources/css/user/D_user.css',
                    'resources/css/user/Layouts_user.css',
                    'resources/css/user/Booking-list.css',
                    'resources/css/user/Booking-form.css',
                    'resources/css/user/review-user.css',
                    'resources/css/user/rating.css',

                    //owner
                    'resources/css/owner/Detail_booking.css',
                    'resources/css/owner/profile-owner.css',
                    'resources/css/owner/vehicle-edit.css',
                    'resources/css/owner/D_owner.css',

                    //admin
                    'resources/css/admin/D_admin.css',
                    'resources/css/admin/A_Histogram.css',








                    // Tambahkan file  atau JS lainnya di sini
                    'resources/js/welcome.js',
                    'resources/js/admin/D_admin.js',
                    'resources/js/owner/D_owner.js',
                    'resources/js/user/D_user.js',
                    'resources/js/owner/profile-owner.js',
                    'resources/js/admin/A_Histogram.js'
            ],

            refresh: true,
        }),
        tailwindcss(),
    ],
});
