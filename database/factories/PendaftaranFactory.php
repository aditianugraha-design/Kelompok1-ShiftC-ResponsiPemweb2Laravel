<?php

namespace Database\Factories;

use App\Models\Dokter;
use App\Models\Pasien;
use Illuminate\Database\Eloquent\Factories\Factory;

class PendaftaranFactory extends Factory
{
    public function definition(): array
    {
        return [
            'kode_daftar' => 'REG-' . $this->faker->unique()->numerify('########'),
            'pasien_id' => Pasien::inRandomOrder()->first()?->id ?? Pasien::factory(),
            'dokter_id' => Dokter::inRandomOrder()->first()?->id ?? Dokter::factory(),
            'tgl_kunjungan' => $this->faker->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d'),
            'jam_kunjungan' => $this->faker->time('H:i'),
            'keluhan' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['menunggu', 'diproses', 'selesai', 'batal']),
        ];
    }
}
