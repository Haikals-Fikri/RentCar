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
                    'resources/css/user/profileuser.css',
                    'resources/css/user/userownerprofile.css',

                    //owner
                    'resources/css/owner/Detail_booking.css',
                    'resources/css/owner/profile-owner.css',
                    'resources/css/owner/vehicle-edit.css',
                    'resources/css/owner/D_owner.css',

                    //admin
                    'resources/css/admin/D_admin.css',
                    'resources/css/admin/A_Histogram.css',
                    'resources/css/admin/admin_owner_crud.css',
                    'resources/css/admin/admin_user_crud.css',
                    'resources/css/admin/admin_owners.css',
                    'resources/css/admin/admin_users.css',

                    //auth
                    'resources/css/auth/login_user.css',
                    'resources/css/auth/login_owner.css',
                    'resources/css/auth/login_admin.css',
                    'resources/css/auth/registrasi_user.css',
                    'resources/css/auth/registrasi_owner.css',








                    // Tambahkan file  atau JS lainnya di sini

                    //user
                    'resources/js/user/D_user.js',
                    'resources/js/user/bookingform.js',
                    'resources/js/user/bookinglist.js',

                    //owner
                    'resources/js/owner/D_owner.js',
                    'resources/js/owner/profile-owner.js',

                    //admin
                     'resources/js/admin/D_admin.js',
                     'resources/js/admin/A_Histogram.js',

                    //auth
                     'resources/js/auth/login_admin.js',
                     'resources/js/auth/login_owner.js',
                     'resources/js/auth/login_user.js',
                     'resources/js/auth/registrasi_owner.js',
                     'resources/js/auth/registrasi_user.js',

                    //welcome
                    'resources/js/welcome.js',



            ],

            refresh: true,
        }),
        tailwindcss(),
    ],
});
