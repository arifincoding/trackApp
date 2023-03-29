<?php

namespace Database\Factories;

use App\Models\Responbility;
use Illuminate\Database\Eloquent\Factories\Factory;


class ResponbilityFactory extends Factory
{

    protected $model = Responbility::class;

    public function definition()
    {
        return [
            'username' => $this->faker->randomNumber(7, true),
            'category_id' => $this->faker->randomNumber(2, false)
        ];
    }
}
