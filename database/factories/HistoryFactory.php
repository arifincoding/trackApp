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
        return [
            'service_id' => Service::factory(),
            'status' => $this->faker->randomElement(['antri', 'mulai diagnosa', 'selesai diagnosa', 'proses', 'selesai']),
            'message' => $this->faker->sentence(4),
            'created_at' => Carbon::now('GMT+7')
        ];
    }
}
