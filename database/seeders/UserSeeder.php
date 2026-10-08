<?php

namespace Database\Seeders;

use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin Klinik',
            'email' => 'admin@klinik.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'no_telp' => '081234567890',
        ]);
        $admin->assignRole('admin');

        $userDokter = User::create([
            'name' => 'dr. Budi Santoso',
            'email' => 'dokter@klinik.test',
            'password' => Hash::make('password'),
            'role' => 'dokter',
            'no_telp' => '081234567891',
        ]);
        $userDokter->assignRole('dokter');

        Dokter::create([
            'user_id' => $userDokter->id,
            'nama' => 'dr. Budi Santoso',
            'nip' => 'STR-0001',
            'spesialisasi' => 'Umum',
            'no_telepon' => '081234567891',
            'jadwal_praktik' => 'Senin, Rabu & Jumat, 08:00 - 12:00 & 14:00 - 17:00',
            'status' => 'aktif',
        ]);

        $userPasien = User::create([
            'name' => 'Andi Wijaya',
            'email' => 'pasien@klinik.test',
            'password' => Hash::make('password'),
            'role' => 'pasien',
            'no_telp' => '081234567892',
        ]);
        $userPasien->assignRole('pasien');

        Pasien::create([
            'user_id' => $userPasien->id,
            'nik' => '1234567890123456',
            'nama' => 'Andi Wijaya',
            'tgl_lahir' => '1995-05-15',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Merdeka No. 10, Bandung',
            'no_telp' => '081234567892',
            'golongan_darah' => 'O',
        ]);
    }
}
