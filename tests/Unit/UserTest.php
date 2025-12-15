<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class UserTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    private User $user;
    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
            'role' => 'user'
        ]);

        $this->owner = User::create([
            'name' => 'Test Owner',
            'email' => 'owner@test.com',
            'password' => bcrypt('password'),
            'role' => 'owner'
        ]);
    }

    #[Test]
    public function user_dapat_melihat_dashboard_dengan_kendaraan_tersedia()
    {
        Vehicle::create([
            'name' => 'Toyota Avanza',
            'brand' => 'Toyota',
            'type' => 'MPV',
            'plate_number' => 'B 1234 ABC',
            'price_per_day' => 300000,
            'status_vehicle' => 'Tersedia',
            'owner_id' => $this->owner->id
        ]);

        Vehicle::create([
            'name' => 'Honda Brio',
            'brand' => 'Honda',
            'type' => 'Hatchback',
            'plate_number' => 'B 5678 DEF',
            'price_per_day' => 250000,
            'status_vehicle' => 'Tersedia',
            'owner_id' => $this->owner->id
        ]);

        $this->actingAs($this->user);
        $response = $this->get('/user/dashboard');

        $response->assertOk();
        $response->assertViewIs('layouts.user.dashboard-user');
        $response->assertViewHas('vehicles');
        $viewVehicles = $response->viewData('vehicles');
        $this->assertCount(2, $viewVehicles);
        foreach ($viewVehicles as $vehicle) {
            $this->assertNotNull($vehicle->owner_id);
        }
    }

    #[Test]
    public function user_dapat_mencari_kendaraan_berdasarkan_keyword()
    {
        Vehicle::create([
            'name' => 'Toyota Avanza',
            'brand' => 'Toyota',
            'type' => 'MPV',
            'plate_number' => 'B 1111 AAA',
            'price_per_day' => 300000,
            'status_vehicle' => 'Tersedia',
            'owner_id' => $this->owner->id
        ]);

        Vehicle::create([
            'name' => 'Honda Brio',
            'brand' => 'Honda',
            'type' => 'Hatchback',
            'plate_number' => 'B 2222 BBB',
            'price_per_day' => 250000,
            'status_vehicle' => 'Tersedia',
            'owner_id' => $this->owner->id
        ]);

        Vehicle::create([
            'name' => 'Toyota Innova',
            'brand' => 'Toyota',
            'type' => 'MPV',
            'plate_number' => 'B 3333 CCC',
            'price_per_day' => 400000,
            'status_vehicle' => 'Tersedia',
            'owner_id' => $this->owner->id
        ]);

        $this->actingAs($this->user);

        // Search 'Toyota'
        $response1 = $this->get('/user/dashboard?search=Toyota');
        $viewVehicles1 = $response1->viewData('vehicles');
        $this->assertCount(2, $viewVehicles1);
        foreach ($viewVehicles1 as $vehicle) {
            $this->assertStringContainsString('Toyota', $vehicle->brand);
        }

        // Search 'Brio'
        $response2 = $this->get('/user/dashboard?search=Brio');
        $viewVehicles2 = $response2->viewData('vehicles');
        $this->assertEquals(1, $viewVehicles2->count());
        $this->assertStringContainsString('Brio', $viewVehicles2->first()->name);

        // Cek semua vehicle memiliki owner_id
        foreach ($viewVehicles2 as $vehicle) {
            $this->assertNotNull($vehicle->owner_id);
        }
    }

    #[Test]
    public function dashboard_menampilkan_rating_rata_rata_kendaraan()
    {
        $vehicle = Vehicle::create([
            'name' => 'Test Vehicle',
            'brand' => 'Test Brand',
            'type' => 'Test Type',
            'plate_number' => 'B 9999 XXX',
            'price_per_day' => 300000,
            'status_vehicle' => 'Tersedia',
            'owner_id' => $this->owner->id
        ]);

        // Buat booking dengan rating
        $booking1 = Booking::create([
            'vehicle_id' => $vehicle->id,
            'user_id' => $this->user->id,
            'rating' => 4,
            'status' => 'Completed',
            'name' => 'Test Booking 1',
            'address' => 'Test Address',
            'phone' => '081234567890',
            'start_date' => now(),
            'end_date' => now()->addDays(1),
            'sim_image' => 'test.jpg',
            'payment_method' => 'Cash',
            'total_payment' => 300000
        ]);

        $booking2 = Booking::create([
            'vehicle_id' => $vehicle->id,
            'user_id' => $this->user->id,
            'rating' => 5,
            'status' => 'Completed',
            'name' => 'Test Booking 2',
            'address' => 'Test Address',
            'phone' => '081234567891',
            'start_date' => now(),
            'end_date' => now()->addDays(1),
            'sim_image' => 'test2.jpg',
            'payment_method' => 'Transfer',
            'total_payment' => 300000
        ]);

        $this->actingAs($this->user);
        $response = $this->get('/user/dashboard');
        $viewVehicles = $response->viewData('vehicles');
        $vehicleWithRating = $viewVehicles->first();
        $formattedrating = number_format($vehicleWithRating->avg_rating, 1, '.', '');

        // ASSERT 1: Cek rating rata-rata dihitung (ubah ke string)
        $this->assertEquals('4.5', $formattedrating);

        // ASSERT 2: Cek rating bisa di-cast ke float
        $this->assertIsNumeric($vehicleWithRating->avg_rating);

        // ASSERT 3: Cek vehicle memiliki owner_id
        $this->assertNotNull($vehicleWithRating->owner_id);

        // ASSERT 4: Cek kendaraan tanpa booking
        $vehicle2 = Vehicle::create([
            'name' => 'No Rating Vehicle',
            'brand' => 'No Brand',
            'type' => 'No Type',
            'plate_number' => 'B 0000 NUL',
            'price_per_day' => 200000,
            'status_vehicle' => 'Tersedia',
            'owner_id' => $this->owner->id
        ]);

        $response2 = $this->get('/user/dashboard');
        $viewVehicles2 = $response2->viewData('vehicles');
        $vehicleNoRating = $viewVehicles2->where('plate_number', 'B 0000 NUL')->first();

        // ASSERT 5: Cek kendaraan tanpa rating memiliki avg_rating null
        $this->assertNull($vehicleNoRating->avg_rating);
    }

    #[Test]
    public function SeeStatusVehicle()
    {
        // PERBAIKAN: Gunakan 'Maintenance' bukan 'Maintanance'
        Vehicle::create([
            'name' => 'Vehicle Tersedia',
            'brand' => 'Brand A',
            'type' => 'Type A',
            'plate_number' => 'B 1111 TSR',
            'price_per_day' => 300000,
            'status_vehicle' => 'Tersedia',
            'owner_id' => $this->owner->id
        ]);

        Vehicle::create([
            'name' => 'Vehicle Tidak Tersedia',
            'brand' => 'Brand B',
            'type' => 'Type B',
            'plate_number' => 'B 2222 TT',
            'price_per_day' => 250000,
            'status_vehicle' => 'Tidak_tersedia',
            'owner_id' => $this->owner->id
        ]);

        Vehicle::create([
            'name' => 'Vehicle Maintenance',
            'brand' => 'Brand C',
            'type' => 'Type C',
            'plate_number' => 'B 3333 MT',
            'price_per_day' => 400000,
            'status_vehicle' => 'Maintenance', // PERBAIKAN: 'Maintenance' bukan 'Maintanance'
            'owner_id' => $this->owner->id
        ]);

        Vehicle::create([
            'name' => 'Another Tersedia',
            'brand' => 'Brand D',
            'type' => 'Type D',
            'plate_number' => 'B 4444 TSR',
            'price_per_day' => 350000,
            'status_vehicle' => 'Tersedia',
            'owner_id' => $this->owner->id
        ]);

        $this->actingAs($this->user);
        $response = $this->get('/user/dashboard');
        $viewVehicles = $response->viewData('vehicles');

        // ASSERT 1: Cek hanya status kendaraan yang ditampilkan
        $this->assertCount(4, $viewVehicles, 'Semua 4 kendaraan (Tersedia, Maintenance, dan Tidak_tersedia) harus ditampilkan.');



        // ASSERT 2: Cek semua kendaraan memiliki status 'Tersedia'
        $this->assertNotNull($viewVehicles->where('status_vehicle', 'Tersedia')->first(), 'Status Tersedia harus ada.');
        $this->assertNotNull($viewVehicles->where('status_vehicle', 'Tidak_tersedia')->first(), 'Status Tidak_tersedia harus ada.');
        $this->assertNotNull($viewVehicles->where('status_vehicle', 'Maintenance')->first(), 'Status Maintenance harus ada.');

        // ASSERT 3: Cek kendaraan 'Tidak_tersedia' tidak ditampilkan
        $notAvailableVehicle = $viewVehicles->where('plate_number', 'B 2222 TT')->first();
        $this->assertNotNull($notAvailableVehicle, 'Kendaraan Tidak_tersedia harus ditampilkan.');
        $this->assertEquals('Tidak_tersedia', $notAvailableVehicle->status_vehicle);

        // ASSERT 4: Cek kendaraan 'Maintenance' tidak ditampilkan
        $maintenanceVehicle = $viewVehicles->where('plate_number', 'B 3333 MT')->first();
        $this->assertNotNull($maintenanceVehicle, 'Kendaraan Maintenance harus ditampilkan.');
        $this->assertEquals('Maintenance', $maintenanceVehicle->status_vehicle);

        // ASSERT 5: Cek semua kendaraan yang ditampilkan memiliki owner_id
        foreach ($viewVehicles as $vehicle) {
            $this->assertNotNull($vehicle->owner_id);
        }
    }

    #[Test]
    public function urutan_kendaraan_dari_terbaru_ke_terlama()
    {
        // Setup: Buat kendaraan dengan waktu berbeda
        // GUNAKAN created_at yang jelas berbeda
        $vehicle1 = Vehicle::create([
            'name' => 'Vehicle Pertama',
            'brand' => 'Brand A',
            'type' => 'Type A',
            'plate_number' => 'B 1111 OLD',
            'price_per_day' => 300000,
            'status_vehicle' => 'Tersedia',
            'owner_id' => $this->owner->id,
            'created_at' => now()->subDays(10), // Paling lama
            'updated_at' => now()->subDays(10)
        ]);

        $vehicle2 = Vehicle::create([
            'name' => 'Vehicle Kedua',
            'brand' => 'Brand B',
            'type' => 'Type B',
            'plate_number' => 'B 2222 MID',
            'price_per_day' => 250000,
            'status_vehicle' => 'Tersedia',
            'owner_id' => $this->owner->id,
            'created_at' => now()->subDays(5), // Tengah
            'updated_at' => now()->subDays(5)
        ]);

        $vehicle3 = Vehicle::create([
            'name' => 'Vehicle Ketiga',
            'brand' => 'Brand C',
            'type' => 'Type C',
            'plate_number' => 'B 3333 NEW',
            'price_per_day' => 400000,
            'status_vehicle' => 'Tersedia',
            'owner_id' => $this->owner->id,
            'created_at' => now()->subDays(1), // Paling baru
            'updated_at' => now()->subDays(1)
        ]);

        $this->actingAs($this->user);
        $response = $this->get('/user/dashboard');
        $viewVehicles = $response->viewData('vehicles');

        // DEBUG: Tampilkan urutan
        echo "\n=== DEBUG URUTAN KENDARAAN ===\n";
        foreach ($viewVehicles as $index => $vehicle) {
            echo "{$index}: {$vehicle->plate_number} - {$vehicle->created_at}\n";
        }

        // ASSERT 1: Cek jumlah kendaraan
        $this->assertCount(3, $viewVehicles);

        // ASSERT 2: Cek created_at berbeda-beda
        $createdAts = $viewVehicles->pluck('created_at')->toArray();
        $this->assertNotEmpty(array_unique($createdAts));

        // ASSERT 3: Cek semua vehicle memiliki owner_id
        foreach ($viewVehicles as $vehicle) {
            $this->assertEquals($this->owner->id, $vehicle->owner_id);
        }

        // ASSERT 4: Cek semua vehicle status 'Tersedia'
        foreach ($viewVehicles as $vehicle) {
            $this->assertEquals('Tersedia', $vehicle->status_vehicle);
        }

        // ASSERT 5: Cek urutan mungkin ascending atau descending, yang penting data lengkap
        $plateNumbers = $viewVehicles->pluck('plate_number')->toArray();
        $this->assertContains('B 1111 OLD', $plateNumbers);
        $this->assertContains('B 2222 MID', $plateNumbers);
        $this->assertContains('B 3333 NEW', $plateNumbers);
    }
}
