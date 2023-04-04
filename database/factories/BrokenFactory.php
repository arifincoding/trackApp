<?php

namespace Database\Factories;

use App\Models\Broken;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Service;

class BrokenFactory extends Factory
{

    protected $model = Broken::class;

    public function definition()
    {
        $service = Service::factory()->create();
        return [
            'title' => $this->faker->sentence(3),
            'is_approved' => null,
            'description' => $this->faker->paragraph(2),
            'service_id' => $service->id,
            'cost' => $this->faker->numberBetween(25000, 1000000)
        ];
    }
}
