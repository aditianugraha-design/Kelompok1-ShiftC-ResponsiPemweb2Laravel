<?php

namespace Database\Factories;

use App\Models\Pendaftaran;
use Illuminate\Database\Eloquent\Factories\Factory;

class RekamMedisFactory extends Factory
{
    public function definition(): array
    {
        return [
            'pendaftaran_id' => Pendaftaran::inRandomOrder()->first()?->id ?? Pendaftaran::factory(),
            'diagnosa' => $this->faker->sentence(4),
            'tindakan' => $this->faker->sentence(3),
            'resep' => $this->faker->sentence(5),
            'catatan' => $this->faker->sentence(),
        ];
    }
}
