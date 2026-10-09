<?php

namespace Tests\Feature;

use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PasienManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    private function makeAdmin(): User
    {
        $user = User::factory()->create(['role' => 'admin']);
        $user->assignRole('admin');

        return $user;
    }

    private function makePasienUser(): User
    {
        $user = User::factory()->create(['role' => 'pasien']);
        $user->assignRole('pasien');
        Pasien::factory()->create(['user_id' => $user->id]);

        return $user;
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/pasien')->assertRedirect('/login');
    }

    public function test_admin_can_list_pasiens(): void
    {
        $pasien = Pasien::factory()->create();

        $this->actingAs($this->makeAdmin())
            ->get('/pasien')
            ->assertOk()
            ->assertSee('Manajemen Data Pasien')
            ->assertSee($pasien->nama);
    }

    public function test_pasien_role_is_redirected_from_master_list_to_own_profile(): void
    {
        $user = $this->makePasienUser();

        $this->actingAs($user)
            ->get('/pasien')
            ->assertRedirect(route('pasien.profile'));
    }

    public function test_pasien_profile_page_shows_own_data_and_visit_history(): void
    {
        $user = $this->makePasienUser();
        $pasien = $user->pasien;
        $pendaftaran = Pendaftaran::factory()->create(['pasien_id' => $pasien->id]);

        $this->actingAs($user)
            ->get('/pasien/profil')
            ->assertOk()
            ->assertSee($pasien->nama)
            ->assertSee($pendaftaran->kode_daftar);
    }

    public function test_user_without_pasien_profile_is_redirected_to_complete_profile(): void
    {
        $user = User::factory()->create(['role' => 'pasien']);
        $user->assignRole('pasien');

        $this->actingAs($user)
            ->get('/pasien/profil')
            ->assertRedirect(route('pasien.complete-profile'));
    }

    public function test_complete_profile_form_can_be_rendered(): void
    {
        $user = User::factory()->create(['role' => 'pasien']);
        $user->assignRole('pasien');

        $this->actingAs($user)
            ->get('/pasien/lengkapi-profil')
            ->assertOk()
            ->assertSee('Lengkapi Profil Pasien');
    }

    public function test_pasien_can_complete_profile(): void
    {
        $user = User::factory()->create(['role' => 'pasien']);
        $user->assignRole('pasien');

        $response = $this->actingAs($user)->post('/pasien/lengkapi-profil', [
            'nik' => '3201011234560001',
            'nama' => 'Budi Santoso',
            'tgl_lahir' => '1990-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jalan Kenanga No. 5',
            'no_telp' => '081234567890',
            'golongan_darah' => 'O',
        ]);

        $response->assertRedirect(route('pasien.profile'));

        $this->assertDatabaseHas('pasiens', [
            'user_id' => $user->id,
            'nik' => '3201011234560001',
            'nama' => 'Budi Santoso',
        ]);
    }

    public function test_admin_can_create_pasien(): void
    {
        $response = $this->actingAs($this->makeAdmin())->post('/pasien', [
            'nik' => '3201011234560002',
            'nama' => 'Siti Aminah',
            'tgl_lahir' => '1995-05-05',
            'jenis_kelamin' => 'P',
            'alamat' => 'Jalan Melati No. 10',
            'no_telp' => '089876543210',
            'golongan_darah' => 'A',
        ]);

        $response->assertRedirect(route('pasien.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('pasiens', ['nik' => '3201011234560002', 'nama' => 'Siti Aminah']);
    }

    public function test_pasien_role_cannot_create_new_pasien(): void
    {
        $this->actingAs($this->makePasienUser())->post('/pasien', [
            'nik' => '3201011234560003',
            'nama' => 'Tidak Boleh',
            'tgl_lahir' => '1995-05-05',
            'jenis_kelamin' => 'L',
            'alamat' => 'Alamat',
            'no_telp' => '081111111111',
        ])->assertForbidden();

        $this->assertDatabaseCount('pasiens', 1);
    }

    public function test_pasien_detail_and_edit_pages_can_be_rendered(): void
    {
        $pasien = Pasien::factory()->create();
        $admin = $this->makeAdmin();

        $this->actingAs($admin)
            ->get('/pasien/'.$pasien->id)
            ->assertOk()
            ->assertSee($pasien->nama);

        $this->actingAs($admin)
            ->get('/pasien/'.$pasien->id.'/edit')
            ->assertOk()
            ->assertSee('Ubah Data Pasien');
    }

    public function test_owner_can_edit_and_update_own_profile(): void
    {
        $user = $this->makePasienUser();
        $pasien = $user->pasien;

        $this->actingAs($user)
            ->get('/pasien/'.$pasien->id.'/edit')
            ->assertOk();

        $this->actingAs($user)->put('/pasien/'.$pasien->id, [
            'nik' => $pasien->nik,
            'nama' => 'Nama Baru',
            'tgl_lahir' => '1992-02-02',
            'jenis_kelamin' => $pasien->jenis_kelamin,
            'alamat' => 'Alamat Baru No. 7',
            'no_telp' => '082222222222',
            'golongan_darah' => 'B',
        ])->assertRedirect(route('pasien.profile'));

        $this->assertDatabaseHas('pasiens', ['id' => $pasien->id, 'nama' => 'Nama Baru']);
    }

    public function test_non_owner_pasien_cannot_edit_or_update_others_profile(): void
    {
        $owner = $this->makePasienUser();
        $intruder = $this->makePasienUser();
        $pasien = $owner->pasien;

        $this->actingAs($intruder)
            ->get('/pasien/'.$pasien->id.'/edit')
            ->assertForbidden();

        $this->actingAs($intruder)->put('/pasien/'.$pasien->id, [
            'nik' => $pasien->nik,
            'nama' => 'Hacker',
            'tgl_lahir' => '1992-02-02',
            'jenis_kelamin' => 'L',
            'alamat' => 'Alamat',
            'no_telp' => '082222222222',
        ])->assertForbidden();
    }

    public function test_api_my_profile_returns_own_profile(): void
    {
        $user = $this->makePasienUser();
        Sanctum::actingAs($user);

        $this->getJson('/api/pasien/me')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.nik', $user->pasien->nik);

        $this->putJson('/api/pasien/me', [
            'nik' => $user->pasien->nik,
            'nama' => 'Diubah Via API',
            'tgl_lahir' => '1991-03-03',
            'jenis_kelamin' => $user->pasien->jenis_kelamin,
            'alamat' => 'Alamat API',
            'no_telp' => '083333333333',
        ])->assertOk()->assertJsonPath('data.nama', 'Diubah Via API');

        $this->assertDatabaseHas('pasiens', ['id' => $user->pasien->id, 'nama' => 'Diubah Via API']);
    }

    public function test_api_my_profile_returns_not_found_without_profile(): void
    {
        $user = User::factory()->create(['role' => 'pasien']);
        $user->assignRole('pasien');
        Sanctum::actingAs($user);

        $this->getJson('/api/pasien/me')->assertNotFound();
    }

    public function test_api_pasien_resource_requires_admin_role(): void
    {
        $this->actingAs($this->makePasienUser())
            ->getJson('/api/pasien')
            ->assertForbidden();

        $this->actingAs($this->makeAdmin())
            ->getJson('/api/pasien')
            ->assertOk();
    }
}
