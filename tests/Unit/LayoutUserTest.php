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
        $user = User::create([
            'name' => 'User Risda',
            'email' => 'risda@gmail.com',
            'password' => bcrypt('11111111'),
            'role' => 'user'
        ]);

        $response = $this->actingAs($user)->get(route('user.dashboard'));

        // ASSERT 1: Memverifikasi Akses Diizinkan dan Status OK
        $response->assertOk();

        // ASSERT 2: Memverifikasi Detail User dan Perannya di dalam blok @auth
        $response->assertSeeTextInOrder([
            'User Risda',
            'User'
        ]);

        // ASSERT 3: Memverifikasi Link Profil Saya (Menguji Navigasi)
        $response->assertSeeInOrder([
            '<a href="' . route('user.profile') . '"',
            'Profil Saya'
        ], false);

        // ASSERT 4 (Baru): Memverifikasi Logo dan Judul Halaman di Header Sidebar
       $response->assertSeeText('RentCar');
         $response->assertSee('<title>Dashboard - RentCar</title>', false);

        // ASSERT 5 (Baru): Memverifikasi Link 'Riwayat Sewa' hadir dengan class 'active'
        $response->assertSeeInOrder([
            '<a href="' . route('user.dashboard') . '" class="nav-link active">'
        ], false);
    }
}
