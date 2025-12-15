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
    private const VALID_EMAIL = 'userhaikal@gmail.com';
    private const VALID_PASSWORD = '11111111';
    private const NEW_EMAIL = 'userhaikals@gmail.com';

    #[Test]
    public function RegistrationValidUser()
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
    public function RegistratationInvalidEmailAvailable()
    {
        // ARRANGE: Buat user terlebih dahulu dengan create()
        User::create([
            'name' => 'User Lama',
            'email' => self::VALID_EMAIL,
            'password' => Hash::make(self::VALID_PASSWORD),
            'role' => 'user'
        ]);

        // ACT: Coba registrasi dengan email yang sama
        $response = $this->post('/register-user', [
            'name' => 'Nama Baru',
            'email' => self::VALID_EMAIL,
            'password' => self::VALID_PASSWORD,
            'password_confirmation' => self::VALID_PASSWORD,
        ]);

        // ASSERT 1: Cek validation error
        $response->assertSessionHasErrors('email');

        // ASSERT 2: Cek jumlah user tetap 1 menggunakan assertCount
        $this->assertCount(1, User::all());

        // ASSERT 3: Cek nama TIDAK berubah (masih 'User Lama') menggunakan assertSame
        $existingUser = User::first();
        $this->assertSame('User Lama', $existingUser->name);

        // ASSERT 4: Verifikasi redirect menggunakan assertStatus
        $response->assertStatus(302);

        // ASSERT 5: Tidak ada success message menggunakan assertSessionMissing
        $response->assertSessionMissing('success');
    }

    #[Test]
    public function ValidatePasswordCharacter()
    {
        // ACT: Coba registrasi dengan password terlalu pendek
        $response = $this->post('/register-user', [
            'name' => self::VALID_NAME,
            'email' => self::NEW_EMAIL,
            'password' => '123', // Hanya 3 karakter
            'password_confirmation' => '123',
        ]);

        // ASSERT 1: Cek redirect menggunakan assertRedirect
        $response->assertRedirect();

        // ASSERT 2: Cek tidak ada data yang tersimpan menggunakan assertDatabaseCount
        $this->assertDatabaseCount('users', 0);

        // ASSERT 3: Cek email tidak ada di database menggunakan assertDatabaseMissing
        $this->assertDatabaseMissing('users', [
            'email' => self::NEW_EMAIL
        ]);

        // ASSERT 4: Cek tidak ada success message menggunakan assertSessionMissing
        $response->assertSessionMissing('success');

        // ASSERT 5: Cek URL bukan redirect ke login (harusnya back dengan error)
        $this->assertNotEquals(
            url('/login-user'),
            $response->getTargetUrl()
        );

        // ASSERT 6: Cek password pendek tidak bisa di-hash dengan benar
        $shortPassword = '123';
        $hashed = Hash::make($shortPassword);
        $this->assertGreaterThan(20, strlen($hashed), 'Hash harus lebih dari 20 karakter');
    }

    #[Test]
    public function RegistratationOwnerDiffrentUser()
    {
        // ACT 1: Registrasi user
        $userResponse = $this->post('/register-user', [
            'name' => 'Risda',
            'email' => 'risda@gmail.com',
            'password' => self::VALID_PASSWORD,
            'password_confirmation' => self::VALID_PASSWORD,
        ]);

        // ACT 2: Registrasi owner
        $ownerResponse = $this->post('/register-owner', [
            'name' => 'Adam',
            'email' => 'adam@gmail.com',
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

        // ASSERT 3: Cek owner redirect ke login-owner menggunakan assertLocation
        $ownerResponse->assertLocation('/login-owner');

        // ASSERT 4: Verifikasi role berbeda menggunakan assertNotSame
        $user = User::where('email', 'risda@gmail.com')->first();
        $owner = User::where('email', 'adam@gmail.com')->first();


        $this->assertNotSame($user->role, $owner->role);

        // ASSERT 5: Cek total user menggunakan assertGreaterThan
        $totalUsers = User::count();
        $this->assertGreaterThan(1, $totalUsers);
    }

    #[Test]
    public function RegistratationLoginSuitable()
    {
        // PHASE 1: Registrasi
        $registerResponse = $this->post('/register-user', [
            'name' => 'Ownerfikri@gmail.com',
            'email' => 'ownerfikri@gmail.com',
            'password' => '11111111',
            'password_confirmation' => '11111111',
        ]);

        // ASSERT 1: Registrasi berhasil menggunakan assertRedirect
        $registerResponse->assertRedirect('/login-user');

        // ASSERT 2: User ada di database menggunakan assertNotNull
        $user = User::where('email', 'ownerfikri@gmail.com')->first();
        $this->assertNotNull($user);

        // PHASE 2: Login dengan password salah
        $failedLogin = $this->post('/login-user', [
            'email' => 'ownerfikri@gmail.com',
            'password' => '111',
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
            'email' => 'ownerfikri@gmail.com',
            'password' => '11111111',
        ]);

        // ASSERT 5: Login berhasil menggunakan assertRedirect
        $successLogin->assertRedirect('/user/dashboard');

        // ASSERT 6: Redirect ke dashboard menggunakan assertStringContainsString
        $this->assertStringContainsString(
            '/user/dashboard',
            $successLogin->getTargetUrl()
        );

        // ASSERT 7: Verifikasi password menggunakan assertFalse untuk wrong password
        $this->assertFalse(Hash::check('wrongpassword', $user->password));

        // ASSERT 8: Verifikasi password menggunakan assertTrue untuk correct password
        $this->assertTrue(Hash::check('11111111', $user->password));
    }
}
