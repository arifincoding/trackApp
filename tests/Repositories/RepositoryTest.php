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
        $this->category = Category::factory()->count(3)->create();
    }

    public function testShouldCreateSingleData()
    {
        $result = $this->repository->save(['name' => 'uji create single category']);
        $this->assertEquals([4, 'uji create single category'], [$result->id, $result->name]);
    }

    public function testShouldUpdateSingleDataById()
    {
        $result = $this->repository->save(['name' => 'uji update single category'], 2);
        $this->assertEquals([2, 'uji update single category'], [$result->id, $result->name]);
    }

    public function testShouldFindDataById()
    {
        $result = $this->repository->findById(2);
        $this->assertEquals($this->category[1]->toArray(), $result->toArray());
    }

    public function testFindDataByIdShouldReturnException()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->repository->findById(4);
    }

    public function testFindDataByIdShouldReturnNull()
    {
        $result = $this->repository->findById(4, ['*'], false);
        $this->assertEquals($result, null);
    }

    public function testShoudDeleteSingleDataById()
    {
        $result = $this->repository->delete(2);
        $this->assertEquals($result, true);
    }

    public function testDeleteSingleDataByIdShouldReturnException()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->repository->delete(4);
    }

    public function testDeleteSingleDataByIdShouldReturnFalse()
    {
        $result = $this->repository->delete(4, 'id', false);
        $this->assertEquals($result, false);
    }
}
