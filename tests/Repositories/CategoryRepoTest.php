<?php

use App\Models\Category;
use App\Models\Responbility;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
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
        $category = Category::orderBy('name', 'asc')->get();
        $result = $this->repository->getListData();
        $this->assertEquals($category->toArray(), $result->toArray());
    }

    public function testShouldGetDataById()
    {
        $categoryFactory = Category::factory()->count(3)->create();
        $result = $this->repository->getDataById($categoryFactory[1]->id);
        $this->assertEquals($categoryFactory[1]->toArray(), $result->toArray());
    }

    public function testGetDataByIdShouldReturnException()
    {
        $categoryFactory = Category::factory()->create();
        Category::where('id', $categoryFactory->id)->delete();
        $this->expectException(ModelNotFoundException::class);
        $this->repository->getDataById($categoryFactory->id);
    }

    public function testShouldReturnCategoriesNotInUsernameResponbilities()
    {
        $categoryFactory = Category::factory()->count(5)->create();
        $user = User::factory()->create();
        Responbility::factory()->count(3)->sequence(function (Sequence $sequence) use ($categoryFactory, $user) {
            return [
                'username' => $user->username,
                'category_id' => $categoryFactory[$sequence->index + 1]->id
            ];
        })->create();
        $result = $this->repository->getDataNotInResponbility($user->username);
        $category = Category::whereIn('id', [$categoryFactory[0]->id, $categoryFactory[4]->id])->orderByDesc('id')->get();
        $this->assertEquals($result->toArray(), $category->toArray());
    }
}
