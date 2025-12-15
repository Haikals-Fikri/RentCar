<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class BookingTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    private User $user;
    private User $owner;
    private Vehicle $vehicle;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::create([
            'name' => 'Risda',
            'email' => 'ownerrisda@gmail.com',
            'password' => bcrypt('11111111'),
            'role' => 'owner'
        ]);

        $this->user = User::create([
            'name' => 'Adam',
            'email' => 'useradam@gmial.com',
            'password' => bcrypt('11111111'),
            'role' => 'user'
        ]);

        $this->vehicle = Vehicle::create([
            'name' => 'Toyota Avanza',
            'brand' => 'Toyota',
            'type' => 'MPV',
            'plate_number' => 'DD 8976 CK',
            'price_per_day' => 300000,
            'status_vehicle' => 'Tersedia',
            'owner_id' => $this->owner->id
        ]);

        Storage::fake('public');
    }


    #[Test]
    public function user_dapat_melakukan_booking_dengan_data_valid()
    {
        $this->actingAs($this->user);

        $simFile = UploadedFile::fake()->create('sim.jpg', 100, 'image/jpeg');

        $bookingData = [
            'name' => 'Adam',
            'address' => 'Jl. Bumi Harapan No. 6',
            'phone' => '081238059674',
            'start_date' => now()->addDays(1)->format('Y-m-d'),
            'end_date' => now()->addDays(3)->format('Y-m-d'),
            'sim_image' => $simFile,
            'payment_method' => 'Bayar Di Tempat'
        ];

        $response = $this->post("/booking/{$this->vehicle->id}/store", $bookingData);

        // ASSERT 1: Cek redirect ke user.bookings
        $response->assertRedirect(route('user.bookings'));

        // ASSERT 2: Cek success message
        $response->assertSessionHas('success');

        // ASSERT 3: Cek booking dibuat di database
        $this->assertDatabaseCount('bookings', 1);

        // ASSERT 4: Cek status vehicle berubah
        $this->assertDatabaseHas('vehicles', [
            'id' => $this->vehicle->id,
            'status_vehicle' => 'Tidak_tersedia'
        ]);

        // ASSERT 5: Cek user_id booking sesuai
        $booking = Booking::first();
        $this->assertEquals($this->user->id, $booking->user_id);
    }

    #[Test]
    public function booking_gagal_jika_kendaraan_tidak_tersedia()
    {
        $this->vehicle->update(['status_vehicle' => 'Tidak_tersedia']);
        $this->actingAs($this->user);

        $simFile = UploadedFile::fake()->create('sim.jpg', 100, 'image/jpeg');

        $bookingData = [
            'name' => 'Haikal',
            'address' => 'Jl. Bumi Harapan No. 8',
            'phone' => '089504517110',
            'start_date' => now()->addDays(1)->format('Y-m-d'),
            'end_date' => now()->addDays(3)->format('Y-m-d'),
            'sim_image' => $simFile,
            'payment_method' => 'Transfer Bank'
        ];

        $response = $this->post("/booking/{$this->vehicle->id}/store", $bookingData);

        // ASSERT 1: Cek redirect back (karena error validasi)
        $response->assertRedirect();

        // ASSERT 2: Cek error message
        $response->assertSessionHas('error');

        // ASSERT 3: Cek tidak ada booking dibuat
        $this->assertDatabaseCount('bookings', 0);

        // ASSERT 4: Cek status vehicle tetap tidak tersedia
        $this->vehicle->refresh();
        $this->assertEquals('Tidak_tersedia', $this->vehicle->status_vehicle);

        // ASSERT 5: Cek session memiliki input lama
        $response->assertSessionHasInput('name');
    }

    #[Test]
    public function user_dapat_membatalkan_booking_yang_milikinya()
    {
        $booking = Booking::create([
            'name' => 'Risda',
            'address' => 'Lompoe No. 7',
            'phone' => '089654109903',
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(3),
            'sim_image' => 'sims/test.jpg',
            'payment_method' => 'Transfer Bank',
            'vehicle_id' => $this->vehicle->id,
            'user_id' => $this->user->id,
            'status' => 'Disetujui',
            'total_payment' => 900000
        ]);

        $this->vehicle->update(['status_vehicle' => 'Tidak_tersedia']);
        $this->actingAs($this->user);

        $response = $this->post("/booking/{$booking->id}/cancel");

        // ASSERT 1: Cek redirect back
        $response->assertRedirect();

        // ASSERT 2: Cek success message
        $response->assertSessionHas('success');

        // ASSERT 3: Cek status booking berubah
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'Dibatalkan User'
        ]);

        // ASSERT 4: Cek status vehicle kembali tersedia
        $this->vehicle->refresh();
        $this->assertEquals('Tersedia', $this->vehicle->status_vehicle);

        // ASSERT 5: Cek booking tidak dihapus
        $this->assertNotNull(Booking::find($booking->id));
    }

    #[Test]
    public function user_dapat_melihat_daftar_booking_milikinya()
    {
        Booking::create([
            'name' => 'Haikal',
            'address' => 'Jl. Bumi Harapan No. 8',
            'phone' => '085904517110',
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(3),
            'sim_image' => 'sims/test1.jpg',
            'payment_method' => 'Transfer Bank',
            'vehicle_id' => $this->vehicle->id,
            'user_id' => $this->user->id,
            'status' => 'Disetujui',
            'total_payment' => 900000
        ]);

        Booking::create([
            'name' => 'Adam',
            'address' => 'Jl. Bumi Harapan No. 8',
            'phone' => '081238059674',
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(7),
            'sim_image' => 'sims/test2.jpg',
            'payment_method' => 'Cash',
            'vehicle_id' => $this->vehicle->id,
            'user_id' => $this->user->id,
            'status' => 'Completed',
            'total_payment' => 900000
        ]);

        $this->actingAs($this->user);

        $response = $this->get('/user/bookings');

        // ASSERT 1: Cek response status 200
        $response->assertOk();

        // ASSERT 2: Cek data bookings di view
        $response->assertViewHas('bookings');

        // ASSERT 3: Cek jumlah booking di view
        $viewBookings = $response->viewData('bookings');
        $this->assertCount(2, $viewBookings);

        // ASSERT 4: Cek hanya booking milik user yang ditampilkan
        foreach ($viewBookings as $booking) {
            $this->assertEquals($this->user->id, $booking->user_id);
        }

        // ASSERT 5: Cek view yang digunakan sesuai controller
        $response->assertViewIs('layouts.user.booking-list');
    }

    #[Test]
    public function user_tidak_dapat_membatalkan_booking_bukan_milikinya()
    {
        $otherUser = User::create([
            'name' => 'Adam',
            'email' => 'useradam2@gmail.com',
            'password' => bcrypt('11111111'),
            'role' => 'user'
        ]);

        $booking = Booking::create([
            'name' => 'Adam',
            'address' => 'Jl. Agus Salim No. 12',
            'phone' => '081238009976',
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(3),
            'sim_image' => 'sims/test.jpg',
            'payment_method' => 'Transfer Bank',
            'vehicle_id' => $this->vehicle->id,
            'user_id' => $otherUser->id,
            'status' => 'Disetujui',
            'total_payment' => 900000
        ]);

        $this->vehicle->update(['status_vehicle' => 'Tidak_tersedia']);
        $this->actingAs($this->user);

        $response = $this->post("/booking/{$booking->id}/cancel");

        // ASSERT 1: Cek redirect back
        $response->assertRedirect();

        // ASSERT 2: Cek error message
        $response->assertSessionHas('error');

        // ASSERT 3: Cek status booking TIDAK berubah
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'Disetujui'
        ]);

        // ASSERT 4: Cek user ID tetap sama
        $booking->refresh();
        $this->assertEquals($otherUser->id, $booking->user_id);

        // ASSERT 5: Cek status vehicle tetap tidak tersedia
        $this->vehicle->refresh();
        $this->assertSame('Tidak_tersedia', $this->vehicle->status_vehicle);
    }
}
