<?php

use App\Models\User;
use App\Repositories\ResponbilityRepository;
use App\Repositories\UserRepository;
use App\Services\UserService;
use App\Transformers\UsersTransformer;
use App\Validations\UserValidation;
use Laravel\Lumen\Testing\DatabaseMigrations;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class UserSrvTest extends TestCase
{
    use DatabaseMigrations;

    private UserRepository $userRepository;
    private ResponbilityRepository $responbilityRepository;
    private UserService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->responbilityRepository = $this->createMock(ResponbilityRepository::class);
        $this->service = new UserService($this->userRepository, $this->responbilityRepository, new UserValidation());
    }

    public function testUserShouldLoginToTheApp()
    {
        $this->markTestSkipped("fitur ini ada static methodnya");
    }

    public function testShoudCreateRefreshToken()
    {
        $this->markTestSkipped("fitur ini bingung ngetestnya");
    }

    public function testShouldLogOutUserAuthenticad()
    {
        $this->markTestSkipped("fitur ini ada static methodnya");
    }

    public function testShouldGetUserAuthenticatedAccountInfo()
    {
        $this->markTestSkipped("fitur ini ada static methodnya");
    }

    public function testShoulUpdateAuthenticatedUserAccount()
    {
        $this->markTestSkipped("fitur ini ada static methodnya");
    }

    public function testShouldChangePasswordAuthenticatedUserAccount()
    {
        $this->markTestSkipped("fitur ini ada static methodnya");
    }

    public function testShouldGetListUser()
    {
        $user = User::factory()->count(4)->create();
        $this->userRepository->expects($this->once())->method('getListData')->willReturn($user);
        $result = $this->service->getListUser([]);
        $fractal = new Manager();
        $this->assertEquals($fractal->createData(new Collection($user, new UsersTransformer))->toArray(), $result);
    }

    public function testShouldGetUserById()
    {
        $user = User::factory()->make(['idPegawai' => 1]);
        $this->userRepository->expects($this->once())->method('getDataById')->willReturn($user);
        $result = $this->service->getUserById(1);
        $this->assertEquals($user->toArray(), $result);
        $this->assertEquals(1, $result['idPegawai']);
    }

    public function testShouldNewSingleUser()
    {
        $account = [
            'id' => 1,
            'email' => 'example@test.com',
            'username' => '2211001',
            'password' => 'rahasia'
        ];
        $user = User::factory()->make($account);
        unset($account['id']);
        $this->userRepository->expects($this->once())->method('save')->willReturn($user);
        $this->userRepository->expects($this->once())->method('registerUser')->willReturn($account);
        $input = [
            'namaDepan' => $user->namaDepan,
            'namaBelakang' => $user->namaBelakang,
            'jenisKelamin' => $user->jenisKelamin,
            'noHp' => $user->noHp,
            'email' => $user->email,
            'peran' => $user->peran,
            'alamat' => $user->alamat
        ];
        $result = $this->service->newUser($input);
        $this->assertEquals(['idPegawai' => 1], $result);
    }

    public function testShouldUpdateSingleUserByIdCaseStudyChangeRoleFromTeknisiToOtherValue()
    {
        $user = User::factory()->make(['id' => 1, 'peran' => 'pemilik']);
        $this->userRepository->expects($this->once())->method('save')->willReturn($user);
        $this->responbilityRepository->expects($this->once())->method('deleteByUsername');
        $input = [
            'namaDepan' => $user->namaDepan,
            'namaBelakang' => $user->namaBelakang,
            'jenisKelamin' => $user->jenisKelamin,
            'noHp' => $user->noHp,
            'email' => $user->email,
            'peran' => $user->peran,
            'alamat' => $user->alamat
        ];
        $result = $this->service->updateUserById($input, 1);
        $this->assertEquals(['idPegawai' => 1], $result);
    }

    public function testShouldUpdateSingleUserByIdCaseStudyRolealwaysTeknisi()
    {
        $user = User::factory()->make(['id' => 1, 'peran' => 'teknisi']);
        $this->userRepository->expects($this->once())->method('save')->willReturn($user);
        $this->responbilityRepository->expects($this->never())->method('deleteByUsername');
        $input = [
            'namaDepan' => $user->namaDepan,
            'namaBelakang' => $user->namaBelakang,
            'jenisKelamin' => $user->jenisKelamin,
            'noHp' => $user->noHp,
            'email' => $user->email,
            'peran' => $user->peran,
            'alamat' => $user->alamat
        ];
        $result = $this->service->updateUserById($input, 1);
        $this->assertEquals(['idPegawai' => 1], $result);
    }

    public function testShouldDeleteSingleUserByIdCaseDeleteMethodInUserRepoReturnTrue()
    {
        $user = User::factory()->make(['id' => 1]);
        $this->userRepository->expects($this->once())->method('findById')->willReturn($user);
        $this->userRepository->expects($this->once())->method('delete')->willReturn(true);
        $this->responbilityRepository->expects($this->once())->method('deleteByUsername');
        $result =  $this->service->deleteUserById(1);
        $this->assertEquals('sukses hapus data pegawai', $result);
    }

    public function testShouldDeleteSingleUserByIdCaseDeleteMethodInUserRepoReturnfalse()
    {
        $user = User::factory()->make(['id' => 1]);
        $this->userRepository->expects($this->once())->method('findById')->willReturn($user);
        $this->userRepository->expects($this->once())->method('delete')->willReturn(false);
        $this->responbilityRepository->expects($this->never())->method('deleteByUsername');
        $result =  $this->service->deleteUserById(1);
        $this->assertEquals('sukses hapus data pegawai', $result);
    }
}