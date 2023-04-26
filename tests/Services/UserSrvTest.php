<?php

use App\Models\User;
use App\Repositories\ResponbilityRepository;
use App\Repositories\UserRepository;
use App\Services\UserService;
use App\Transformers\UsersTransformer;
use App\Validations\UserValidation;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Testing\DatabaseTransactions;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class UserSrvTest extends TestCase
{
    use DatabaseTransactions;

    private $userRepository;
    private $responbilityRepository;
    private UserService $service;
    private $validator;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->responbilityRepository = $this->createMock(ResponbilityRepository::class);
        $this->validator = $this->createMock(UserValidation::class);
        $this->service = new UserService($this->userRepository, $this->responbilityRepository, $this->validator);
    }

    public static function authenticated(array $input)
    {
        $user = User::factory()->create(['username' => $input['username'], 'password' => Hash::make($input['password'])]);
        Auth::attempt($input);
        return $user;
    }

    public function testUserShouldLoginToTheApp()
    {
        $input = ['username' => '2211001', 'password' => 'rahasia'];
        User::factory()->create(['username' => $input['username'], 'password' => Hash::make($input['password'])]);
        $this->validator->expects($this->once())->method('login');
        $this->validator->expects($this->once())->method('validate')->willReturn(true);
        $result = $this->service->login($input);
        $this->assertEquals(true, $result['success']);
        $this->assertNotEquals(null, $result['token']);
        $this->assertNotEquals(null, Auth::user());
        Auth::logout();
    }

    public function testShoudCreateRefreshToken()
    {
        $this->markTestIncomplete("fitur ini bingung ngetestnya");
        Auth::attempt(['username' => '2211001', 'password' => 'rahasia']);
        Auth::logout();
    }

    public function testShouldLogOutUserAuthenticad()
    {
        $input = ['username' => '2211001', 'password' => 'rahasia'];
        self::authenticated($input);
        $this->assertNotEquals(null, Auth::user());
        $result = $this->service->logout();
        $this->assertEquals(null, Auth::user());
        $this->assertEquals('sukses logout', $result);
    }

    public function testShouldGetUserAuthenticatedAccountInfo()
    {
        $input = ['username' => '2211001', 'password' => 'rahasia'];
        $user = self::authenticated($input);
        $this->userRepository->expects($this->once())->method('findByUsername')->willReturn($user);
        $result = $this->service->getMyAccount($user['username']);
        $this->assertEquals($user->toArray(), $result);
        Auth::logout();
    }

    public function testShoulUpdateAuthenticatedUserAccount()
    {
        $credential = ['username' => '2211001', 'password' => 'rahasia'];
        $user = self::authenticated($credential);
        $this->userRepository->expects($this->once())->method('findByUsername')->willReturn($user);
        $this->validator->expects($this->once())->method('update');
        $this->validator->expects($this->once())->method('validate');
        $this->userRepository->expects($this->once())->method('save')->willReturn($user);
        $input = [
            'telp' => $user->telp,
            'email' => $user->email,
            'address' => $user->address
        ];
        $result = $this->service->updateMyAccount($input);
        $this->assertEquals('sukses update akun', $result);
        Auth::logout();
    }

    public function testShouldChangePasswordAuthenticatedUserAccount()
    {
        $credential = ['username' => '2211001', 'password' => 'rahasia'];
        self::authenticated($credential);
        $this->validator->expects($this->once())->method('changePassword');
        $this->validator->expects($this->once())->method('validate')->willReturn(true);
        $this->userRepository->expects($this->once())->method('changePassword')->willReturn(true);
        $input = ['old_password' => 'public', 'new_password' => 'rahasia'];
        $result = $this->service->changePassword($input);
        $this->assertEquals('sukses merubah sandi akun', $result);
        Auth::logout();
    }

    public function testShouldGetListUser()
    {
        $user = User::factory()->count(4)->sequence(fn (Sequence $sequence) => ['id' => $sequence->index + 1])->make();
        $this->userRepository->expects($this->once())->method('getListData')->willReturn($user);
        $result = $this->service->getListUser([]);
        $fractal = new Manager();
        $this->assertEquals($fractal->createData(new Collection($user, new UsersTransformer))->toArray(), $result);
    }

    public function testShouldGetUserById()
    {
        $user = User::factory()->make(['id' => 1]);
        $this->userRepository->expects($this->once())->method('getDataById')->willReturn($user);
        $result = $this->service->getUserById(1);
        $this->assertEquals($user->toArray(), $result);
    }

    public function testShouldNewSingleUser()
    {
        $account = [
            'email' => 'example@test.com',
            'username' => '2211001',
            'password' => 'rahasia'
        ];
        $user = User::factory()->sequence($account)->make(['id' => 1]);
        $this->userRepository->expects($this->once())->method('save')->willReturn($user);
        $this->userRepository->expects($this->once())->method('registerUser')->willReturn($account);
        $input = [
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'gender' => $user->gender,
            'telp' => $user->telp,
            'email' => $user->email,
            'role' => $user->role,
            'address' => $user->address
        ];
        $result = $this->service->newUser($input);
        $this->assertEquals(['user_id' => $user->id], $result);
    }

    public function testShouldUpdateSingleUserByIdCaseStudyChangeRoleFromTeknisiToOtherValue()
    {
        $user = User::factory()->make(['id' => 1, 'role' => 'pemilik']);
        $this->userRepository->expects($this->once())->method('save')->willReturn($user);
        $this->responbilityRepository->expects($this->once())->method('deleteByUsername');
        $input = [
            'fistname' => $user->firstname,
            'lastname' => $user->lastname,
            'gender' => $user->gender,
            'telp' => $user->telp,
            'email' => $user->email,
            'role' => $user->role,
            'address' => $user->address
        ];
        $result = $this->service->updateUserById($input, $user->id);
        $this->assertEquals(['user_id' => $user->id], $result);
    }

    public function testShouldUpdateSingleUserByIdCaseStudyRolealwaysTeknisi()
    {
        $user = User::factory()->make(['id' => 1, 'role' => 'teknisi']);
        $this->userRepository->expects($this->once())->method('save')->willReturn($user);
        $this->responbilityRepository->expects($this->never())->method('deleteByUsername');
        $input = [
            'fistname' => $user->fistname,
            'lastname' => $user->lastname,
            'gender' => $user->gender,
            'telp' => $user->telp,
            'email' => $user->email,
            'role' => $user->role,
            'alamat' => $user->address
        ];
        $result = $this->service->updateUserById($input, 1);
        $this->assertEquals(['user_id' => 1], $result);
    }

    public function testShouldDeleteSingleUserById()
    {
        $user = User::factory()->make(['id' => 1]);
        $this->userRepository->expects($this->once())->method('findById')->willReturn($user);
        $this->userRepository->expects($this->once())->method('delete')->willReturn(true);
        $this->responbilityRepository->expects($this->once())->method('deleteByUsername');
        $result =  $this->service->deleteUserById(1);
        $this->assertEquals('sukses hapus data pegawai', $result);
    }
}
