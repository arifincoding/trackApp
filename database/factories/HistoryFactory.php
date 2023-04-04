<?php

namespace Database\Factories;

use App\Models\History;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class HistoryFactory extends Factory
{

    protected $model = History::class;

    public function definition()
    {
        $service = Service::factory()->create();
        return [
            'service_id' => $service->id,
            'status' => $this->faker->randomElement(['antri', 'mulai diagnosa', 'selesai diagnosa', 'proses', 'selesai']),
            'message' => $this->faker->sentence(4),
            'created_at' => Carbon::now('GMT+7')
        ];
    }
}
