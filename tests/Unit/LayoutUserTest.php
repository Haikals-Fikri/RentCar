<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LayoutUserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_all_authenticated_sidebar_and_navigation_elements()
    {
        // 1. SETUP: Buat dan autentikasi user
        $user = User::create([
            'name' => 'Budi Pelanggan',
            'email' => 'budi@rental.com',
            'password' => bcrypt('password'),
            'role' => 'user'
        ]);

        // Aksi: Akses halaman dashboard sebagai user yang sudah login
        $response = $this->actingAs($user)->get(route('user.dashboard'));

        // ASSERT 1: Memverifikasi Akses Diizinkan dan Status OK
        $response->assertOk();

        // ASSERT 2: Memverifikasi Detail User dan Perannya di dalam blok @auth
        // Memastikan nama dan peran "Pelanggan" tampil
        $response->assertSeeTextInOrder([
            'Budi Pelanggan',
            'Pelanggan'
        ]);

        // ASSERT 3: Memverifikasi Link Profil Saya (Menguji Navigasi)
        // Memastikan link navigasi utama (Profil Saya) hadir dengan route yang benar.
        $response->assertSeeInOrder([
            '<a href="' . route('user.profile') . '"',
            'Profil Saya'
        ], false);

        // ASSERT 4 (Baru): Memverifikasi Logo dan Judul Halaman di Header Sidebar
        // Memastikan logo 'RentCar' dan Judul Halaman 'Dashboard User' (nilai default) hadir.
       $response->assertSeeText('RentCar');
         $response->assertSee('<title>Dashboard - RentCar</title>', false);

        // ASSERT 5 (Baru): Memverifikasi Link 'Riwayat Sewa' hadir dengan class 'active'
        // Jika route('user.dashboard') yang sedang diakses, maka 'Beranda' yang harus aktif.
        // Mari kita asumsikan kita menguji halaman Riwayat Sewa (route('user.bookings')) untuk menguji class 'active'
        // KARENA ANDA MENGAKSES route('user.dashboard'), maka 'Beranda' yang harus diuji aktif.
        $response->assertSeeInOrder([
            // Memverifikasi link Beranda (route('user.dashboard')) harus memiliki class 'active'
            '<a href="' . route('user.dashboard') . '" class="nav-link active">'
        ], false);
    }
}
