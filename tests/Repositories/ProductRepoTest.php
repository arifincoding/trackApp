<?php

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ProductRepoTest extends TestCase
{

    use DatabaseTransactions;

    private $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make('App\Repositories\ProductRepository');
    }

    public function testShouldCreateSingleProductData()
    {
        $category = Category::factory()->create();
        $inputs = [
            'name' => 'asus tuf 505DD',
            'category_id' => $category->id,
            'completeness' => 'baterai, cas',
            'customer_id' => 'tidak ada',
            'product_defects' => 'body tergores'
        ];
        $customer = Customer::factory()->create();
        $result = $this->repository->create($inputs, $customer->id);
        $product = Product::orderByDesc('id')->first();
        $this->assertEquals($product->toArray(), $result->toArray());
    }
}
