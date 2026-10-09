<?php

namespace Tests\Feature\Auth;

use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $this->seed(RoleSeeder::class);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'nik' => '3201011234560001',
            'tgl_lahir' => '1990-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jalan Test Nomor 1',
            'no_telp' => '081234567890',
            'golongan_darah' => 'O',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'pasien',
        ]);

        $this->assertDatabaseHas('pasiens', [
            'nik' => '3201011234560001',
            'nama' => 'Test User',
            'no_telp' => '081234567890',
        ]);
    }

    public function test_registration_requires_identity_fields(): void
    {
        $this->seed(RoleSeeder::class);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['nik', 'tgl_lahir', 'jenis_kelamin', 'alamat', 'no_telp']);
        $this->assertGuest();
    }
}
