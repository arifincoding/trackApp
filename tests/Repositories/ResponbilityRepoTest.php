<?php

use App\Models\Category;
use App\Models\Responbility;
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
        $category = Category::factory()->count(3)->create();
        Responbility::factory()->for($category[0], 'category')->create([
            'username' => '2210001'
        ]);
        Responbility::factory()->for($category[1], 'category')->create([
            'username' => '2210005'
        ]);
        Responbility::factory()->for($category[2], 'category')->create([
            'username' => '2210001'
        ]);
        $responbility = Responbility::with('category')->where('username', '2210001')->get();
        $result = $this->repository->getListDataByUsername('2210001');
        $this->assertEquals($responbility->toArray(), $result->toArray());
    }

    public function testShouldCreateManyResponbility()
    {
        $inputs = [
            'category_id' => [
                2, 3, 4, 5, 6
            ]
        ];
        $username = '2210001';
        $result = $this->repository->create($inputs, $username);
        $this->assertEquals(true, $result);
    }

    public function testShouldDeleteListResponbilityByUsername()
    {
        Responbility::factory()->count(2)->create();
        Responbility::factory()->count(3)->create([
            'username' => '2210003'
        ]);
        Responbility::factory()->create();
        $result = $this->repository->deleteByUsername('2210003');
        $this->assertEquals(true, $result);
    }

    public function testDeleteListResponbilityByUsernameShouldReturnFalse()
    {
        Responbility::factory()->count(3)->create([
            'username' => '2210001'
        ]);
        $result = $this->repository->deleteByUsername('2210002');
        $this->assertEquals(false, $result);
    }
}
