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
        $customer = Customer::factory()->create();
        $category = Category::factory()->create();
        return [
            'name' => $this->faker->word(),
            'category_id' => $category->id,
            'customer_id' => $customer->id,
            'product_defects' => $this->faker->paragraph(3, false),
            'completeness' => $this->faker->sentence(3, false),
        ];
    }
}
