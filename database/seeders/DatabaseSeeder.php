<?php

namespace Database\Seeders;

use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\RekamMedis;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
        ]);

        Pasien::factory(20)->create();
        Dokter::factory(5)->create();
        Pendaftaran::factory(30)->create();
        RekamMedis::factory(10)->create();
    }
}
