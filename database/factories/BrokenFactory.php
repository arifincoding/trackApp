<?php

namespace Database\Factories;

use App\Models\Broken;
use Illuminate\Database\Eloquent\Factories\Factory;

class BrokenFactory extends Factory
{

    protected $model = Broken::class;

    public function definition()
    {
        return [
            'judul' => $this->faker->sentence(3),
            'disetujui' => null,
            'deskripsi' => $this->faker->paragraph(2),
            'idService' => $this->faker->randomNumber(2, false),
            'biaya' => $this->faker->numberBetween(25000, 1000000)
        ];
    }
}