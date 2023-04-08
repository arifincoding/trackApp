<?php

use App\Models\Category;
use App\Models\Responbility;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ResponbilityRepoTest extends TestCase
{

    use DatabaseTransactions;

    private $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make('App\Repositories\ResponbilityRepository');
    }

    public function testShouldGetListDataByUsername()
    {
        $userFactory = User::factory()->count(2)->create();
        $categoryFactory = Category::factory()->count(4)->create();
        $responbilityFactory =  Responbility::factory()->count(8)->state(new Sequence(
            ['username' => $userFactory[0]->username, 'category_id' => $categoryFactory[1]->id],
            ['username' => $userFactory[1]->username, 'category_id' => $categoryFactory[0]->id],
            ['username' => $userFactory[0]->username, 'category_id' => $categoryFactory[2]->id],
            ['username' => $userFactory[1]->username, 'category_id' => $categoryFactory[3]->id],
        ))->create();
        $responbility = Responbility::with('category')->whereIn('id', [
            $responbilityFactory[1]->id,
            $responbilityFactory[3]->id,
            $responbilityFactory[5]->id,
            $responbilityFactory[7]->id,
        ])->get();
        $result = $this->repository->getListDataByUsername($userFactory[1]->username);
        $this->assertEquals($responbility->toArray(), $result->toArray());
    }

    public function testShouldCreateManyResponbility()
    {
        $userFactory = User::factory()->create();
        $categoryFactory = Category::factory()->count(3)->create();
        $categoryId = [];
        foreach ($categoryFactory as $item) {
            $categoryId[] += $item->id;
        }
        $result = $this->repository->create(['category_id' => $categoryId], $userFactory->username);
        $this->assertEquals(true, $result);
        $this->assertEquals(3, Responbility::where('username', $userFactory->username)->whereIn('category_id', $categoryId)->count());
    }

    public function testShouldDeleteListResponbilityByUsername()
    {
        $userFactory = User::factory()->count(3)->create();
        Responbility::factory()->count(6)->state(new Sequence(
            ['username' => $userFactory[0]->username],
            ['username' => $userFactory[1]->username],
            ['username' => $userFactory[2]->username],
        ))->withCategory()->create();
        $result = $this->repository->deleteByUsername($userFactory[1]->username);
        $this->assertEquals(true, $result);
        $this->assertEquals(0, Responbility::where('username', $userFactory[1]->username)->count());
    }

    public function testDeleteListResponbilityByUsernameShouldReturnFalse()
    {
        $responbilityFactory = Responbility::factory()->withRelation()->create();
        Responbility::where('id', $responbilityFactory->id)->delete();
        $result = $this->repository->deleteByUsername($responbilityFactory->username);
        $this->assertEquals(false, $result);
    }
}
