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
            'name' => 'Adam Syahqwan',
            'email' => 'usersyahqwan@gmail.com',
            'password' => bcrypt('11111111'),
            'role' => 'user'
        ]);

        $this->assertNotNull($user->id, 'User harus berhasil dibuat dengan field dari $fillable');
        $this->assertEquals('Adam Syahqwan', $user->name);
        $this->assertEquals('usersyahqwan@gmail.com', $user->email);
        $this->assertEquals('user', $user->role);

        // ASSERT 2: Method isOwner() bekerja sesuai role
        $owner = User::create([
            'name' => 'Risda',
            'email' => 'ownerrisda@gmail.com',
            'password' => bcrypt('11111111'),
            'role' => 'owner'
        ]);

        $this->assertTrue($owner->isOwner(), 'User dengan role "owner" harus return true untuk isOwner()');
        $this->assertFalse($user->isOwner(), 'User dengan role "user" harus return false untuk isOwner()');

        // ASSERT 3: Method isUser() bekerja sesuai role
        $admin = User::create([
            'name' => 'Achmad Haikal Fikri',
            'email' => 'adminhaikal@gmail.com',
            'password' => bcrypt('11111111'),
            'role' => 'admin'
        ]);

        $this->assertTrue($user->isUser(), 'User dengan role "user" harus return true untuk isUser()');
        $this->assertFalse($owner->isUser(), 'User dengan role "owner" harus return false untuk isUser()');
        $this->assertFalse($admin->isUser(), 'User dengan role "admin" harus return false untuk isUser()');

        // ASSERT 4: Role validation/handling
        // Test role default jika tidak diisi
        $noRoleUser = User::create([
            'name' => 'Adam',
            'email' => 'adam@gmail.com',
            'password' => bcrypt('11111111')
            // role tidak diisi
        ]);

        $this->assertNull($noRoleUser->role, 'Role harus null jika tidak diisi');
        $this->assertFalse($noRoleUser->isOwner());
        $this->assertFalse($noRoleUser->isUser());

        // ASSERT 5: Email harus unique
        try {
            User::create([
                'name' => 'Adam Syahqwan',
                'email' => 'usersyahqwan@gmail.com',
                'password' => bcrypt('111111111'),
                'role' => 'user'
            ]);
            $this->fail('Seharusnya throw exception untuk duplicate email');
        } catch (\Illuminate\Database\QueryException $e) {
            $this->assertStringContainsString('duplicate', strtolower($e->getMessage()));
        }
    }
}
