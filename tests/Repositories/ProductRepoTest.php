<?php

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
        $inputs = [
            'name' => 'asus tuf 505DD',
            'category_id' => 1,
            'completeness' => 'baterai, cas',
            'note' => 'tidak ada',
            'product_defects' => 'body tergores'
        ];
        $result = $this->repository->create($inputs);
        $this->assertEquals(1, $result);
    }
}
