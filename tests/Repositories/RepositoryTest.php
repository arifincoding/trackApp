<?php

use App\Models\Category;
use Laravel\Lumen\Testing\DatabaseMigrations;

class RepositoryTest extends TestCase
{

    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make('App\Repositories\CategoryRepository');
    }

    public function testShouldCreateAData()
    {
        $result = $this->repository->save(['nama' => 'uji create single category']);
        $this->assertEquals([1, 'uji create single category'], [$result->id, $result->nama]);
    }

    public function testShouldUpdateData()
    {
        Category::factory()->count(3)->create();
        $result = $this->repository->save(['nama' => 'uji update single category'], 2);
        $this->assertEquals([2, 'uji update single category'], [$result->id, $result->nama]);
    }

    public function testShouldFindDataById()
    {
        $category = Category::factory()->count(3)->create();
        $result = $this->repository->findById(2);
        $this->assertEquals($category[1]->toArray(), $result->toArray());
    }

    public function testShoudDeleteDataById()
    {
        Category::factory()->count(3)->create();
        $result = $this->repository->delete(2);
        $this->assertEquals($result, true);
    }
}