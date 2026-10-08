<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dokters', function (Blueprint $table) {
            $table->renameColumn('no_sip', 'nip');
        });

        Schema::table('dokters', function (Blueprint $table) {
            $table->renameColumn('no_telp', 'no_telepon');
        });

        Schema::table('dokters', function (Blueprint $table) {
            $table->string('nip')->nullable()->change();
            $table->text('jadwal_praktik')->nullable()->change();
        });

        Schema::table('dokters', function (Blueprint $table) {
            $table->string('status', 20)->default('aktif');
        });

        $this->normalizeJadwalPraktik();
    }

    public function down(): void
    {
        $this->restoreLegacyJadwalPraktik();

        Schema::table('dokters', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        DB::table('dokters')->whereNull('nip')->update(['nip' => '']);

        Schema::table('dokters', function (Blueprint $table) {
            $table->string('nip')->nullable(false)->default('')->change();
        });

        Schema::table('dokters', function (Blueprint $table) {
            $table->json('jadwal_praktik')->nullable()->change();
        });

        Schema::table('dokters', function (Blueprint $table) {
            $table->renameColumn('nip', 'no_sip');
        });

        Schema::table('dokters', function (Blueprint $table) {
            $table->renameColumn('no_telepon', 'no_telp');
        });
    }

    /**
     * Ubah data jadwal_praktik lama (JSON per hari) menjadi teks terbaca.
     */
    protected function normalizeJadwalPraktik(): void
    {
        $dokters = DB::table('dokters')->whereNotNull('jadwal_praktik')->get();

        foreach ($dokters as $dokter) {
            $decoded = json_decode($dokter->jadwal_praktik, true);

            if (! is_array($decoded) || $decoded === []) {
                continue;
            }

            $jadwal = [];
            foreach ($decoded as $hari => $jam) {
                $jadwal[] = ucfirst($hari).' '.$jam;
            }

            DB::table('dokters')
                ->where('id', $dokter->id)
                ->update(['jadwal_praktik' => implode(', ', $jadwal)]);
        }
    }

    /**
     * Kembalikan nilai jadwal_praktik ke format JSON sebelum kolom diubah ke json.
     */
    protected function restoreLegacyJadwalPraktik(): void
    {
        $dokters = DB::table('dokters')->whereNotNull('jadwal_praktik')->get();

        foreach ($dokters as $dokter) {
            if (json_decode($dokter->jadwal_praktik, true) === null) {
                DB::table('dokters')
                    ->where('id', $dokter->id)
                    ->update(['jadwal_praktik' => null]);
            }
        }
    }
};
