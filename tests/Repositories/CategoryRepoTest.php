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
        $this->markTestSkipped();
        Category::factory()->count(3)->create();
        $category = Category::select('id as category_id', 'name')->get();
        $result = $this->repository->getListData();
        $this->assertEquals($category->toArray(), $result->toArray());
    }

    public function testShouldGetDataById()
    {
        $this->markTestSkipped();
        Category::factory()->count(3)->create();
        $category = Category::select('id as category_id', 'name')->orderByDesc('id')->first();
        $result = $this->repository->getDataById($category->category_id);
        $this->assertEquals($category->toArray(), $result->toArray());
    }

    public function testGetDataByIdShouldReturnException()
    {
        $this->markTestSkipped();
        Category::factory()->count(2)->create();
        $this->expectException(ModelNotFoundException::class);
        $result = $this->repository->getDataById(3);
    }

    public function testShouldReturnCoba()
    {
        $this->markTestSkipped();
        Category::factory()->count(4)->sequence(['name' => 'ahmad'], ['name' => 'arifin'], ['name' => 'mark'], ['name' => 'jonshon'])->create();
        $data = $this->repository->coba('jon');
        foreach ($data as $item) {
            echo $item['name'];
        }
    }

    public function testShouldReturnCategoriesNotInUsernameResponbilities()
    {
        $data = $this->repository->getDataNotInResponbility('30031999');
        foreach ($data as $item) {
            echo PHP_EOL . 'id = ' . $item->id;
        }
        // var_dump($data);
    }
}
