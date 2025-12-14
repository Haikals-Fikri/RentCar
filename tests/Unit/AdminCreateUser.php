<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AdminCreateUser extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup admin user
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);
    }

    #[Test]
    public function admin_dapat_melihat_daftar_user()
    {
        // Setup: Buat beberapa user
        User::factory()->count(3)->create(['role' => 'user']);

        // Login sebagai admin
        $this->actingAs($this->admin);

        // ACT: Akses halaman daftar user
        $response = $this->get('/admin/users');

        // ASSERT 1: Cek response status 200 menggunakan assertOk
        $response->assertOk();

        // ASSERT 2: Cek view yang digunakan menggunakan assertViewIs
        $response->assertViewIs('admin-users');

        // ASSERT 3: Cek data users di view menggunakan assertViewHas
        $response->assertViewHas('users');

        // ASSERT 4: Cek jumlah user yang ditampilkan menggunakan assertCount
        $viewUsers = $response->viewData('users');
        $this->assertCount(3, $viewUsers);

        // ASSERT 5: Cek hanya user dengan role 'user' yang ditampilkan menggunakan assertTrue
        foreach ($viewUsers as $user) {
            $this->assertTrue($user->role === 'user');
        }
    }

    #[Test]
    public function admin_dapat_menambahkan_user_baru()
    {
        // Login sebagai admin
        $this->actingAs($this->admin);

        // Data user baru
        $userData = [
            'name' => 'User Baru',
            'email' => 'newuser@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        // ACT: Tambah user baru
        $response = $this->post('/admin/users/store', $userData);

        // ASSERT 1: Cek redirect menggunakan assertRedirect
        $response->assertRedirect(route('admin.users'));

        // ASSERT 2: Cek success message menggunakan assertSessionHas
        $response->assertSessionHas('success');

        // ASSERT 3: Cek user dibuat di database menggunakan assertDatabaseCount
        $this->assertDatabaseCount('users', 2); // admin + user baru

        // ASSERT 4: Cek data user tersimpan dengan benar menggunakan assertDatabaseHas
        $this->assertDatabaseHas('users', [
            'name' => 'User Baru',
            'email' => 'newuser@test.com',
            'role' => 'user'
        ]);

        // ASSERT 5: Cek password di-hash menggunakan assertNotEquals
        $user = User::where('email', 'newuser@test.com')->first();
        $this->assertNotEquals('password123', $user->password);
    }

    #[Test]
    public function admin_dapat_menghapus_user()
    {
        // Setup: Buat user untuk dihapus
        $userToDelete = User::create([
            'name' => 'User Dihapus',
            'email' => 'delete@test.com',
            'password' => bcrypt('password'),
            'role' => 'user'
        ]);

        // Login sebagai admin
        $this->actingAs($this->admin);

        // ACT: Hapus user
        $response = $this->delete("/admin/users/{$userToDelete->id}");

        // ASSERT 1: Cek redirect menggunakan assertRedirect
        $response->assertRedirect(route('admin.users'));

        // ASSERT 2: Cek success message menggunakan assertSessionHas
        $response->assertSessionHas('success');

        // ASSERT 3: Cek user dihapus dari database menggunakan assertDatabaseMissing
        $this->assertDatabaseMissing('users', [
            'id' => $userToDelete->id,
            'email' => 'delete@test.com'
        ]);

        // ASSERT 4: Cek jumlah user berkurang menggunakan assertDatabaseCount
        $this->assertDatabaseCount('users', 1); // hanya admin tersisa

        // ASSERT 5: Cek admin tidak terhapus menggunakan assertNotNull
        $admin = User::find($this->admin->id);
        $this->assertNotNull($admin);
    }

    #[Test]
    public function admin_dapat_melihat_daftar_owner()
    {
        // Setup: Buat beberapa owner
        User::factory()->count(2)->create(['role' => 'owner']);

        // Login sebagai admin
        $this->actingAs($this->admin);

        // ACT: Akses halaman daftar owner
        $response = $this->get('/admin/owners');

        // ASSERT 1: Cek response status 200 menggunakan assertStatus
        $response->assertStatus(200);

        // ASSERT 2: Cek view yang digunakan menggunakan assertViewIs
        $response->assertViewIs('admin-owners');

        // ASSERT 3: Cek data owners di view menggunakan assertViewHas
        $response->assertViewHas('owners');

        // ASSERT 4: Cek jumlah owner yang ditampilkan menggunakan assertCount
        $viewOwners = $response->viewData('owners');
        $this->assertCount(2, $viewOwners);

        // ASSERT 5: Cek hanya user dengan role 'owner' yang ditampilkan menggunakan assertEquals
        foreach ($viewOwners as $owner) {
            $this->assertEquals('owner', $owner->role);
        }
    }

    #[Test]
    public function admin_dapat_melihat_statistik_pengguna()
    {
        // Setup: Buat beberapa user dan owner
        User::factory()->count(4)->create(['role' => 'user']);
        User::factory()->count(3)->create(['role' => 'owner']);

        // Login sebagai admin
        $this->actingAs($this->admin);

        // ACT: Akses halaman histogram
        $response = $this->get('/admin/histogram');

        // ASSERT 1: Cek response status 200 menggunakan assertOk
        $response->assertOk();

        // ASSERT 2: Cek view yang digunakan menggunakan assertViewIs
        $response->assertViewIs('admin-histogram');

        // ASSERT 3: Cek data UserCount di view menggunakan assertViewHas
        $response->assertViewHas('UserCount');

        // ASSERT 4: Cek data OwnerCount di view menggunakan assertViewHas
        $response->assertViewHas('OwnerCount');

        // ASSERT 5: Cek perhitungan jumlah benar menggunakan assertEquals
        $this->assertEquals(4, $response->viewData('UserCount')); // 4 user
        $this->assertEquals(3, $response->viewData('OwnerCount')); // 3 owner
    }
}
