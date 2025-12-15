<?php

namespace Tests\Unit; // Diubah ke Feature Test

use App\Models\User;
use App\Models\Vehicle;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

// Ganti nama class agar lebih spesifik
class OwnerDetailBookingTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;
    protected Vehicle $vehicle;
    protected User $user; // Diubah dari $customer menjadi $user

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Buat user Owner
        $this->owner = User::create([
            'name' => 'Test Owner',
            'email' => 'owner@test.com',
            'password' => bcrypt('password'),
            'role' => 'owner'
        ]);
        $this->actingAs($this->owner);

        // 2. Buat User (Customer)
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
            'role' => 'user'
        ]);

        // 3. Buat Vehicle milik Owner
        $this->vehicle = Vehicle::create([
            'owner_id' => $this->owner->id,
            'name' => 'Test Mobil',
            'brand' => 'Honda',
            'type' => 'Sedan',
            'plate_number' => 'B 7777 XX',
            'price_per_day' => 500000,
            'status_vehicle' => 'Tersedia'
        ]);

        // Atur waktu sekarang ke 15 Maret 2025
        Carbon::setTestNow(Carbon::create(2025, 3, 15));
    }

    /**
     * @param int $count
     * @param Carbon $date
     * @return void
     */
    protected function createBookingsForAnalytics(int $count, Carbon $date): void
    {
        // Pastikan kolom NOT NULL terisi
        for ($i = 0; $i < $count; $i++) {
            Booking::create([
                'vehicle_id' => $this->vehicle->id,
                'user_id' => $this->user->id, // Gunakan $this->user
                'start_date' => $date,
                'end_date' => $date->copy()->addDay(),
                'status' => 'Completed', // Status selain 'dibatalkan'
                'total_payment' => 500000,
                'name' => 'Booking ' . $date->format('M d') . ' - ' . $i,
                'address' => 'Addr',
                'phone' => '123',
                'sim_image' => 'sim',
                'payment_method' => 'Transfer Bank',
                'deposit_amount' => 50000,
            ]);
        }
    }

    #[Test]
    public function analytics_dashboard_memfilter_data_berdasarkan_bulanan_default_owner()
    {
        // Setup Data (Maret 2025): Total 5 booking yang HARUS diambil
        $this->createBookingsForAnalytics(3, Carbon::create(2025, 3, 10));
        $this->createBookingsForAnalytics(2, Carbon::create(2025, 3, 20));

        // Booking Februari 2025: Total 1 booking yang HARUS difilter
        $this->createBookingsForAnalytics(1, Carbon::create(2025, 2, 10));
        $februaryBooking = Booking::whereMonth('start_date', 2)->first();

        // 1. Aksi: Akses dashboard 'DetailBooking@index' dengan filter bulanan (Maret 2025)
        // Ganti route jika nama route Anda berbeda (misalnya: 'owner.analytics')
        $response = $this->get(route('booking.analytics', [
            'filter' => 'bulanan',
            'month' => '03',
            'year' => '2025'
        ]));

        $response->assertOk();
        $mainData = $response->viewData('mainData');

        // ASSERT 1: Memverifikasi jumlah total booking harus 5 (Hanya Maret).
        $this->assertEquals(5, $mainData['stats']['total'], 'Total booking di stats harus 5 (hanya booking Maret milik owner).');

        // ASSERT 2: Memverifikasi data booking bulan Februari tidak muncul di daftar tabel
        $this->assertEmpty(
            collect($mainData['table_data'])->where('nama', $februaryBooking->name)->toArray(),
            'Booking dari bulan Februari tidak boleh muncul di table_data.'
        );

        // ASSERT 3: Memverifikasi status filter 'bulanan' terpilih di view
        // Periksa apakah input filter di view berisi nilai 'bulanan' yang terpilih
        $response->assertSeeInOrder([
            '<select name="filter">',
            '<option value="bulanan" selected',
            '</select>'
        ], false);

        // ASSERT 4: Memverifikasi hitungan data tabel sesuai dengan total (5)
        $this->assertCount(5, $mainData['table_data'], 'Table data harus berisi 5 booking dari bulan Maret.');

        // ASSERT 5: Memverifikasi Rata-rata booking
        // Bulan Maret 2025 memiliki 31 hari. Rata-rata = 5 / 31 = 0.161 -> 0.2 (karena calculateStats membulatkan ke 1 desimal)
        $expectedAverage = number_format(5 / Carbon::create(2025, 3, 1)->daysInMonth, 1);
        $this->assertEquals($expectedAverage, $mainData['stats']['average'], 'Rata-rata booking harus terhitung benar (5/31 dibulatkan).');
    }
}
