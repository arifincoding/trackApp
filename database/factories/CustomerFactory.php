<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{

    protected $model = Customer::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'telp' => $this->faker->numerify('628##########'),
            'is_whatsapp' => true
        ];
    }
}
