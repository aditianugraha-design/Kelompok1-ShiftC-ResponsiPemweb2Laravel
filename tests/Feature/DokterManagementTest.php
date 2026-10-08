<?php

namespace Tests\Feature;

use App\Models\Dokter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DokterManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dokter')->assertRedirect('/login');
    }

    public function test_dokter_index_page_can_be_rendered(): void
    {
        Dokter::factory()->count(3)->create();

        $response = $this->actingAs(User::factory()->create())->get('/dokter');

        $response->assertOk()
            ->assertSee('Manajemen Data Dokter')
            ->assertSee('Total Dokter')
            ->assertSee('Dokter Spesialis')
            ->assertSee('Dokter Umum')
            ->assertSee('Dokter Aktif / Hadir')
            ->assertSee('Tambah Dokter Baru')
            ->assertSee('Daftar Dokter Terdaftar');
    }

    public function test_dokter_index_shows_empty_state_when_there_is_no_data(): void
    {
        $response = $this->actingAs(User::factory()->create())->get('/dokter');

        $response->assertOk()->assertSee('Tidak ada data dokter');
    }

    public function test_dokter_create_page_opens_the_create_modal(): void
    {
        $response = $this->actingAs(User::factory()->create())->get('/dokter/create');

        $response->assertOk()->assertSee('Tambah Dokter Baru');
    }

    public function test_dokter_can_be_created(): void
    {
        $response = $this->actingAs(User::factory()->create())->post('/dokter', [
            'nama' => 'dr. Andi Pratama, Sp.A',
            'nip' => 'STR-10001',
            'spesialisasi' => 'Anak',
            'no_telepon' => '081234567890',
            'jadwal_praktik' => 'Senin - Jumat, 08:00 - 14:00',
            'status' => 'aktif',
        ]);

        $response->assertRedirect(route('dokter.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('dokters', [
            'nama' => 'dr. Andi Pratama, Sp.A',
            'nip' => 'STR-10001',
            'spesialisasi' => 'Anak',
            'no_telepon' => '081234567890',
            'jadwal_praktik' => 'Senin - Jumat, 08:00 - 14:00',
            'status' => 'aktif',
        ]);
    }

    public function test_dokter_creation_requires_valid_data(): void
    {
        $response = $this->actingAs(User::factory()->create())->from('/dokter/create')->post('/dokter', [
            'nama' => '',
            'spesialisasi' => '',
            'no_telepon' => '',
            'status' => 'unknown',
        ]);

        $response->assertRedirect('/dokter/create')
            ->assertSessionHasErrors(['nama', 'spesialisasi', 'no_telepon', 'status']);

        $this->assertDatabaseCount('dokters', 0);
    }

    public function test_dokter_can_be_updated(): void
    {
        $dokter = Dokter::factory()->create();

        $response = $this->actingAs(User::factory()->create())->put('/dokter/'.$dokter->id, [
            'nama' => 'dr. Andi Pratama, Sp.THT',
            'nip' => $dokter->nip,
            'spesialisasi' => 'THT',
            'no_telepon' => '089876543210',
            'jadwal_praktik' => 'Selasa & Kamis, 13:00 - 17:00',
            'status' => 'non-aktif',
        ]);

        $response->assertRedirect(route('dokter.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('dokters', [
            'id' => $dokter->id,
            'nama' => 'dr. Andi Pratama, Sp.THT',
            'spesialisasi' => 'THT',
            'status' => 'non-aktif',
        ]);
    }

    public function test_dokter_can_be_deleted(): void
    {
        $dokter = Dokter::factory()->create();

        $response = $this->actingAs(User::factory()->create())->delete('/dokter/'.$dokter->id);

        $response->assertRedirect(route('dokter.index'))
            ->assertSessionHas('success');

        $this->assertSoftDeleted('dokters', ['id' => $dokter->id]);
    }

    public function test_dokter_can_be_searched_by_name_nip_or_specialization(): void
    {
        Dokter::factory()->create(['nama' => 'dr. Sulistiawan', 'nip' => 'STR-777', 'spesialisasi' => 'Mata']);
        Dokter::factory()->create(['nama' => 'dr. Handayani', 'nip' => 'STR-888', 'spesialisasi' => 'Gigi']);

        $this->actingAs(User::factory()->create())
            ->get('/dokter?search=STR-777')
            ->assertOk()
            ->assertSee('dr. Sulistiawan')
            ->assertDontSee('dr. Handayani');

        $this->actingAs(User::factory()->create())
            ->get('/dokter?spesialisasi=Gigi')
            ->assertOk()
            ->assertSee('dr. Handayani')
            ->assertDontSee('dr. Sulistiawan');
    }
}
