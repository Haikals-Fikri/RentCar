<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use App\Models\Booking;
use App\Models\OwnerProfile;

// Welcome Page
Route::get('/', function () {
    return view('welcome');
});

// ================= USER AUTH =================
Route::get('/register-user', [AuthController::class, 'showRegisterUser'])->name('register-user');
Route::post('/register-user', [AuthController::class, 'registerUser']);
Route::get('/login-user', [AuthController::class, 'showLoginUser'])->name('login-user');
Route::post('/login-user', [AuthController::class, 'loginUser']);

// ================= OWNER AUTH =================
Route::get('/register-owner', [AuthController::class, 'showRegisterOwner'])->name('register-owner');
Route::post('/register-owner', [AuthController::class, 'registerOwner'])->name('owner.register');
Route::get('/login-owner', [AuthController::class, 'showLoginOwner'])->name('login-owner');
Route::post('/login-owner', [AuthController::class, 'loginOwner'])->name('owner.login');

// ================= ADMIN AUTH =================
Route::get('/login-admin', [AuthController::class, 'showLoginAdmin'])->name('login-admin');
Route::post('/login-admin', [AuthController::class, 'loginAdmin']);

// ================= LOGOUT =================
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ================= USER DASHBOARD =================
Route::get('/user/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');

// ================= SEE ALL DASHBOARD =================
Route::get('/vehicles', [VehicleController::class, 'indexForUser'])->name('user.vehicles');

// ================= OWNER DASHBOARD & VEHICLE MANAGEMENT =================
Route::get('/owner/dashboard', [VehicleController::class, 'index'])->name('owner.dashboard');
Route::get('/vehicles/create', [VehicleController::class, 'create'])->name('vehicles.create');
Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
Route::get('/vehicles/{id}/edit', [VehicleController::class, 'edit'])->name('vehicles.edit');
Route::put('/vehicles/{id}', [VehicleController::class, 'update'])->name('vehicles.update');
Route::delete('/vehicles/{id}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');

// ================= ADMIN DASHBOARD =================
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

// Data User (Admin)
Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
Route::get('/admin/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
Route::post('/admin/users/store', [AdminController::class, 'storeUser'])->name('admin.users.store');
Route::get('/admin/users/{id}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
Route::put('/admin/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
Route::delete('/admin/users/{id}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');

// ================= USER PROFILE =================

Route::get('/profile/user', [ProfileController::class, 'showUserProfile'])->name('user.profile');
Route::put('/profile/update', [ProfileController::class, 'updateUserProfile'])->name('profile.update');
Route::get('/user/{id}/profile', [ProfileController::class, 'showOwnerProfileForUser'])->name('profile.owner.view');

// ================= OWNER PROFILE =================
Route::get('/profile/owner', [ProfileController::class, 'showOwnerProfile'])->name('profile.owner');
Route::post('/profile/owner', [ProfileController::class, 'updateOwnerProfile'])->name('profile.owner.update');

// Data Owner (Admin)
Route::get('/admin/owners', [AdminController::class, 'owners'])->name('admin.owners');
Route::get('/admin/owners/create', [AdminController::class, 'createOwner'])->name('admin.owners.create');
Route::post('/admin/owners/store', [AdminController::class, 'storeOwner'])->name('admin.owners.store');
Route::get('/admin/owners/{id}/edit', [AdminController::class, 'editOwner'])->name('admin.owners.edit');
Route::put('/admin/owners/{id}', [AdminController::class, 'updateOwner'])->name('admin.owners.update');
Route::delete('/admin/owners/{id}', [AdminController::class, 'destroyOwner'])->name('admin.owners.destroy');

// Statistik Pengguna (Admin)
Route::get('/admin/histogram', [AdminController::class, 'histogram'])->name('admin.histogram');

// ================= BOOKING =================
// Form booking & simpan
Route::get('/booking/{vehicle}', [BookingController::class, 'create'])->name('booking.form');
Route::post('/booking/{vehicle}/store', [BookingController::class, 'store'])->name('booking.store');

// Daftar booking user
Route::get('/user/bookings', [BookingController::class, 'index'])->name('user.bookings');

// Daftar booking admin
Route::get('/admin/booking', [BookingController::class, 'adminBooking'])->name('admin.booking');

// Daftar booking owner
Route::get('/owner/bookings', [BookingController::class, 'ownerBooking'])->name('owner.bookings');
// booking di batalkan
Route::post('/booking/{id}/cancel', [BookingController::class, 'cancelBooking'])->name('booking.cancel');

// Rating & Review
Route::get('/booking/{id}/complete', [BookingController::class, 'completeForm'])->name('booking.completeForm');
Route::post('/booking/{id}/complete', [BookingController::class, 'submitReview'])->name('booking.submitReview');
Route::get('/user/reviews', [BookingController::class, 'userReviews'])->name('user.reviews');



// Cetak PDF
Route::get('/booking/{id}/print', [BookingController::class, 'printPdf'])->name('booking.print');



