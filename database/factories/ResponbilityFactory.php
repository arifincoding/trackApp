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
        $user = User::factory()->create();
        $category = Category::factory()->create();
        return [
            'username' => $user->username,
            'category_id' => $category->id
        ];
    }
}
