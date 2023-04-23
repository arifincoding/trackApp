<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Service;
use App\Models\User;
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
            'status' => $this->faker->randomElement(['antri', 'mulai diagnosa', 'selesai diagnosa', 'proses', 'selesai']),
            'estimated_cost' => $this->faker->numberBetween(25000, 1000000),
            'product_id' => Product::factory(),
            'need_approval' => true,
            'is_approved' => null,
            'is_cost_confirmation' => false,
            'is_take' => false,
            'entry_at' => Carbon::now('GMT+7'),
            'taked_at' => null,
            'cs_username' => function (array $attributes) {
                $user = User::factory()->create(['role' => 'customer service']);
                return $user->username;
            },
            'tecnician_username' => function (array $attributes) {
                $user = User::factory()->create(['role' => 'teknisi']);
                return $user->username;
            },
            'total_cost' => 0,
            'warranty' => null,
            'note' => $this->faker->paragraph(3, false)
        ];
    }
}
