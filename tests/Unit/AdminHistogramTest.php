<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AdminHistogramTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_user_owner_histogram_with_correct_data_without_factory()
    {
        // 1. SETUP DATA KRITIS (Membuat data di database test secara manual)
        $expectedUserCount = 15;
        $expectedOwnerCount = 5;

        // Membuat 15 User dengan role 'user'
        for ($i = 1; $i <= $expectedUserCount; $i++) {
            User::create([
                'name' => 'User ' . $i,
                'email' => 'user' . $i . '@test.com',
                'password' => bcrypt('password'),
                'role' => 'user',
            ]);
        }

        // Membuat 5 User dengan role 'owner'
        for ($i = 1; $i <= $expectedOwnerCount; $i++) {
            User::create([
                'name' => 'Owner ' . $i,
                'email' => 'owner' . $i . '@test.com',
                'password' => bcrypt('password'),
                'role' => 'owner',
            ]);
        }

        // Membuat Admin yang akan login
        $admin = User::create([
            'name' => 'Admin Tester',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        // Aksi: Akses halaman sebagai admin
        $response = $this->actingAs($admin)->get(route('admin.histogram'));

        // =======================================================
        // 5 ASSERTIONS (Menggunakan method yang kompatibel)
        // =======================================================

        // ASSERT 1: Memverifikasi Akses Halaman Berhasil
        $response->assertStatus(200);

        // ASSERT 2: Memverifikasi Variabel View Diteruskan
        $response->assertViewHas('UserCount', $expectedUserCount);

        // ASSERT 3: Memverifikasi Variabel View Diteruskan
        $response->assertViewHas('OwnerCount', $expectedOwnerCount);

        // ASSERT 4: Memverifikasi Judul Utama Grafik (Menggunakan assertSeeText)
        $response->assertSeeText('Statistik Pengguna dan Booking');

        // ASSERT 5: Memverifikasi Data-Attribute Canvas (Menggunakan assertSeeInOrder)
        // Kita kembali ke assertSeeInOrder karena ini adalah method native Laravel
        // dan harus lulus jika stringnya ditemukan berdekatan.
        $response->assertSeeInOrder([
            'data-users="' . $expectedUserCount . '"',
            'data-owners="' . $expectedOwnerCount . '"'
        ], $escaped = false); // Wajib menggunakan $escaped = false untuk raw HTML
    }
}
