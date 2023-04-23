<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Responbility;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;


class ResponbilityFactory extends Factory
{

    protected $model = Responbility::class;

    public function definition()
    {
        return [
            'username' => function (array $attributes) {
                $user = User::factory()->create(['role' => 'teknisi']);
                return $user->username;
            },
            'category_id' => Category::factory()
        ];
    }
}
