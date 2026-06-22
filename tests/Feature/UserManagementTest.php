<?php

namespace Tests\Feature;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_and_super_admin_can_access_user_management(): void
    {
        $admin = User::query()->where('role', 'admin')->firstOrFail();
        $superAdmin = User::query()->where('role', 'super_admin')->firstOrFail();

        $this->actingAs($admin)->get('/users')->assertOk()->assertSee('Manajemen Pengguna');
        $this->actingAs($superAdmin)->get('/users')->assertOk()->assertSee('Manajemen Pengguna');
    }

    public function test_nurse_and_technician_cannot_access_user_management(): void
    {
        $nurse = User::query()->where('role', 'nurse')->firstOrFail();
        $technician = User::query()->where('role', 'technician')->firstOrFail();

        $this->actingAs($nurse)->get('/users')->assertStatus(403);
        $this->actingAs($technician)->get('/users')->assertStatus(403);
    }

    public function test_admin_can_create_a_new_user_nurse_with_unit(): void
    {
        $admin = User::query()->where('role', 'admin')->firstOrFail();
        $unit = Unit::query()->firstOrFail();

        $response = $this->actingAs($admin)->post('/users', [
            'name' => 'Ns. John Doe',
            'email' => 'johndoe@rs.test',
            'password' => 'secret123',
            'role' => 'nurse',
            'unit_id' => $unit->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'name' => 'Ns. John Doe',
            'email' => 'johndoe@rs.test',
            'role' => 'nurse',
            'unit_id' => $unit->id,
        ]);

        $createdUser = User::query()->where('email', 'johndoe@rs.test')->firstOrFail();
        $this->assertTrue(Hash::check('secret123', $createdUser->password));
    }

    public function test_admin_cannot_create_nurse_without_unit(): void
    {
        $admin = User::query()->where('role', 'admin')->firstOrFail();

        $response = $this->actingAs($admin)->post('/users', [
            'name' => 'Ns. John Doe',
            'email' => 'johndoe@rs.test',
            'password' => 'secret123',
            'role' => 'nurse',
            'unit_id' => null,
        ]);

        $response->assertSessionHasErrors(['unit_id']);
        $this->assertDatabaseMissing('users', ['email' => 'johndoe@rs.test']);
    }

    public function test_admin_can_create_technician_without_unit(): void
    {
        $admin = User::query()->where('role', 'admin')->firstOrFail();

        $response = $this->actingAs($admin)->post('/users', [
            'name' => 'Tech Budi',
            'email' => 'budi@rs.test',
            'password' => 'secret123',
            'role' => 'technician',
            'unit_id' => null,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'name' => 'Tech Budi',
            'email' => 'budi@rs.test',
            'role' => 'technician',
            'unit_id' => null,
        ]);
    }

    public function test_admin_can_edit_user_data_without_updating_password(): void
    {
        $admin = User::query()->where('role', 'admin')->firstOrFail();
        $nurse = User::query()->where('role', 'nurse')->firstOrFail();
        $oldPasswordHash = $nurse->password;

        $response = $this->actingAs($admin)->patch("/users/{$nurse->id}", [
            'name' => 'Ns. Rina Updated',
            'email' => $nurse->email,
            'role' => 'nurse',
            'password' => '', // leave blank
            'unit_id' => $nurse->unit_id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $nurse->id,
            'name' => 'Ns. Rina Updated',
        ]);

        $nurse->refresh();
        $this->assertSame($oldPasswordHash, $nurse->password);
    }

    public function test_admin_can_edit_user_data_and_update_password(): void
    {
        $admin = User::query()->where('role', 'admin')->firstOrFail();
        $nurse = User::query()->where('role', 'nurse')->firstOrFail();

        $response = $this->actingAs($admin)->patch("/users/{$nurse->id}", [
            'name' => 'Ns. Rina Updated Pass',
            'email' => $nurse->email,
            'role' => 'nurse',
            'password' => 'newsecret123',
            'unit_id' => $nurse->unit_id,
        ]);

        $response->assertRedirect();
        $nurse->refresh();
        $this->assertTrue(Hash::check('newsecret123', $nurse->password));
    }

    public function test_user_cannot_delete_themselves(): void
    {
        $admin = User::query()->where('role', 'admin')->firstOrFail();

        $response = $this->actingAs($admin)->delete("/users/{$admin->id}");

        $response->assertSessionHasErrors(['email']);
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_can_delete_other_user(): void
    {
        $admin = User::query()->where('role', 'admin')->firstOrFail();
        $nurse = User::query()->where('role', 'nurse')->firstOrFail();

        $response = $this->actingAs($admin)->delete("/users/{$nurse->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $nurse->id]);
    }
}
