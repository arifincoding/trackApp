<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'nama' => $this->faker->word(),
            'kategori' => $this->faker->word(),
            'cacatProduk' => $this->faker->paragraph(3, false),
            'kelengkapan' => $this->faker->sentence(3, false),
            'catatan' => $this->faker->paragraph(3, false)
        ];
    }
}