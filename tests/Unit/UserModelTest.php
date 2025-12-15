<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_user_model_with_five_asserts_based_on_fillable()
    {
        // ASSERT 1: User dapat dibuat dengan field dari $fillable
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
            'role' => 'user'
        ]);

        $this->assertNotNull($user->id, 'User harus berhasil dibuat dengan field dari $fillable');
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals('user', $user->role);

        // ASSERT 2: Method isOwner() bekerja sesuai role
        $owner = User::create([
            'name' => 'Vehicle Owner',
            'email' => 'owner@example.com',
            'password' => bcrypt('password123'),
            'role' => 'owner'
        ]);

        $this->assertTrue($owner->isOwner(), 'User dengan role "owner" harus return true untuk isOwner()');
        $this->assertFalse($user->isOwner(), 'User dengan role "user" harus return false untuk isOwner()');

        // ASSERT 3: Method isUser() bekerja sesuai role
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin'
        ]);

        $this->assertTrue($user->isUser(), 'User dengan role "user" harus return true untuk isUser()');
        $this->assertFalse($owner->isUser(), 'User dengan role "owner" harus return false untuk isUser()');
        $this->assertFalse($admin->isUser(), 'User dengan role "admin" harus return false untuk isUser()');

        // ASSERT 4: Role validation/handling
        // Test role default jika tidak diisi
        $noRoleUser = User::create([
            'name' => 'No Role User',
            'email' => 'norole@example.com',
            'password' => bcrypt('password123')
            // role tidak diisi
        ]);

        $this->assertNull($noRoleUser->role, 'Role harus null jika tidak diisi');
        $this->assertFalse($noRoleUser->isOwner());
        $this->assertFalse($noRoleUser->isUser());

        // ASSERT 5: Email harus unique
        try {
            User::create([
                'name' => 'Duplicate Email',
                'email' => 'john@example.com', // Email sama dengan user pertama
                'password' => bcrypt('password123'),
                'role' => 'user'
            ]);
            $this->fail('Seharusnya throw exception untuk duplicate email');
        } catch (\Illuminate\Database\QueryException $e) {
            $this->assertStringContainsString('duplicate', strtolower($e->getMessage()));
        }
    }
}
