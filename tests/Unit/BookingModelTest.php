<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class BookingModelTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $vehicle;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat user untuk booking
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Buat vehicle dengan hanya field yang diperlukan untuk relasi
        // Asumsikan Vehicle memiliki field minimal: owner_id, name, price_per_day
        $this->vehicle = Vehicle::create([
            'owner_id' => $this->user->id,
            'name' => 'Test Vehicle',
            'price_per_day' => 500000,
            // Tambahkan field wajib lainnya jika ada
            'type' => 'Sedan',
            'brand' => 'Toyota',
            'plate_number' => 'B 1234 XYZ',
            'status_vehicle' => 'Tersedia',

        ]);
    }

    #[Test]
    public function test_booking_model_accessors()
    {
        // Data dasar booking dengan SEMUA field dari $fillable
        $baseBookingData = [
            'vehicle_id' => $this->vehicle->id,
            'user_id' => $this->user->id,
            'name' => 'John Doe',
            'address' => 'Jl. Contoh No. 123',
            'phone' => '081234567890',
            'sim_image' => 'sim_images/test.jpg',
            'payment_method' => 'Transfer Bank',
            'status' => 'Pending',
            'total_payment' => 1500000,
            'review' => null,
            'rating' => null,
            'start_date' => Carbon::now()->addDays(1),
            'end_date' => Carbon::now()->addDays(3),
        ];

        // Buat 5 booking dengan kondisi berbeda untuk 5 assert
        $bookings = [];

        // 1. Booking baru (tidak expired, belum mulai)
        $booking1 = new Booking($baseBookingData);
        $booking1->created_at = Carbon::now()->subHours(12); // 12 jam lalu
        $booking1->save();

        // 2. Booking expired (lebih dari 24 jam)
        $booking2 = new Booking(array_merge($baseBookingData, [
            'status' => 'Pending'
        ]));
        $booking2->created_at = Carbon::now()->subHours(25); // 25 jam lalu
        $booking2->save();

        // 3. Booking dengan status Disetujui dan sudah selesai (end_date lewat)
        $booking3 = new Booking(array_merge($baseBookingData, [
            'status' => 'Disetujui',
            'start_date' => Carbon::now()->subDays(5),
            'end_date' => Carbon::now()->subDays(2),
        ]));
        $booking3->created_at = Carbon::now()->subHours(12);
        $booking3->save();

        // 4. Booking dengan status Disetujui dan masih aktif
        $booking4 = new Booking(array_merge($baseBookingData, [
            'status' => 'Disetujui',
            'start_date' => Carbon::now()->subDays(1),
            'end_date' => Carbon::now()->addDays(2),
        ]));
        $booking4->created_at = Carbon::now()->subHours(12);
        $booking4->save();

        // 5. Booking dengan status Dibatalkan
        $booking5 = new Booking(array_merge($baseBookingData, [
            'status' => 'Dibatalkan',
        ]));
        $booking5->created_at = Carbon::now()->subHours(12);
        $booking5->save();

        // ASSERT 1: Booking baru tidak expired
        $this->assertFalse(
            $booking1->is_expired,
            'Booking baru seharusnya tidak expired'
        );

        // ASSERT 2: Booking 24+ jam yang lalu expired
        $this->assertTrue(
            $booking2->is_expired,
            'Booking 25 jam yang lalu seharusnya expired'
        );

        // ASSERT 3: Booking selesai menampilkan status COMPLETED
        $this->assertEquals(
            'COMPLETED',
            $booking3->display_status,
            'Booking Disetujui dengan end_date lewat seharusnya COMPLETED'
        );

        // ASSERT 4: Booking aktif bisa dibatalkan
        $this->assertTrue(
            $booking4->can_be_cancelled,
            'Booking Disetujui dengan end_date future seharusnya bisa dibatalkan'
        );

        // ASSERT 5: Booking dibatalkan menampilkan status Dibatalkan
        $this->assertEquals(
            'Dibatalkan',
            $booking5->display_status,
            'Booking dengan status Dibatalkan seharusnya menampilkan Dibatalkan'
        );
    }
}
