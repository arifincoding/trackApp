<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ServiceFactory extends Factory
{

    protected $model = Service::class;

    public function definition()
    {
        return [
            'code' => $this->faker->randomNumber(9, true),
            'complaint' => $this->faker->paragraph(3, false),
            'down_payment' => $this->faker->numberBetween(25000, 500000),
            'status' => 'antri',
            'estimated_cost' => $this->faker->numberBetween(25000, 1000000),
            'customer_id' => $this->faker->randomNumber(2, false),
            'product_id' => $this->faker->randomNumber(2, false),
            'need_approval' => true,
            'is_approved' => null,
            'is_cost_confirmation' => false,
            'is_take' => false,
            'entry_at' => Carbon::now('GMT+7'),
            'taked_at' => null,
            'cs_username' => $this->faker->randomNumber(7, true),
            'tecnician_username' => $this->faker->randomNumber(7, true),
            'total_cost' => null,
            'warranty' => null
        ];
    }
}
