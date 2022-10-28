<?php

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{

    protected $model = Customer::class;

    public function definition()
    {
        return [
            'nama' => $this->faker->name(),
            'noHp' => $this->faker->numerify('628##########'),
            'bisaWa' => true
        ];
    }
}