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

class PendaftaranJadwalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'dokter']);
        Role::firstOrCreate(['name' => 'pasien']);
    }

    private function pasienUser(): User
    {
        $user = User::factory()->create(['role' => 'pasien']);
        $user->assignRole('pasien');
        Pasien::factory()->create(['user_id' => $user->id]);

        return $user;
    }

    private function payload(Dokter $dokter, string $tgl, string $jam): array
    {
        return [
            'dokter_id' => $dokter->id,
            'tgl_kunjungan' => $tgl,
            'jam_kunjungan' => $jam,
            'keluhan' => 'Batuk pilek',
        ];
    }

    public function test_tolak_hari_di_luar_jadwal(): void
    {
        $dokter = Dokter::factory()->create([
            'jadwal_praktik' => 'Senin, Rabu & Jumat, 08:00 - 12:00',
            'status' => 'aktif',
        ]);

        Sanctum::actingAs($this->pasienUser());

        // 2026-10-15 adalah Kamis -> di luar jadwal
        $this->postJson('/api/pendaftaran', $this->payload($dokter, '2026-10-15', '09:00'))
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_tolak_jam_di_luar_jadwal(): void
    {
        $dokter = Dokter::factory()->create([
            'jadwal_praktik' => 'Senin - Jumat, 08:00 - 12:00',
            'status' => 'aktif',
        ]);

        Sanctum::actingAs($this->pasienUser());

        // Rabu 2026-10-14 pukul 15:00 -> di luar slot
        $this->postJson('/api/pendaftaran', $this->payload($dokter, '2026-10-14', '15:00'))
            ->assertStatus(422);
    }

    public function test_terima_dalam_jadwal(): void
    {
        $dokter = Dokter::factory()->create([
            'jadwal_praktik' => 'Senin - Jumat, 08:00 - 12:00',
            'status' => 'aktif',
        ]);

        Sanctum::actingAs($this->pasienUser());

        $this->postJson('/api/pendaftaran', $this->payload($dokter, '2026-10-14', '09:00'))
            ->assertCreated();

        $this->assertDatabaseHas('pendaftarans', [
            'dokter_id' => $dokter->id,
            'jam_kunjungan' => '09:00',
        ]);
        $this->assertSame(
            '2026-10-14',
            Pendaftaran::where('dokter_id', $dokter->id)->first()->tgl_kunjungan->toDateString()
        );
    }

    public function test_tolak_dokter_nonaktif(): void
    {
        $dokter = Dokter::factory()->create([
            'jadwal_praktik' => 'Senin - Jumat, 08:00 - 12:00',
            'status' => 'non-aktif',
        ]);

        Sanctum::actingAs($this->pasienUser());

        $this->postJson('/api/pendaftaran', $this->payload($dokter, '2026-10-14', '09:00'))
            ->assertStatus(422);
    }

    public function test_update_jadwal_keluar_ditolak(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->assignRole('admin');

        $dokter = Dokter::factory()->create([
            'jadwal_praktik' => 'Senin - Jumat, 08:00 - 12:00',
            'status' => 'aktif',
        ]);

        $pendaftaran = Pendaftaran::create([
            'kode_daftar' => Pendaftaran::generateKodeDaftar().'71',
            'pasien_id' => Pasien::factory()->create()->id,
            'dokter_id' => $dokter->id,
            'tgl_kunjungan' => '2026-10-14',
            'jam_kunjungan' => '09:00',
            'keluhan' => 'Demam',
            'status' => 'menunggu',
        ]);

        Sanctum::actingAs($admin);

        // Pindah ke Sabtu -> di luar jadwal Senin-Jumat
        $this->putJson("/api/pendaftaran/{$pendaftaran->id}", [
            'status' => 'diproses',
            'tgl_kunjungan' => '2026-10-17',
        ])->assertStatus(422);
    }
}
