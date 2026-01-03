<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\OwnerProfile;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class UserProfileOwner extends TestCase
{
    use RefreshDatabase;

    /**
     * Pastikan halaman profil owner menampilkan detail profil dan kendaraannya
     * (Menggunakan model::create() manual, BUKAN factory).
     */
    #[Test]
    public function user_can_view_owner_profile_with_details_and_vehicles_without_factory()
    {
        // 1. Setup Data Owner dan Profil secara manual
        $owner = User::create([
            'name' => 'Hasanuddin',
            'email' => 'Rentcar@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'owner'
        ]);

        // Buat OwnerProfile dengan data lengkap secara manual
        OwnerProfile::create([
            'owner_id' => $owner->id,
            'business_name' => 'Rencar Abadi Nan Jaya',
            'description' => 'Kami menyediakan layanan rental mobil terbaik.',
            'address' => 'Jl. Syamsul Alam No. 45, Parepare',
            'phone_number' => '08123456789', // Penting untuk WhatsApp
            'instagram' => 'https://instagram.com/login',
            'facebook' => 'https://facebook.com/login'
            // Catatan: Jika ada field yang wajib diisi tapi tidak disebutkan di sini,
            // Anda perlu menambahkannya ke array 'create' ini.
        ]);

        // 2. Setup Data Kendaraan (3 unit) secara manual
        Vehicle::create([
            'owner_id' => $owner->id,
            'name' => 'Toyota Kijang Innova A',
            'status_vehicle' => 'Tersedia',
            'price_per_day' => 450000,
            // Tambahkan field wajib lainnya di sini (misalnya: brand, type, plate_number)
            'brand' => 'Toyota', 'type' => 'MPV', 'plate_number' => 'D 111 AA'
        ]);
        Vehicle::create([
            'owner_id' => $owner->id,
            'name' => 'Toyota Kijang Innova B',
            'status_vehicle' => 'Tersedia',
            'price_per_day' => 450000,
            'brand' => 'Toyota', 'type' => 'MPV', 'plate_number' => 'D 222 BB'
        ]);
        Vehicle::create([
            'owner_id' => $owner->id,
            'name' => 'Toyota Kijang Innova C',
            'status_vehicle' => 'Tersedia',
            'price_per_day' => 450000,
            'brand' => 'Toyota', 'type' => 'MPV', 'plate_number' => 'D 333 CC'
        ]);

        // Buat 1 kendaraan Owner lain (untuk memastikan filter)
        $otherOwner = User::create(['name' => 'Owner Lain', 'email' => 'other.owner@mail.com', 'password' => bcrypt('password'), 'role' => 'owner']);
        Vehicle::create([
            'owner_id' => $otherOwner->id,
            'name' => 'Mobil Sultan',
            'price_per_day' => 1000000,
            'status_vehicle' => 'Tersedia',
            // Tambahkan field wajib lainnya di sini (misalnya: brand, type, plate_number
            'brand' => 'Honda', 'type' => 'Sedan', 'plate_number' => 'B 999 Z'
        ]);

        // 3. Eksekusi Request HTTP
        $response = $this->get(route('profile.owner.view', $owner->id)); // Asumsikan rute bernama 'owner.profile'

        // 4. ASSERTIONS

        // ASSERT 1: Memastikan halaman berhasil dimuat (Status 200 OK)
        $response->assertOk();
        // Memastikan menggunakan layout/view yang benar, jika diperlukan:
        // $response->assertViewIs('nama.view.profil');

        // ASSERT 2: Memverifikasi Detail Profil (Nama Bisnis, Alamat) muncul di Halaman
        $response->assertSeeText('Rencar Abadi Nan Jaya');
        $response->assertSeeText('Jl. Syamsul Alam No. 45, Parepare');

        // ASSERT 3: Memverifikasi Hitungan Kendaraan pada Stat Section sudah benar (3 unit)
        // Kita hitung jumlah kendaraan milik owner, harusnya 3.
        $response->assertSeeInOrder([
            '<span class="stat-number">3</span>', // Jumlah total kendaraan owner ini
            '<span class="stat-label">Kendaraan</span>',
        ], false);

        // ASSERT 4: Memverifikasi Link Kontak WhatsApp telah terformat dengan benar
        // Nomor HP: 08123456789 -> Diformat menjadi 628123456789 (sesuai Blade yang Anda berikan)
        $response->assertSee('href="https://wa.me/628123456789"', false);

        // ASSERT 5: Memverifikasi Kendaraan Owner terdaftar muncul, dan kendaraan Owner lain tidak muncul
        $response->assertSeeText('Toyota Kijang Innova A'); // Kendaraan milik owner ini
        $response->assertDontSeeText('Mobil Owner Lain'); // Kendaraan milik owner lain harus disembunyikan
    }
}
