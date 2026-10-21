<?php

namespace Tests\Feature;

use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PendaftaranStatusFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'dokter']);
        Role::firstOrCreate(['name' => 'pasien']);
    }

    private function makeAdmin(): User
    {
        $user = User::factory()->create(['role' => 'admin']);
        $user->assignRole('admin');

        return $user;
    }

    private function makePasienUser(): array
    {
        $user = User::factory()->create(['role' => 'pasien']);
        $user->assignRole('pasien');
        $pasien = Pasien::factory()->create(['user_id' => $user->id]);

        return [$user, $pasien];
    }

    private function makeDokterUser(): array
    {
        $user = User::factory()->create(['role' => 'dokter']);
        $user->assignRole('dokter');
        $dokter = Dokter::factory()->create(['user_id' => $user->id]);

        return [$user, $dokter];
    }

    private function makePendaftaran(array $overrides = []): Pendaftaran
    {
        $pasien = Pasien::factory()->create();
        $dokter = Dokter::factory()->create();

        return Pendaftaran::create(array_merge([
            'kode_daftar' => Pendaftaran::generateKodeDaftar().random_int(10, 99),
            'pasien_id' => $pasien->id,
            'dokter_id' => $dokter->id,
            'tgl_kunjungan' => today()->toDateString(),
            'jam_kunjungan' => '09:00',
            'keluhan' => 'Demam',
            'status' => 'menunggu',
        ], $overrides));
    }

    public function test_menunggu_bisa_ke_diproses(): void
    {
        Sanctum::actingAs($this->makeAdmin());
        $p = $this->makePendaftaran(['status' => 'menunggu']);

        $this->putJson("/api/pendaftaran/{$p->id}", ['status' => 'diproses'])
            ->assertOk()
            ->assertJsonPath('data.status', 'diproses');
    }

    public function test_menunggu_ke_selesai_ditolak(): void
    {
        Sanctum::actingAs($this->makeAdmin());
        $p = $this->makePendaftaran(['status' => 'menunggu']);

        $this->putJson("/api/pendaftaran/{$p->id}", ['status' => 'selesai'])
            ->assertStatus(422);
    }

    public function test_diproses_ke_batal_ok_tapi_ke_menunggu_ditolak(): void
    {
        Sanctum::actingAs($this->makeAdmin());
        $p = $this->makePendaftaran(['status' => 'diproses']);

        $this->putJson("/api/pendaftaran/{$p->id}", ['status' => 'batal'])->assertOk();

        $p2 = $this->makePendaftaran(['status' => 'diproses']);
        $this->putJson("/api/pendaftaran/{$p2->id}", ['status' => 'menunggu'])->assertStatus(422);
    }

    public function test_selesai_dan_batal_final(): void
    {
        Sanctum::actingAs($this->makeAdmin());

        $p = $this->makePendaftaran(['status' => 'selesai']);
        $this->putJson("/api/pendaftaran/{$p->id}", ['status' => 'batal'])->assertStatus(422);

        $b = $this->makePendaftaran(['status' => 'batal']);
        $this->putJson("/api/pendaftaran/{$b->id}", ['status' => 'diproses'])->assertStatus(422);
    }

    public function test_rekam_medis_auto_selesai_dan_tolak_jika_final(): void
    {
        $admin = $this->makeAdmin();
        Sanctum::actingAs($admin);
        $p = $this->makePendaftaran(['status' => 'diproses']);

        $this->postJson('/api/rekam-medis', [
            'pendaftaran_id' => $p->id,
            'diagnosa' => 'Flu',
        ])->assertCreated()
            ->assertJsonPath('data.pendaftaran.status', 'selesai');

        $this->assertDatabaseHas('pendaftarans', ['id' => $p->id, 'status' => 'selesai']);

        $b = $this->makePendaftaran(['status' => 'batal']);
        $this->postJson('/api/rekam-medis', [
            'pendaftaran_id' => $b->id,
            'diagnosa' => 'Flu',
        ])->assertStatus(422);
    }

    public function test_pasien_bisa_batalkan_menunggu_miliknya(): void
    {
        [$user, $pasien] = $this->makePasienUser();
        $dokter = Dokter::factory()->create();
        $p = Pendaftaran::create([
            'kode_daftar' => Pendaftaran::generateKodeDaftar().'11',
            'pasien_id' => $pasien->id,
            'dokter_id' => $dokter->id,
            'tgl_kunjungan' => today()->toDateString(),
            'jam_kunjungan' => '10:00',
            'keluhan' => 'Batuk',
            'status' => 'menunggu',
        ]);

        Sanctum::actingAs($user);
        $this->patchJson("/api/pendaftaran/{$p->id}/batal")->assertOk()
            ->assertJsonPath('data.status', 'batal');
    }

    public function test_update_tanpa_status_tidak_500(): void
    {
        Sanctum::actingAs($this->makeAdmin());
        $p = $this->makePendaftaran(['status' => 'diproses']);

        $this->putJson("/api/pendaftaran/{$p->id}", ['keluhan' => 'Update tanpa status'])
            ->assertStatus(422);
    }

    public function test_update_status_sama_dengan_data_lain_ok(): void
    {
        Sanctum::actingAs($this->makeAdmin());
        $p = $this->makePendaftaran(['status' => 'diproses']);

        $this->putJson("/api/pendaftaran/{$p->id}", ['status' => 'diproses', 'keluhan' => 'Batuk berdahak'])
            ->assertOk()
            ->assertJsonPath('data.status', 'diproses');

        $this->assertDatabaseHas('pendaftarans', ['id' => $p->id, 'keluhan' => 'Batuk berdahak']);
    }

    public function test_dokter_melihat_antrean_hari_ini_urut_jam_dan_bisa_update(): void
    {
        [$user, $dokter] = $this->makeDokterUser();

        $siang = Pendaftaran::create([
            'kode_daftar' => Pendaftaran::generateKodeDaftar().'44',
            'pasien_id' => Pasien::factory()->create()->id,
            'dokter_id' => $dokter->id,
            'tgl_kunjungan' => today()->toDateString(),
            'jam_kunjungan' => '11:00',
            'keluhan' => 'Kontrol',
            'status' => 'menunggu',
        ]);

        $pagi = Pendaftaran::create([
            'kode_daftar' => Pendaftaran::generateKodeDaftar().'45',
            'pasien_id' => Pasien::factory()->create()->id,
            'dokter_id' => $dokter->id,
            'tgl_kunjungan' => today()->toDateString(),
            'jam_kunjungan' => '08:00',
            'keluhan' => 'Demam',
            'status' => 'menunggu',
        ]);

        $besok = Pendaftaran::create([
            'kode_daftar' => Pendaftaran::generateKodeDaftar().'46',
            'pasien_id' => Pasien::factory()->create()->id,
            'dokter_id' => $dokter->id,
            'tgl_kunjungan' => today()->addDay()->toDateString(),
            'jam_kunjungan' => '08:00',
            'keluhan' => 'Besok',
            'status' => 'menunggu',
        ]);

        $dokterLain = Dokter::factory()->create();
        $milikLain = $this->makePendaftaran(['dokter_id' => $dokterLain->id, 'status' => 'menunggu']);

        Sanctum::actingAs($user);

        // Default: hanya hari ini, urut jam paling awal
        $res = $this->getJson('/api/pendaftaran');
        $res->assertOk();
        $ids = collect($res->json('data'))->pluck('id')->all();
        $this->assertSame([$pagi->id, $siang->id], $ids);
        $this->assertNotContains($besok->id, $ids);
        $this->assertNotContains($milikLain->id, $ids);

        // Filter tanggal eksplisit tetap bisa lihat hari lain
        $resBesok = $this->getJson('/api/pendaftaran?tgl_kunjungan='.today()->addDay()->toDateString());
        $resBesok->assertOk();
        $this->assertContains($besok->id, collect($resBesok->json('data'))->pluck('id')->all());

        $this->putJson("/api/pendaftaran/{$siang->id}", ['status' => 'diproses'])
            ->assertOk()
            ->assertJsonPath('data.status', 'diproses');
    }

    public function test_pasien_tidak_bisa_batalkan_diproses_atau_milik_orang(): void
    {
        [$user, $pasien] = $this->makePasienUser();
        $dokter = Dokter::factory()->create();

        $p = Pendaftaran::create([
            'kode_daftar' => Pendaftaran::generateKodeDaftar().'22',
            'pasien_id' => $pasien->id,
            'dokter_id' => $dokter->id,
            'tgl_kunjungan' => today()->toDateString(),
            'jam_kunjungan' => '10:00',
            'keluhan' => 'Batuk',
            'status' => 'diproses',
        ]);

        Sanctum::actingAs($user);
        $this->patchJson("/api/pendaftaran/{$p->id}/batal")->assertStatus(422);

        [$other, $otherPasien] = $this->makePasienUser();
        $po = Pendaftaran::create([
            'kode_daftar' => Pendaftaran::generateKodeDaftar().'33',
            'pasien_id' => $otherPasien->id,
            'dokter_id' => $dokter->id,
            'tgl_kunjungan' => today()->toDateString(),
            'jam_kunjungan' => '11:00',
            'keluhan' => 'Pusing',
            'status' => 'menunggu',
        ]);

        $this->patchJson("/api/pendaftaran/{$po->id}/batal")->assertForbidden();
    }
}
