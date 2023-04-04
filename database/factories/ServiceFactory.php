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

        $user = User::factory()->count(2)->sequence(['role' => 'customer service'], ['role' => 'teknisi'])->create();
        $product = Product::factory()->create();

        return [
            'code' => $this->faker->randomNumber(9, true),
            'complaint' => $this->faker->paragraph(3, false),
            'down_payment' => $this->faker->numberBetween(25000, 500000),
            'status' => $this->faker->randomElement(['antri', 'mulai diagnosa', 'selesai diagnosa', 'proses', 'selesai']),
            'estimated_cost' => $this->faker->numberBetween(25000, 1000000),
            'product_id' => $product->id,
            'need_approval' => true,
            'is_approved' => null,
            'is_cost_confirmation' => false,
            'is_take' => false,
            'entry_at' => Carbon::now('GMT+7'),
            'taked_at' => null,
            'cs_username' => $user[0]->username,
            'tecnician_username' => $user[1]->username,
            'total_cost' => 0,
            'warranty' => null,
            'note' => $this->faker->paragraph(3, false)
        ];
    }
}
