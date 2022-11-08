<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class ProductRepoTest extends TestCase
{

    use DatabaseMigrations;

    private $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make('App\Repositories\ProductRepository');
    }

    public function testShouldCreateSingleProductData()
    {
        $inputs = [
            'nama' => 'asus tuf 505DD',
            'kategori' => 'laptop',
            'kelengkapan' => 'baterai, cas',
            'catatan' => 'tidak ada',
            'cacatProduk' => 'body tergores'
        ];
        $result = $this->repository->create($inputs);
        $this->assertEquals(1, $result);
    }
}