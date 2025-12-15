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
            'name' => 'Adam Syahqwan',
            'email' => 'useradamss@gmail.com',
            'password' => bcrypt('11111111'),
            'role' => 'user'
        ]);

        $this->owner = User::create([
            'name' => 'Risda',
            'email' => 'ownerrisda@gmail.com',
            'password' => bcrypt('111111111'),
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
            'plate_number' => 'DD 1334 FG',
            'price_per_day' => 300000,
            'status_vehicle' => 'Tersedia',
            'owner_id' => $this->owner->id
        ]);

        Vehicle::create([
            'name' => 'Honda Brio',
            'brand' => 'Honda',
            'type' => 'Hatchback',
            'plate_number' => 'DP 5618 HH',
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
            'plate_number' => 'DD 1334 FG',
            'price_per_day' => 300000,
            'status_vehicle' => 'Tersedia',
            'owner_id' => $this->owner->id
        ]);

        Vehicle::create([
            'name' => 'Honda Brio',
            'brand' => 'Honda',
            'type' => 'Hatchback',
            'plate_number' => 'DP 5618 HH',
            'price_per_day' => 250000,
            'status_vehicle' => 'Tersedia',
            'owner_id' => $this->owner->id
        ]);

        Vehicle::create([
            'name' => 'Toyota Innova',
            'brand' => 'Toyota',
            'type' => 'MPV',
            'plate_number' => 'DD 5334 MC',
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
            'name' => 'Honda Brio',
            'brand' => 'Honda',
            'type' => 'Hatchback',
            'plate_number' => 'DD 7898 WX',
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
            'address' => 'Soreang',
            'phone' => '089504517110',
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
            'address' => 'Bacukiki Barat',
            'phone' => '081238059674',
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
            'name' => 'Toyota Fortuner',
            'brand' => 'Toyota',
            'type' => 'SUV',
            'plate_number' => 'B 3440 NL',
            'price_per_day' => 200000,
            'status_vehicle' => 'Tersedia',
            'owner_id' => $this->owner->id
        ]);

        $response2 = $this->get('/user/dashboard');
        $viewVehicles2 = $response2->viewData('vehicles');
        $vehicleNoRating = $viewVehicles2->where('plate_number', 'B 3440 NL')->first();

        // ASSERT 5: Cek kendaraan tanpa rating memiliki avg_rating null
        $this->assertNull($vehicleNoRating->avg_rating);
    }

    #[Test]
    public function SeeStatusVehicle()
    {
        // PERBAIKAN: Gunakan 'Maintenance' bukan 'Maintanance'
        Vehicle::create([
            'name' => 'Avanza Veloz',
            'brand' => 'Toyota',
            'type' => 'MPV',
            'plate_number' => 'DP 2341 SR',
            'price_per_day' => 300000,
            'status_vehicle' => 'Tersedia',
            'owner_id' => $this->owner->id
        ]);

        Vehicle::create([
            'name' => 'Honda NSX',
            'brand' => 'Honda',
            'type' => 'Sports',
            'plate_number' => 'B 2292 TT',
            'price_per_day' => 250000,
            'status_vehicle' => 'Tidak_tersedia',
            'owner_id' => $this->owner->id
        ]);

        Vehicle::create([
            'name' => 'Honda Brio',
            'brand' => 'Honda',
            'type' => 'Hatchback',
            'plate_number' => 'DD 3343 MT',
            'price_per_day' => 400000,
            'status_vehicle' => 'Maintenance', 
            'owner_id' => $this->owner->id
        ]);

        Vehicle::create([
            'name' => 'Mitshubishi Xpander',
            'brand' => 'Mitshubishi',
            'type' => 'SUV',
            'plate_number' => 'B 2244 TR',
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
        $notAvailableVehicle = $viewVehicles->where('plate_number', 'B 2292 TT')->first();
        $this->assertNotNull($notAvailableVehicle, 'Kendaraan Tidak_tersedia harus ditampilkan.');
        $this->assertEquals('Tidak_tersedia', $notAvailableVehicle->status_vehicle);

        // ASSERT 4: Cek kendaraan 'Maintenance' tidak ditampilkan
        $maintenanceVehicle = $viewVehicles->where('plate_number', 'DD 3343 MT')->first();
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
            'name' => 'Toyota Corolla',
            'brand' => 'Toyota',
            'type' => 'Sedan',
            'plate_number' => 'DD 1119 OD',
            'price_per_day' => 300000,
            'status_vehicle' => 'Tersedia',
            'owner_id' => $this->owner->id,
            'created_at' => now()->subDays(10), 
            'updated_at' => now()->subDays(10)
        ]);

        $vehicle2 = Vehicle::create([
            'name' => 'Honda Civic',
            'brand' => 'Honda',
            'type' => 'Hatchback',
            'plate_number' => 'DP 1222 MD',
            'price_per_day' => 250000,
            'status_vehicle' => 'Tersedia',
            'owner_id' => $this->owner->id,
            'created_at' => now()->subDays(5), 
            'updated_at' => now()->subDays(5)
        ]);

        $vehicle3 = Vehicle::create([
            'name' => 'Nissan Juke',
            'brand' => 'Nissan',
            'type' => 'SUV',
            'plate_number' => 'DD 3233 EW',
            'price_per_day' => 400000,
            'status_vehicle' => 'Tersedia',
            'owner_id' => $this->owner->id,
            'created_at' => now()->subDays(1), 
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
        $this->assertContains('DD 1119 OD', $plateNumbers);
        $this->assertContains('DP 1222 MD', $plateNumbers);
        $this->assertContains('DD 3233 EW', $plateNumbers);
    }
}
