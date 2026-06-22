<?php

namespace Tests\Feature;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_can_access_registration_page(): void
    {
        $response = $this->get('/register');
        $response->assertOk();
        $response->assertSee('Daftar Akun Baru');
    }

    public function test_logged_in_user_cannot_access_registration_page(): void
    {
        $user = User::query()->firstOrFail();
        $response = $this->actingAs($user)->get('/register');
        $response->assertRedirect('/');
    }

    public function test_user_can_register_as_technician_and_is_redirected_to_technician_dashboard(): void
    {
        $response = $this->post('/register', [
            'name' => 'Teknisi Baru',
            'email' => 'newtech@rs.test',
            'role' => 'technician',
            'unit_id' => null,
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect('/dashboard/teknisi');
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'name' => 'Teknisi Baru',
            'email' => 'newtech@rs.test',
            'role' => 'technician',
            'unit_id' => null,
        ]);

        $user = User::query()->where('email', 'newtech@rs.test')->firstOrFail();
        $this->assertTrue(Hash::check('secret123', $user->password));
    }

    public function test_user_can_register_as_nurse_with_unit_and_is_redirected_to_nurse_dashboard(): void
    {
        $unit = Unit::query()->firstOrFail();

        $response = $this->post('/register', [
            'name' => 'Perawat Baru',
            'email' => 'newnurse@rs.test',
            'role' => 'nurse',
            'unit_id' => $unit->id,
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect('/dashboard/perawat');
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'name' => 'Perawat Baru',
            'email' => 'newnurse@rs.test',
            'role' => 'nurse',
            'unit_id' => $unit->id,
        ]);
    }

    public function test_nurse_registration_fails_without_unit(): void
    {
        $response = $this->post('/register', [
            'name' => 'Perawat Tanpa Unit',
            'email' => 'nonunit@rs.test',
            'role' => 'nurse',
            'unit_id' => null,
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertSessionHasErrors(['unit_id']);
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'nonunit@rs.test']);
    }

    public function test_registration_fails_with_validation_errors(): void
    {
        // 1. Password confirmation mismatch
        $response = $this->post('/register', [
            'name' => 'Perawat Gagal',
            'email' => 'failed@rs.test',
            'role' => 'nurse',
            'unit_id' => Unit::query()->value('id'),
            'password' => 'secret123',
            'password_confirmation' => 'mismatch',
        ]);
        $response->assertSessionHasErrors(['password']);

        // 2. Email already registered
        $existingUser = User::query()->firstOrFail();
        $response = $this->post('/register', [
            'name' => 'User Duplikat',
            'email' => $existingUser->email,
            'role' => 'technician',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);
        $response->assertSessionHasErrors(['email']);
    }
}
