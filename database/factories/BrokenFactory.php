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
            'title' => $this->faker->sentence(3),
            'is_approved' => null,
            'description' => $this->faker->paragraph(2),
            'service_id' => $this->faker->randomNumber(2, false),
            'cost' => $this->faker->numberBetween(25000, 1000000)
        ];
    }
}
