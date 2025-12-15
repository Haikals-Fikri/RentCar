<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class VehicleTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    private User $owner;
    private Vehicle $vehicle;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup owner
        $this->owner = User::create([
            'name' => 'Test Owner',
            'email' => 'owner@test.com',
            'password' => bcrypt('password'),
            'role' => 'owner'
        ]);

        // Set owner_id di session
        Session::put('owner_id', $this->owner->id);

        // Setup vehicle
        $this->vehicle = Vehicle::create([
            'name' => 'Toyota Avanza',
            'brand' => 'Toyota',
            'type' => 'MPV',
            'plate_number' => 'B 1234 ABC',
            'price_per_day' => 300000,
            'status_vehicle' => 'Tersedia',
            'owner_id' => $this->owner->id
        ]);

        Storage::fake('public');
    }

    #[Test]
    public function owner_dapat_melihat_daftar_kendaraan_miliknya()
    {
        // Buat kendaraan tambahan
        Vehicle::create([
            'name' => 'Honda Brio',
            'brand' => 'Honda',
            'type' => 'Hatchback',
            'plate_number' => 'B 5678 DEF',
            'price_per_day' => 250000,
            'status_vehicle' => 'Tersedia',
            'owner_id' => $this->owner->id
        ]);

        $response = $this->get('/owner/dashboard');

        $response->assertOk();
        $response->assertViewIs('layouts.owner.dashboard-owner');
        $response->assertViewHas('vehicles');
        $viewVehicles = $response->viewData('vehicles');
        $this->assertCount(2, $viewVehicles);
        foreach ($viewVehicles as $vehicle) {
            $this->assertEquals($this->owner->id, $vehicle->owner_id);
        }
    }

    #[Test]
    public function owner_dapat_menambahkan_kendaraan_baru()
    {
        // GUNAKAN FILE TANPA GD EXTENSION
        $vehicleData = [
            'name' => 'Mitsubishi Xpander',
            'brand' => 'Mitsubishi',
            'type' => 'MPV',
            'plate_number' => 'B 9999 XYZ',
            'seat' => '7',
            'transmission' => 'Automatic',
            'fuel_type' => 'Petrol',
            'year' => '2023',
            'price_per_day' => 350000,
            // OPSI 1: Tanpa image (nullable)
            // 'image' => null
        ];

        // OPSI 2: Jika ingin tetap test upload, gunakan file tanpa GD
        // $vehicleData['image'] = UploadedFile::fake()->create('vehicle.jpg', 100, 'image/jpeg');

        $response = $this->post('/vehicles', $vehicleData);

        $response->assertRedirect(route('owner.dashboard'));
        $response->assertSessionHas('success');
        $this->assertDatabaseCount('vehicles', 2);
        $this->assertDatabaseHas('vehicles', [
            'name' => 'Mitsubishi Xpander',
            'plate_number' => 'B 9999 XYZ',
            'owner_id' => $this->owner->id
        ]);
        $newVehicle = Vehicle::where('plate_number', 'B 9999 XYZ')->first();
        $this->assertEquals('Tersedia', $newVehicle->status_vehicle);
    }

    #[Test]
    public function owner_dapat_mengupdate_data_kendaraan()
    {
        $updateData = [
            'name' => 'Toyota Avanza Updated',
            'brand' => 'Toyota',
            'type' => 'MPV',
            'plate_number' => 'B 1234 ABC',
            'price_per_day' => 350000,
            'seat' => '7',
            'transmission' => 'Manual'
        ];

        $response = $this->put("/vehicles/{$this->vehicle->id}", $updateData);

        $response->assertRedirect(route('owner.dashboard'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('vehicles', [
            'id' => $this->vehicle->id,
            'name' => 'Toyota Avanza Updated',
            'price_per_day' => 350000
        ]);
        $this->vehicle->refresh();
        $this->assertSame('Toyota', $this->vehicle->brand);
        $this->assertSame('MPV', $this->vehicle->type);
        $this->assertEquals('B 1234 ABC', $this->vehicle->plate_number);
    }

    #[Test]
    public function OwnerVehicleSeeVehicle()
    {
        $otherowner = User::create([
            'name' => 'Owner Lain',
            'email' => 'ownerlain@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'owner'
        ]);
        Vehicle::create([
            'name' => 'Honda Jazz',
            'brand' => 'Honda',
            'type' => 'Hatchback',
            'plate_number' => 'B 4321 CDE',
            'price_per_day' => 280000,
            'status_vehicle' => 'Tersedia',
            'owner_id' => $otherowner->id
        ]);

        Vehicle::create([
            'name' => 'Suzuki Ertiga',
            'brand' => 'Suzuki',
            'type' => 'MPV',
            'plate_number' => 'B 8765 FGH',
            'price_per_day' => 320000,
            'status_vehicle' => 'Tersedia',
            'owner_id' => $this->owner->id
        ]);

        $vehicleSee = Vehicle::where('owner_id', $this->owner->id)->get();

        // assert 1
        $this->assertCount(2, $vehicleSee);

        foreach ($vehicleSee as $vehicle) {
            // assert 2
            $this->assertEquals($this->owner->id, $vehicle->owner_id);
        }

        // assert 3
        $AnotherOwnerVehicle = Vehicle::where('owner_id', $otherowner->id)->get();
        $this->assertNotEquals($AnotherOwnerVehicle, $vehicleSee->toArray());



        // asssert 4
       $PlateNumber = $vehicleSee->pluck('plate_number')->toArray();
       $this->assertContains('B 1234 ABC', $PlateNumber);
       $this->assertContains('B 8765 FGH', $PlateNumber);

       // assert 5
       $this->assertDatabaseHas('vehicles', [
        'name' => 'Toyota Avanza',
        'plate_number' => 'B 1234 ABC',
        'owner_id' => $this->owner->id
       ]);
       $this->assertNotContains('B 4321 CDE', $PlateNumber);

    }
    #[Test]
    public function owner_dapat_menghapus_kendaraan()
    {
        $vehicleId = $this->vehicle->id;

        $response = $this->delete("/vehicles/{$vehicleId}");

        $response->assertRedirect(route('owner.dashboard'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('vehicles', [
            'id' => $vehicleId
        ]);
        $this->assertDatabaseCount('vehicles', 0);
        $deletedVehicle = Vehicle::find($vehicleId);
        $this->assertNull($deletedVehicle);
    }
}
