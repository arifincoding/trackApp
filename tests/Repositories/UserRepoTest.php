<?php

use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;

class UserRepotest extends TestCase
{
    use DatabaseMigrations;

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
        User::factory()->count(3)->cs()->create();
        $user = User::select(['id as idPegawai', 'username', 'namaDepan', 'namaBelakang', 'jenisKelamin', 'noHp', 'peran', 'email', 'alamat'])->where('id', 2)->first();
        $result = $this->repository->getDataById(2);
        $this->assertEquals($result, $user);
    }

    public function testShouldFindSingleDataByUsername()
    {
        User::factory()->create();
        $user = User::factory()->create([
            'username' => '2210002'
        ]);
        User::factory()->cs()->create();
        $result = $this->repository->findByUsername('2210002');
        $this->assertEquals($user->toArray(), $result->toArray());
    }

    public function testShouldChangePasswordUser()
    {
        User::factory()->create();
        User::factory()->cs()->create([
            'username' => '2210002'
        ]);
        User::factory()->cs()->create();
        $result = $this->repository->changePassword(['sandiBaru' => 'rahasia'], '2210002');
        $this->assertEquals(true, $result);
    }

    public function testShouldRegisterUser()
    {
        User::factory()->count(3)->create([
            'username' => null,
            'password' => null
        ]);
        $result = $this->repository->registerUser(2);
        unset($result['password']);
        $user = User::find(2);
        $this->assertEquals(['email' => $user->email, 'username' => $user->username], $result);
    }
}