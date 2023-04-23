<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'category_id' => Category::factory(),
            'customer_id' => Customer::factory(),
            'product_defects' => $this->faker->paragraph(3, false),
            'completeness' => $this->faker->sentence(3, false),
        ];
    }
}
