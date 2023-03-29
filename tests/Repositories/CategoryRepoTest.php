<?php

use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Testing\DatabaseTransactions;

class CategoryRepoTest extends TestCase
{
    use DatabaseTransactions;

    private $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make('App\Repositories\CategoryRepository');
    }

    public function testShouldGetListData()
    {
        Category::factory()->count(3)->create();
        $category = Category::select('id as category_id', 'name')->get();
        $result = $this->repository->getListData();
        $this->assertEquals($category->toArray(), $result->toArray());
    }

    public function testShouldGetDataById()
    {
        Category::factory()->count(3)->create();
        $category = Category::select('id as category_id', 'name')->orderByDesc('id')->first();
        $result = $this->repository->getDataById($category->category_id);
        $this->assertEquals($category->toArray(), $result->toArray());
    }

    public function testGetDataByIdShouldReturnException()
    {
        Category::factory()->count(2)->create();
        $this->expectException(ModelNotFoundException::class);
        $result = $this->repository->getDataById(3);
    }
}
