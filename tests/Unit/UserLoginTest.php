<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class UserLoginTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    private const USER_EMAIL = 'userhaikal@user.com';
    private const OWNER_EMAIL = 'ownerhaikal@user.com';
    private const PASSWORD = '1111111';

    protected function setUp(): void
    {
        parent::setUp();

        User::create([
            'name' => 'Achmad Haikal Fikri',
            'email' => self::USER_EMAIL,
            'password' => Hash::make(self::PASSWORD),
            'role' => 'user',
        ]);

        User::create([
            'name' => 'Owner Haikal',
            'email' => self::OWNER_EMAIL,
            'password' => Hash::make(self::PASSWORD),
            'role' => 'owner',
        ]);
    }

    ## 1. Skenario Login Berhasil (Positive Test)

    #[Test]
    public function LoginDenganKredensialValid()
    {
        $response = $this->post('/login-user', [
            'email' => self::USER_EMAIL,
            'password' => self::PASSWORD,
        ]);

        $response->assertRedirect('/user/dashboard');
        $this->assertAuthenticatedAs(User::where('email', self::USER_EMAIL)->first());
    }

    ## 2. Skenario Kredensial Salah (Negative Test)

    #[Test]
    public function PasswordTidakValid()
    {
        $response = $this->from('/login-user')->post('/login-user', [
            'email' => self::USER_EMAIL,
            'password' => '111', // Password salah
        ]);

        $response->assertRedirect('/login-user');

        $response->assertSessionHasErrors();

        $this->assertGuest();
    }

    ## 3. Skenario Role Salah (Security Test)

    #[Test]
    public function LoginUserInvalid()
    {
        $response = $this->from('/login-user')->post('/login-user', [
            'email' => self::OWNER_EMAIL,
            'password' => self::PASSWORD,
        ]);

        $response->assertRedirect('/login-user');

        $response->assertSessionHasErrors();

        $this->assertGuest();
    }

    ## 4. Skenario Validasi Kosong (Validation Test)

    #[Test]
    public function EmailNotAvailable()
    {
        $response = $this->from('/login-user')->post('/login-user', [
            'email' => '', // Kosong
            'password' => self::PASSWORD,
        ]);

        $response->assertRedirect('/login-user');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    #[Test]
    public function EmailInValid()
    {
        $response = $this->from('/login-user')->post('/login-user', [
            'email' => 'invalid-email', // Format email salah
            'password' => self::PASSWORD,
        ]);

        $response->assertRedirect('/login-user');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
