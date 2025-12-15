<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

// Saya pindahkan ke Feature Test karena tes ini melibatkan HTTP request dan DB
class DetailBookingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $owner2;
    protected Vehicle $vehicleA;
    protected Vehicle $vehicleB;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Buat user Admin (untuk akses dashboard)
        $this->admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);
        $this->actingAs($this->admin);

        // 1b. Buat Owner kedua (untuk menghindari FK Constraint pada Vehicle B)
        $this->owner2 = User::create([
            'name' => 'Test Owner 2',
            'email' => 'owner2@test.com',
            'password' => bcrypt('password'),
            'role' => 'owner'
        ]);

        // 2. Buat Vehicle (dengan kolom NOT NULL yang lengkap)
        $this->vehicleA = Vehicle::create([
            'owner_id' => $this->admin->id, // Menggunakan ID yang valid
            'name' => 'Car A',
            'brand' => 'Toyota',
            'model' => 'Avanza',
            'plate_number' => 'B 1111 XX',
            'transmisi' => 'Manual',
            'price_per_day' => 100000,
            'status_vehicle' => 'Tersedia',
            'type' => 'Sedan'
        ]);

        $this->vehicleB = Vehicle::create([
            'owner_id' => $this->owner2->id, // Menggunakan ID yang valid
            'name' => 'Car B',
            'brand' => 'Honda',
            'model' => 'Brio',
            'plate_number' => 'B 2222 YY',
            'transmisi' => 'Matic',
            'price_per_day' => 200000,
            'status_vehicle' => 'Tersedia',
            'type' => 'Hatchback'
        ]);

        // Atur waktu sekarang ke 15 Maret 2025 (Jumat, 14 Maret 2025)
        Carbon::setTestNow(Carbon::create(2025, 3, 14));
    }

    /**
     * Helper untuk membuat Booking
     */
    protected function createBooking(Carbon $date, Vehicle $vehicle, string $status, int $count = 1): void
    {
        for ($i = 0; $i < $count; $i++) {
            Booking::create([
                'vehicle_id' => $vehicle->id,
                'user_id' => $this->admin->id,
                'start_date' => $date,
                'end_date' => $date->copy()->addDay(),
                'status' => $status,
                'total_payment' => $vehicle->price_per_day,
                'name' => 'Booking ' . $vehicle->name . ' ' . $i,
                'address' => 'Addr', 'phone' => '123', 'sim_image' => 'sim',
                'payment_method' => 'Cash', 'deposit_amount' => 50000,
            ]);
        }
    }

    #[Test]
    public function it_can_filter_data_weekly_and_calculate_peak_day()
    {
        // Setup Data untuk Minggu ini (Senin, 10 Maret 2025 s/d Minggu, 16 Maret 2025)
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY); // 10 Maret

        // Data hari Jumat (14 Maret) -> Peak
        $this->createBooking($startOfWeek->copy()->addDays(4), $this->vehicleA, 'completed', 3);
        // Data hari Selasa (11 Maret)
        $this->createBooking($startOfWeek->copy()->addDay(), $this->vehicleB, 'confirmed', 1);

        // Aksi: Akses dengan filter mingguan
        $response = $this->get(route('booking.analytics', ['filter' => 'mingguan'])); // <-- Perbaikan Route

        $response->assertOk();
        $mainData = $response->viewData('mainData');

        // ASSERT 1: Memverifikasi Total Booking
        $this->assertEquals(4, $mainData['stats']['total'], 'Total booking mingguan harus 4.');

        // ASSERT 2: Memverifikasi Peak Day
        $this->assertEquals(3, $mainData['stats']['peak'], 'Peak booking harus 3.');
        $this->assertEquals('Jumat', $mainData['stats']['peak_label'], 'Peak label harus Jumat.');

        // ASSERT 3: Memverifikasi data chart memiliki 7 label
        $this->assertCount(7, $mainData['labels'], 'Chart labels harus 7.');

        // ASSERT 4: Memverifikasi Insight memuat rekomendasi spesifik mingguan
        $insights = $response->viewData('insights');
        $this->assertStringContainsString('tambah unit matic di akhir pekan', $insights[3], 'Insight harus memberikan rekomendasi mingguan.');

        // ASSERT 5: Memverifikasi status filter 'mingguan'
        $this->assertEquals('mingguan', $response->viewData('filter'), 'Filter yang digunakan harus mingguan.');
    }

    #[Test]
    public function it_can_filter_data_monthly_and_handle_comparison()
    {
        // Setup Data (Maret 2025)
        $this->createBooking(Carbon::create(2025, 3, 10), $this->vehicleA, 'confirmed', 5); // 5 Booking di Maret
        // Setup Data untuk perbandingan (Februari 2025)
        $this->createBooking(Carbon::create(2025, 2, 10), $this->vehicleB, 'completed', 2); // 2 Booking di Februari

        // Aksi: Akses dengan filter bulanan (Maret) dan perbandingan (Februari)
        $response = $this->get(route('booking.analytics', [ // <-- Perbaikan Route
            'filter' => 'bulanan',
            'month' => '03',
            'year' => '2025',
            'compare_mode' => 'bulanan',
            'periodA' => '03', // Maret (5)
            'periodB' => '02'  // Februari (2)
        ]));

        $response->assertOk();
        $mainData = $response->viewData('mainData');
        $comparisonData = $response->viewData('comparisonData');

        // ASSERT 1: Memverifikasi Total Booking (Hanya Maret)
        $this->assertEquals(5, $mainData['stats']['total'], 'Total booking bulanan harus 5.');

        // ASSERT 2: Memverifikasi Peak Day (Tgl 10)
        $this->assertEquals('Tgl 10', $mainData['stats']['peak_label'], 'Peak label harus Tgl 10.');

        // ASSERT 3: Memverifikasi Data Perbandingan (150% kenaikan)
        $this->assertEquals(150.0, $comparisonData['percentage'], 'Persentase perbandingan harus 150%.');
        $this->assertEquals('up', $comparisonData['trend'], 'Tren harus naik.');

        // ASSERT 4: Memverifikasi data chart memiliki jumlah label hari di bulan Maret (31)
        $this->assertCount(31, $mainData['labels'], 'Chart labels harus 31 untuk bulan Maret.');

        // ASSERT 5: Memverifikasi Insight memuat rekomendasi spesifik bulanan
        $insights = $response->viewData('insights');
        $this->assertStringContainsString('paruh akhir bulan', $insights[2], 'Insight harus memberikan tren paruh bulanan.');
    }

    #[Test]
    public function it_can_filter_data_yearly()
    {
        // Setup Data (2025)
        $this->createBooking(Carbon::create(2025, 1, 1), $this->vehicleA, 'completed', 4); // Januari
        $this->createBooking(Carbon::create(2025, 11, 1), $this->vehicleB, 'confirmed', 7); // November -> Peak
        // Data di luar tahun 2025
        $this->createBooking(Carbon::create(2024, 12, 1), $this->vehicleA, 'completed', 2);

        // Aksi: Akses dengan filter tahunan (2025)
        $response = $this->get(route('booking.analytics', ['filter' => 'tahunan', 'year' => '2025'])); // <-- Perbaikan Route

        $response->assertOk();
        $mainData = $response->viewData('mainData');

        // ASSERT 1: Memverifikasi Total Booking
        $this->assertEquals(11, $mainData['stats']['total'], 'Total booking tahunan harus 11.');

        // ASSERT 2: Memverifikasi Peak Month (November)
        $this->assertEquals(7, $mainData['stats']['peak'], 'Peak booking harus 7.');
        $this->assertEquals('Nov', $mainData['stats']['peak_label'], 'Peak label harus Nov.');

        // ASSERT 3: Memverifikasi data chart memiliki 12 label
        $this->assertCount(12, $mainData['labels'], 'Chart labels harus 12.');

        // ASSERT 4: Memverifikasi Rata-rata booking (11/12 = 0.9)
        $this->assertEquals(0.9, $mainData['stats']['average'], 'Rata-rata booking bulanan harus 0.9.');

        // ASSERT 5: Memverifikasi Insight memuat rekomendasi spesifik tahunan
        $insights = $response->viewData('insights');
        $this->assertStringContainsString('paket promosi di bulan sepi', $insights[3], 'Insight harus memberikan rekomendasi tahunan.');
    }

    #[Test]
    public function it_can_filter_data_custom_date()
    {
        $customDate = Carbon::create(2025, 4, 20); // 20 April 2025

        // Setup Data (20 April)
        $this->createBooking($customDate, $this->vehicleA, 'completed', 6);
        // Data di luar tanggal custom
        $this->createBooking($customDate->copy()->subDay(), $this->vehicleB, 'confirmed', 1);

        // Aksi: Akses dengan filter custom
        $response = $this->get(route('booking.analytics', ['filter' => 'custom', 'date' => $customDate->format('Y-m-d')])); // <-- Perbaikan Route

        $response->assertOk();
        $mainData = $response->viewData('mainData');

        // ASSERT 1: Memverifikasi Total Booking (Hanya 20 April)
        $this->assertEquals(7, $mainData['stats']['total'], 'Total booking custom harus 7.');

        // ASSERT 2: Memverifikasi Table Data hanya berisi 6 entry
        $this->assertCount(6, $mainData['table_data'], 'Table data harus berisi 6 booking.');

        // ASSERT 3: Memverifikasi Chart Data mencakup rentang 5 hari
        $this->assertCount(5, $mainData['labels'], 'Chart labels harus 5 untuk filter custom (rentang 5 hari).');

        // ASSERT 4: Memverifikasi data chart menunjukkan angka 6 pada posisi hari H (20 April)
        // 18 Apr (0), 19 Apr (1), 20 Apr (6), 21 Apr (0), 22 Apr (0). Data 6 ada di index 2.
        // Cek: Data 19 April (index 1) harus 1 (dari booking subDay)
        $this->assertEquals(1, $mainData['data'][1], 'Chart data untuk tanggal 19 April (subDay) harus 1.');

        // ASSERT 5: Memverifikasi filter yang digunakan
        $this->assertEquals('custom', $response->viewData('filter'), 'Filter yang digunakan harus custom.');
    }

    #[Test]
    public function it_returns_empty_data_for_no_bookings()
    {
        // Data kosong, tidak ada booking yang dibuat setelah RefreshDatabase

        // Aksi: Akses dengan filter bulanan default
        $response = $this->get(route('booking.analytics', [ // <-- Perbaikan Route
            'filter' => 'bulanan',
            'month' => date('m'),
            'year' => date('Y')
        ]));

        $response->assertOk();
        $mainData = $response->viewData('mainData');
        $insights = $response->viewData('insights');

        // ASSERT 1: Memverifikasi Total Booking adalah 0
        $this->assertEquals(0, $mainData['stats']['total'], 'Total booking harus 0 jika tidak ada data.');

        // ASSERT 2: Memverifikasi Table Data kosong
        $this->assertEmpty($mainData['table_data'], 'Table data harus kosong.');

        // ASSERT 3: Memverifikasi Insight memberikan pesan "Tidak ada data"
        $this->assertStringContainsString('Tidak ada data booking untuk periode ini.', $insights[0], 'Insight harus menunjukkan tidak ada data.');

        // ASSERT 4: Memverifikasi Average dan Peak adalah 0
        $this->assertEquals(0, $mainData['stats']['average']);
        $this->assertEquals(0, $mainData['stats']['peak']);

        // ASSERT 5: Memverifikasi type adalah 'bulanan' meskipun data kosong
        $this->assertEquals('bulanan', $mainData['type'], 'Type harus bulanan (default filter).');
    }
}
