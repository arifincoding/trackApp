<?php

use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Testing\DatabaseTransactions;

class RepositoryTest extends TestCase
{

    use DatabaseTransactions;

    private $repository;
    private $category;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make('App\Repositories\CategoryRepository');
    }

    public function testShouldCreateSingleData()
    {
        $result = $this->repository->save(['name' => 'uji create single category']);
        $category = Category::orderByDesc('id')->first();
        $this->assertEquals('uji create single category', $result->name);
    }

    public function testShouldUpdateSingleDataById()
    {
        $categoryFactory = Category::factory()->create();
        $result = $this->repository->save(['name' => 'uji update single category'], $categoryFactory->id);
        $this->assertEquals([$categoryFactory->id, 'uji update single category'], [$result->id, $result->name]);
    }

    public function testShouldFindDataById()
    {
        $categoryFactory = Category::factory()->count(3)->create();
        $result = $this->repository->findById($categoryFactory[1]->id);
        $this->assertEquals($categoryFactory[1]->toArray(), $result->toArray());
    }

    public function testFindDataByIdShouldReturnException()
    {
        $categoryFactory = Category::factory()->create();
        Category::where('id', $categoryFactory->id)->delete();
        $this->expectException(ModelNotFoundException::class);
        $this->repository->findById($categoryFactory->id);
    }

    public function testFindDataByIdShouldReturnNull()
    {
        $categoryFactory = Category::factory()->create();
        Category::where('id', $categoryFactory->id)->delete();
        $result = $this->repository->findById($categoryFactory->id, ['*'], false);
        $this->assertEquals(null, $result);
    }

    public function testShoudDeleteSingleDataById()
    {
        $categoryFactory = Category::factory()->create();
        $result = $this->repository->delete($categoryFactory->id);
        $this->assertEquals($result, true);
        $this->assertEquals(0, Category::where('id', $categoryFactory->id)->count());
    }

    public function testDeleteSingleDataByIdShouldReturnException()
    {
        $categoryFactory = Category::factory()->create();
        Category::where('id', $categoryFactory->id)->delete();
        $this->expectException(ModelNotFoundException::class);
        $this->repository->delete($categoryFactory->id);
    }

    public function testDeleteSingleDataByIdShouldReturnFalse()
    {
        $categoryFactory = Category::factory()->create();
        Category::where('id', $categoryFactory->id)->delete();
        $result = $this->repository->delete($categoryFactory->id, 'id', false);
        $this->assertEquals($result, false);
    }
}
