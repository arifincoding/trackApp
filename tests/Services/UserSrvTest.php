<?php

use App\Models\User;
use App\Repositories\ResponbilityRepository;
use App\Repositories\UserRepository;
use App\Services\UserService;
use App\Transformers\UsersTransformer;
use App\Validations\UserValidation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Testing\DatabaseMigrations;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class UserSrvTest extends TestCase
{
    use DatabaseMigrations;

    private UserRepository $userRepository;
    private ResponbilityRepository $responbilityRepository;
    private UserService $service;
    private UserValidation $validator;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->responbilityRepository = $this->createMock(ResponbilityRepository::class);
        $this->validator = $this->createMock(UserValidation::class);
        $this->service = new UserService($this->userRepository, $this->responbilityRepository, $this->validator);
    }

    public function testUserShouldLoginToTheApp()
    {
        User::factory()->create(['username' => '2211001', 'password' => Hash::make('rahasia')]);
        $this->validator->expects($this->once())->method('login');
        $this->validator->expects($this->once())->method('validate')->willReturn(true);
        $result = $this->service->login(['username' => '2211001', 'password' => 'rahasia']);
        $this->assertEquals(true, $result['success']);
        $this->assertNotEquals(null, $result['token']);
        $this->assertNotEquals(null, Auth::user());
        Auth::logout();
    }

    public function testShoudCreateRefreshToken()
    {
        $this->markTestSkipped("fitur ini bingung ngetestnya");
        Auth::attempt(['username' => '2211001', 'password' => 'rahasia']);
        Auth::logout();
    }

    public function testShouldLogOutUserAuthenticad()
    {
        User::factory()->create(['username' => '2211001', 'password' => Hash::make('rahasia')]);
        Auth::attempt(['username' => '2211001', 'password' => 'rahasia']);
        $this->assertNotEquals(null, Auth::user());
        $result = $this->service->logout();
        $this->assertEquals(null, Auth::user());
        $this->assertEquals('sukses logout', $result);
    }

    public function testShouldGetUserAuthenticatedAccountInfo()
    {
        $user = User::factory()->create(['username' => '2211001', 'password' => Hash::make('rahasia')]);
        Auth::attempt(['username' => '2211001', 'password' => 'rahasia']);
        $this->userRepository->expects($this->once())->method('findByUsername')->willReturn($user);
        $result = $this->service->getMyAccount('2211001');
        $this->assertEquals($user->toArray(), $result);
        Auth::logout();
    }

    public function testShoulUpdateAuthenticatedUserAccount()
    {
        $user = User::factory()->create(['username' => '2211001', 'password' => Hash::make('rahasia')]);
        Auth::attempt(['username' => '2211001', 'password' => 'rahasia']);
        $this->userRepository->expects($this->once())->method('findByUsername')->willReturn($user);
        $this->validator->expects($this->once())->method('update');
        $this->validator->expects($this->once())->method('validate');
        $this->userRepository->expects($this->once())->method('save')->willReturn($user);
        $input = [
            'noHp' => $user->noHp,
            'email' => $user->email,
            'alamat' => $user->alamat
        ];
        $result = $this->service->updateMyAccount($input);
        $this->assertEquals('sukses update akun', $result);
        Auth::logout();
    }

    public function testShouldChangePasswordAuthenticatedUserAccount()
    {
        User::factory()->create(['username' => '2211001', 'password' => Hash::make('rahasia')]);
        Auth::attempt(['username' => '2211001', 'password' => 'rahasia']);
        $this->validator->expects($this->once())->method('changePassword');
        $this->validator->expects($this->once())->method('validate')->willReturn(true);
        $this->userRepository->expects($this->once())->method('changePassword')->willReturn(true);
        $result = $this->service->changePassword(['sandiLama' => 'public', 'sandiBaru' => 'rahasia']);
        $this->assertEquals('sukses merubah sandi akun', $result);
        Auth::logout();
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