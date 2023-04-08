<?php

use App\Models\User;
use Laravel\Lumen\Testing\DatabaseTransactions;

class UserRepoTest extends TestCase
{
    use DatabaseTransactions;

    private $repository;
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make('App\Repositories\UserRepository');
    }

    public function testShouldGetListData()
    {
        $user = User::factory()->count(3)->tecnician()->create();
        $result = $this->repository->getListData();
        $this->assertEquals($user->toArray(), $result->toArray());
    }

    public function testShouldGetSingleDataById()
    {
        $userFactory = User::factory()->count(3)->cs()->create();
        $result = $this->repository->getDataById($userFactory[1]->id);
        $this->assertEquals($userFactory[1]->toArray(), $result->toArray());
    }

    public function testShouldFindSingleDataByUsername()
    {
        $userFactory = User::factory()->count(3)->create();
        $result = $this->repository->findByUsername($userFactory[1]->username);
        $this->assertEquals($userFactory[1]->toArray(), $result->toArray());
    }

    public function testShouldChangePasswordUser()
    {
        $userFactory = User::factory()->create();
        $result = $this->repository->changePassword(['new_password' => 'rahasia'], $userFactory->username);
        $this->assertEquals(true, $result);
    }

    public function testShouldRegisterUser()
    {
        $userFactory = User::factory()->count(2)->create([
            'username' => null,
            'password' => null
        ]);
        $result = $this->repository->registerUser($userFactory[1]->id);
        unset($result['password']);
        $user = User::whereIn('id', [$userFactory[0]->id, $userFactory[1]->id])->get();
        $this->assertEquals(['email' => $user[1]->email, 'username' => $user[1]->username], $result);
        $this->assertEquals(null, $user[0]->username);
    }
}
