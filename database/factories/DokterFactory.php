<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DokterFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama' => 'dr. ' . $this->faker->name(),
            'nip' => 'STR-' . $this->faker->unique()->numerify('######'),
            'spesialisasi' => $this->faker->randomElement([
                'Umum', 'Gigi', 'Anak', 'Kandungan', 'Mata', 'THT',
            ]),
            'no_telepon' => $this->faker->numerify('08##########'),
            'jadwal_praktik' => $this->faker->randomElement([
                'Senin - Jumat, 08:00 - 14:00',
                'Senin, Rabu & Jumat, 08:00 - 12:00',
                'Selasa & Kamis, 13:00 - 17:00',
                'Senin - Sabtu, 09:00 - 15:00',
            ]),
            'status' => $this->faker->randomElement(['aktif', 'aktif', 'aktif', 'non-aktif']),
        ];
    }
}
