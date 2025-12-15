<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\OwnerProfile;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ProfileTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    private User $user;
    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'user']);
        $this->owner = User::factory()->create(['role' => 'owner']);

        Storage::fake('public');
    }

    #[Test]
    public function user_dapat_mengelola_profil_pribadi()
    {
        $this->actingAs($this->user);

        // 1. Update dengan foto - GUNAKAN create() bukan image()
        try {
            $photoFile = UploadedFile::fake()->image('profile.jpg', 100, 100);
        } catch (\LogicException $e) {
            // Jika image() error karena GD, gunakan create()
            $photoFile = UploadedFile::fake()->create('profile.jpg', 100, 'image/jpeg');
        }

        $photoData = [
            'name' => 'Risda',
            'email' => 'risda@gmail.com',
            'full_name' => 'Risda',
            'phone_number' => '089654109903',
            'photo' => $photoFile
        ];

        $response = $this->put('/profile/update', $photoData);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $profile = UserProfile::where('user_id', $this->user->id)->first();

        if ($profile && $profile->photo) {
            echo "\nPhoto path in database: " . $profile->photo;

            $possiblePaths = [
                $profile->photo,
                str_replace('UserProfile/', '', $profile->photo),
                'UserProfile/' . basename($profile->photo),
                basename($profile->photo)
            ];

            foreach ($possiblePaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    echo "\nFile exists at: " . $path;
                }
            }

            $allFiles = Storage::disk('public')->allFiles();
            echo "\nAll files in storage: " . implode(', ', $allFiles);
        }

        $this->assertNotNull($profile);
        $this->assertNotNull($profile->photo);

        $fileExists = false;
        $allFiles = Storage::disk('public')->allFiles();

        foreach ($allFiles as $file) {
            if (str_contains($file, basename($profile->photo))) {
                $fileExists = true;
                break;
            }
        }

        $this->assertTrue($fileExists, 'File photo should exist in storage');
    }

    #[Test]
    public function owner_dapat_mengelola_profil_bisnis()
    {
        $this->actingAs($this->owner);

        try {
            $photoFile = UploadedFile::fake()->image('owner.jpg', 100, 100);
        } catch (\LogicException $e) {
            $photoFile = UploadedFile::fake()->create('owner.jpg', 100, 'image/jpeg');
        }

        $photoData = [
            'name' => 'Owner Risda',
            'email' => 'ownerrisda@gmail.com',
            'owner_name' => 'Owner Risda',
            'business_name' => 'CV.Rentcar',
            'phone_number' => '089654109903',
            'photo' => $photoFile
        ];

        $response = $this->post('/profile/owner', $photoData);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Debug
        $profile = OwnerProfile::where('owner_id', $this->owner->id)->first();

        if ($profile && $profile->photo) {
            echo "\nOwner photo path in database: " . $profile->photo;

            $allFiles = Storage::disk('public')->allFiles();
            echo "\nAll files in storage: " . implode(', ', $allFiles);
        }

        $this->assertNotNull($profile);
        $this->assertNotNull($profile->photo);

        // Alternative assertion
        $hasPhoto = Storage::disk('public')->allFiles('OwnerProfile/');
        $this->assertNotEmpty($hasPhoto, 'Should have photo in OwnerProfile directory');
    }

    #[Test]
    public function user_dapat_melihat_profil_owner_dengan_kendaraan()
    {
        OwnerProfile::create([
            'owner_id' => $this->owner->id,
            'owner_name' => 'Rentcar',
            'business_name' => 'CV. Kokorentcar',
        ]);

        Vehicle::create([
            'name' => 'Toyota Avanza',
            'brand' => 'Toyota',
            'type' => 'MPV',
            'plate_number' => 'DD 1131 FF',
            'price_per_day' => 300000,
            'status_vehicle' => 'Tersedia',
            'owner_id' => $this->owner->id
        ]);

        Vehicle::create([
            'name' => 'Honda Brio',
            'brand' => 'Honda',
            'type' => 'Hatchback',
            'plate_number' => 'DD 2422 RB',
            'price_per_day' => 250000,
            'status_vehicle' => 'Tersedia',
            'owner_id' => $this->owner->id
        ]);

        $response = $this->actingAs($this->user)
            ->get("/user/{$this->owner->id}/profile");

        $response->assertOk();
        $response->assertViewIs('layouts.user.user-owner-profile');
        $response->assertViewHas('owner');

        $viewOwner = $response->viewData('owner');
        $this->assertEquals('owner', $viewOwner->role);
        $this->assertEquals($this->owner->id, $viewOwner->id);
        $this->assertNotEmpty($viewOwner->vehicles);
        $this->assertCount(2, $viewOwner->vehicles);
    }

    #[Test]
    public function update_profil_dengan_data_lengkap()
    {
        $this->actingAs($this->user);

        $fullData = [
            'name' => 'User Risda',
            'email' => 'userrisda@test.com',
            'full_name' => 'User Risda',
            'phone_number' => '089654109903',
            'address' => 'Jl. Lompoe No. 9',
            'city' => 'Parepare',
            'province' => 'Sulawesi Selatan',
            'postal_code' => '91161',
            'date_of_birth' => '2005-10-20',
            'gender' => 'Perempuan'
        ];

        $response = $this->put('/profile/update', $fullData);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $profile = UserProfile::where('user_id', $this->user->id)->first();
        $this->assertNotNull($profile);
        $this->assertEquals('User Risda', $profile->full_name);
        $this->assertEquals('Parepare', $profile->city);
        $this->assertEquals('Sulawesi Selatan', $profile->province);
        $this->assertEquals('91161', $profile->postal_code);

        if ($profile->date_of_birth) {
            $this->assertEquals('2005-10-20', $profile->date_of_birth->toDateString());
        }

        $this->assertEquals('Perempuan', $profile->gender);
    }

    #[Test]
    public function owner_dapat_update_profil_dengan_sosial_media()
    {
        $this->actingAs($this->owner);

        $socialData = [
            'name' => 'Owner Risda',
            'email' => 'risdaowner@gmail.com',
            'owner_name' => 'Owner Risda',
            'business_name' => 'CV. Rentcar',
            'phone_number' => '089654109903',
            'facebook' => 'https://facebook.com/business',
            'instagram' => 'https://instagram.com/business',
            'tiktok' => 'https://tiktok.com/@business'
        ];

        $response = $this->post('/profile/owner', $socialData);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $profile = OwnerProfile::where('owner_id', $this->owner->id)->first();
        $this->assertNotNull($profile);

        if (isset($profile->facebook)) {
            $this->assertStringStartsWith('https://', $profile->facebook);
        }
        if (isset($profile->instagram)) {
            $this->assertStringStartsWith('https://', $profile->instagram);
        }
        if (isset($profile->tiktok)) {
            $this->assertStringStartsWith('https://', $profile->tiktok);
        }

        $this->assertEquals('Owner Risda', $profile->owner_name);
        $this->assertEquals('CV. Rentcar', $profile->business_name);
    }
}
