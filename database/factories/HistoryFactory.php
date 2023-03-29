<?php

namespace Database\Factories;

use App\Models\History;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class HistoryFactory extends Factory
{

    protected $model = History::class;

    public function definition()
    {
        return [
            'service_id' => $this->faker->randomNumber(2, false),
            'status' => 'antri',
            'message' => $this->faker->sentence(4),
            'created_at' => Carbon::now('GMT+7')
        ];
    }
}
