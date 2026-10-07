<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PasienFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nik' => $this->faker->unique()->numerify('################'),
            'nama' => $this->faker->name(),
            'tgl_lahir' => $this->faker->date('Y-m-d', '-18 years'),
            'jenis_kelamin' => $this->faker->randomElement(['L', 'P']),
            'alamat' => $this->faker->address(),
            'no_telp' => $this->faker->numerify('08##########'),
            'golongan_darah' => $this->faker->randomElement(['A', 'B', 'AB', 'O']),
        ];
    }
}
