<?php

use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Testing\DatabaseMigrations;

class CategoryRepoTest extends TestCase
{
    use DatabaseMigrations;

    private $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make('App\Repositories\CategoryRepository');
    }

    public function testShouldGetListData()
    {
        Category::factory()->count(3)->create();
        $category = Category::select('id as idKategori', 'nama')->get();
        $result = $this->repository->getListData();
        $this->assertEquals($category->toArray(), $result->toArray());
    }

    public function testShouldGetDataById()
    {
        Category::factory()->count(3)->create();
        $category = Category::select('id as idKategori', 'nama')->where('id', 2)->first();
        $result = $this->repository->getDataById(2);
        $this->assertEquals($category->toArray(), $result->toArray());
    }

    public function testGetDataByIdShouldReturnException()
    {
        Category::factory()->count(2)->create();
        $this->expectException(ModelNotFoundException::class);
        $result = $this->repository->getDataById(3);
    }

    public function ShouldGetDataNotInResponbility()
    {
    }
}