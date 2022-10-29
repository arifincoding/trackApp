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
            'idService' => $this->faker->randomNumber(2, false),
            'status' => 'antri',
            'pesan' => $this->faker->sentence(4),
            'waktu' => Carbon::now('GMT+7')
        ];
    }
}