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

        $expectedUserCount = 15;
        $expectedOwnerCount = 5;

        for ($i = 1; $i <= $expectedUserCount; $i++) {
            User::create([
                'name' => 'Haikal' . $i,
                'email' => 'userhaikal' . $i . '@gmail.com',
                'password' => bcrypt('11111111'),
                'role' => 'user',
            ]);
        }

        for ($i = 1; $i <= $expectedOwnerCount; $i++) {
            User::create([
                'name' => 'Risda' . $i,
                'email' => 'ownerrisda' . $i . '@gmail.com',
                'password' => bcrypt('11111111'),
                'role' => 'owner',
            ]);
        }

        $admin = User::create([
            'name' => 'Adam',
            'email' => 'adminadam@gmail.com',
            'password' => bcrypt('11111111'),
            'role' => 'admin'
        ]);

        $response = $this->actingAs($admin)->get(route('admin.histogram'));

        // ASSERT 1: Memverifikasi Akses Halaman Berhasil
        $response->assertStatus(200);

        // ASSERT 2: Memverifikasi Variabel View Diteruskan
        $response->assertViewHas('UserCount', $expectedUserCount);

        // ASSERT 3: Memverifikasi Variabel View Diteruskan
        $response->assertViewHas('OwnerCount', $expectedOwnerCount);

        // ASSERT 4: Memverifikasi Judul Utama Grafik (Menggunakan assertSeeText)
        $response->assertSeeText('Statistik Pengguna dan Booking');

        // ASSERT 5: Memverifikasi Data-Attribute Canvas (Menggunakan assertSeeInOrder)

        $response->assertSeeInOrder([
            'data-users="' . $expectedUserCount . '"',
            'data-owners="' . $expectedOwnerCount . '"'
        ], $escaped = false);
    }
}
