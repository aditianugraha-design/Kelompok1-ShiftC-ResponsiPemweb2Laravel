<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DokterFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama' => 'dr. ' . $this->faker->name(),
            'no_sip' => 'SIP-' . $this->faker->unique()->numerify('#####'),
            'spesialisasi' => $this->faker->randomElement([
                'Umum', 'Gigi', 'Anak', 'Kandungan', 'Mata', 'THT',
            ]),
            'no_telp' => $this->faker->numerify('08##########'),
            'jadwal_praktik' => [
                'senin' => '08:00-12:00',
                'selasa' => '13:00-17:00',
                'kamis' => '08:00-12:00',
            ],
        ];
    }
}
