<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class RegistrasiTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    private const VALID_NAME = 'Achmad Haikal Fikri';
    private const VALID_EMAIL = 'haikal@example.com';
    private const VALID_PASSWORD = 'Password123';
    private const NEW_EMAIL = 'newuser@example.com';

    #[Test]
    public function registrasi_user_berhasil_dengan_data_valid()
    {
        // ACT: Lakukan registrasi
        $response = $this->post('/register-user', [
            'name' => self::VALID_NAME,
            'email' => self::VALID_EMAIL,
            'password' => self::VALID_PASSWORD,
            'password_confirmation' => self::VALID_PASSWORD,
        ]);

        // ASSERT 1: Cek redirect menggunakan assertRedirect
        $response->assertRedirect('/login-user');

        // ASSERT 2: Cek flash message dengan assertSessionHas
        $response->assertSessionHas('success');

        // ASSERT 3: Verifikasi teks success message menggunakan assertStringContainsString
        $this->assertStringContainsString(
            'Registrasi berhasil',
            session('success')
        );

        // ASSERT 4: Cek data di database dengan assertDatabaseHas
        $this->assertDatabaseHas('users', [
            'name' => self::VALID_NAME,
            'email' => self::VALID_EMAIL,
        ]);

        // ASSERT 5: Verifikasi role menggunakan assertEquals
        $user = User::first();
        $this->assertEquals('user', $user->role);

        // ASSERT 6: Cek password di-hash menggunakan assertTrue dengan Hash::check
        $this->assertTrue(Hash::check(self::VALID_PASSWORD, $user->password));
    }

    #[Test]
    public function registrasi_gagal_karena_email_sudah_terdaftar()
    {
        // ARRANGE: Buat user terlebih dahulu
        User::factory()->create([
            'email' => self::VALID_EMAIL,
            'role' => 'user'
        ]);

        // ACT: Coba registrasi dengan email yang sama
        $response = $this->post('/register-user', [
            'name' => 'Nama Baru',
            'email' => self::VALID_EMAIL,
            'password' => self::VALID_PASSWORD,
            'password_confirmation' => self::VALID_PASSWORD,
        ]);

        // ASSERT 1: Cek validation error menggunakan assertSessionHasErrors
        $response->assertSessionHasErrors('email');

        // ASSERT 2: Cek pesan error mengandung kata 'taken' menggunakan assertStringContainsString
        $sessionErrors = session('errors')->getBag('default')->get('email');
        $this->assertStringContainsString('taken', $sessionErrors[0]);

        // ASSERT 3: Cek jumlah user tetap 1 menggunakan assertCount
        $users = User::where('email', self::VALID_EMAIL)->get();
        $this->assertCount(1, $users);

        // ASSERT 4: Cek nama tidak berubah menggunakan assertSame
        $existingUser = User::first();
        $this->assertSame('Nama Baru', $existingUser->name);

        // ASSERT 5: Verifikasi redirect menggunakan assertStatus untuk cek 302
        $response->assertStatus(302);
    }

    #[Test]
    public function validasi_password_confirmation_wajib_sesuai()
    {
        // ACT: Coba registrasi dengan password confirmation berbeda
        $response = $this->post('/register-user', [
            'name' => self::VALID_NAME,
            'email' => self::NEW_EMAIL,
            'password' => self::VALID_PASSWORD,
            'password_confirmation' => 'DifferentPassword',
        ]);

        // ASSERT 1: Cek error password menggunakan assertInvalid
        $response->assertInvalid('password');

        // ASSERT 2: Cek database kosong menggunakan assertDatabaseCount
        $this->assertDatabaseCount('users', 0);

        // ASSERT 3: Cek email tidak ada di database menggunakan assertDatabaseMissing
        $this->assertDatabaseMissing('users', [
            'email' => self::NEW_EMAIL
        ]);

        // ASSERT 4: Cek session memiliki errors menggunakan assertSessionHasAllErrors
        $response->assertSessionHasAllErrors(['password']);

        // ASSERT 5: Verifikasi tidak ada success message menggunakan assertSessionMissing
        $response->assertSessionMissing('success');
    }

    #[Test]
    public function registrasi_owner_berbeda_dengan_user()
    {
        // ACT 1: Registrasi user
        $userResponse = $this->post('/register-user', [
            'name' => 'Regular User',
            'email' => 'user@test.com',
            'password' => self::VALID_PASSWORD,
            'password_confirmation' => self::VALID_PASSWORD,
        ]);

        // ACT 2: Registrasi owner
        $ownerResponse = $this->post('/register-owner', [
            'name' => 'Business Owner',
            'email' => 'owner@test.com',
            'password' => self::VALID_PASSWORD,
            'password_confirmation' => self::VALID_PASSWORD,
        ]);

        // ASSERT 1: Cek redirect berbeda menggunakan assertNotEquals
        $this->assertNotEquals(
            $userResponse->getTargetUrl(),
            $ownerResponse->getTargetUrl()
        );

        // ASSERT 2: Cek user redirect ke login-user menggunakan assertLocation
        $userResponse->assertLocation('/login-user');

        // ASSERT 3: Cek owner redirect ke login-owner menggunakan assertStringContainsString
        $this->assertStringContainsString(
            '/login-owner',
            $ownerResponse->getTargetUrl()
        );

        // ASSERT 4: Verifikasi role berbeda menggunakan assertNotSame
        $user = User::where('email', 'user@test.com')->first();
        $owner = User::where('email', 'owner@test.com')->first();

        $this->assertNotSame($user->role, $owner->role);

        // ASSERT 5: Cek total user menggunakan assertGreaterThan
        $totalUsers = User::count();
        $this->assertGreaterThan(1, $totalUsers);
    }

    #[Test]
    public function registrasi_dan_login_dapat_dilakukan_secara_berurutan()
    {
        // PHASE 1: Registrasi
        $registerResponse = $this->post('/register-user', [
            'name' => 'Integration Test User',
            'email' => 'integration@test.com',
            'password' => 'testpassword',
            'password_confirmation' => 'testpassword',
        ]);

        // ASSERT 1: Registrasi berhasil menggunakan assertRedirect
        $registerResponse->assertRedirect('/login-user');

        // ASSERT 2: User ada di database menggunakan assertNotNull
        $user = User::where('email', 'integration@test.com')->first();
        $this->assertNotNull($user);

        // PHASE 2: Login dengan password salah
        $failedLogin = $this->post('/login-user', [
            'email' => 'integration@test.com',
            'password' => 'wrongpassword',
        ]);

        // ASSERT 3: Login gagal menggunakan assertSessionHasErrors
        $failedLogin->assertSessionHasErrors();

        // ASSERT 4: Redirect tetap di login menggunakan assertStringEndsWith
        $this->assertStringEndsWith(
            '/login-user',
            $failedLogin->getTargetUrl()
        );

        // PHASE 3: Login dengan password benar
        $successLogin = $this->post('/login-user', [
            'email' => 'integration@test.com',
            'password' => 'testpassword',
        ]);

        // ASSERT 5: Login berhasil menggunakan assertSessionDoesntHaveErrors
        $successLogin->assertSessionDoesntHaveErrors();

        // ASSERT 6: Redirect ke dashboard menggunakan assertStringContainsString
        $this->assertStringContainsString(
            '/user/dashboard',
            $successLogin->getTargetUrl()
        );

        // ASSERT 7: Verifikasi password menggunakan assertFalse untuk wrong password
        $this->assertFalse(Hash::check('wrongpassword', $user->password));

        // ASSERT 8: Verifikasi password menggunakan assertTrue untuk correct password
        $this->assertTrue(Hash::check('testpassword', $user->password));
    }
}
