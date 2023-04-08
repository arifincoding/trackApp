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
            'username' => '',
            'category_id' => ''
        ];
    }

    public function withRelation(): Factory
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        return $this->state(function (array $attributes) use ($user, $category) {
            return [
                'username' => $user->username,
                'category_id' => $category->id
            ];
        });
    }

    public function withCategory(): Factory
    {
        $category = Category::factory()->create();
        return $this->state(function (array $attributes) use ($category) {
            return [
                'category_id' => $category->id
            ];
        });
    }
}