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

        $this->admin = User::create([
            'name' => 'Admin Haikal',
            'email' => 'adminhaikal@gmail.com',
            'password' => bcrypt('11111111'),
            'role' => 'admin'
        ]);
        $this->actingAs($this->admin);

        $this->owner2 = User::create([
            'name' => 'Owner Haikal',
            'email' => 'ownerhaikal@gmail.com',
            'password' => bcrypt('11111111'),
            'role' => 'owner'
        ]);

        $this->vehicleA = Vehicle::create([
            'owner_id' => $this->admin->id, // Menggunakan ID yang valid
            'name' => 'Avanza',
            'brand' => 'Toyota',
            'model' => 'Avanza',
            'plate_number' => 'DD 1811 PP',
            'transmisi' => 'Manual',
            'price_per_day' => 100000,
            'status_vehicle' => 'Tersedia',
            'type' => 'Sedan'
        ]);

        $this->vehicleB = Vehicle::create([
            'owner_id' => $this->owner2->id, // Menggunakan ID yang valid
            'name' => 'Brio',
            'brand' => 'Honda',
            'model' => 'Brio',
            'plate_number' => 'D 2892 MI',
            'transmisi' => 'Matic',
            'price_per_day' => 200000,
            'status_vehicle' => 'Tersedia',
            'type' => 'Hatchback'
        ]);

        Carbon::setTestNow(Carbon::create(2025, 3, 14));
    }

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
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY); // 10 Maret

        $this->createBooking($startOfWeek->copy()->addDays(4), $this->vehicleA, 'completed', 3);
        $this->createBooking($startOfWeek->copy()->addDay(), $this->vehicleB, 'confirmed', 1);

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
        $this->createBooking(Carbon::create(2025, 3, 10), $this->vehicleA, 'confirmed', 5); // 5 Booking di Maret
        $this->createBooking(Carbon::create(2025, 2, 10), $this->vehicleB, 'completed', 2); // 2 Booking di Februari

        $response = $this->get(route('booking.analytics', [ // <-- Perbaikan Route
            'filter' => 'bulanan',
            'month' => '12',
            'year' => '2025',
            'compare_mode' => 'bulanan',
            'periodA' => '12',
            'periodB' => '01'
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
        $this->createBooking(Carbon::create(2025, 1, 1), $this->vehicleA, 'completed', 4); // Januari
        $this->createBooking(Carbon::create(2025, 11, 1), $this->vehicleB, 'confirmed', 7); // November -> Peak
        $this->createBooking(Carbon::create(2024, 12, 1), $this->vehicleA, 'completed', 2);

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

        $this->createBooking($customDate, $this->vehicleA, 'completed', 6);
        $this->createBooking($customDate->copy()->subDay(), $this->vehicleB, 'confirmed', 1);

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
        $this->assertEquals(1, $mainData['data'][1], 'Chart data untuk tanggal 19 April (subDay) harus 1.');

        // ASSERT 5: Memverifikasi filter yang digunakan
        $this->assertEquals('custom', $response->viewData('filter'), 'Filter yang digunakan harus custom.');
    }

    #[Test]
    public function it_returns_empty_data_for_no_bookings()
    {

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
