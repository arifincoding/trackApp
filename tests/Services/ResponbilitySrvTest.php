<?php

use App\Models\Category;
use App\Models\Responbility;
use App\Models\User;
use App\Repositories\ResponbilityRepository;
use App\Repositories\UserRepository;
use App\Services\ResponbilityService;
use App\Transformers\ResponbilitiesTransformer;
use App\Validations\ResponbilityValidation;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Laravel\Lumen\Testing\DatabaseTransactions;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class ResponbilitySrvTest extends TestCase
{
    use DatabaseTransactions;

    private $responbilityRepository;
    private $userRepository;
    private $validator;
    private ResponbilityService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->responbilityRepository = $this->createMock(ResponbilityRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->validator = $this->createMock(ResponbilityValidation::class);
        $this->service = new ResponbilityService($this->responbilityRepository, $this->userRepository, $this->validator);
    }

    public function testShouldGetAllUserResponbilities()
    {
        $username = '2211001';
        Responbility::factory()->count(3)->create(['username' => $username]);
        $responbilities = Responbility::with('category')->where('username', $username)->orderByDesc('id')->get();
        $this->responbilityRepository->expects($this->once())->method('getListDataByUsername')->willReturn($responbilities);
        $fractal = new Manager();
        $responbilitiesFormatted = $fractal->createData(new Collection($responbilities, new ResponbilitiesTransformer))->toArray();
        $result = $this->service->getAllRespobilities($username);
        $this->assertEquals($responbilitiesFormatted, $result);
        $this->assertEquals($responbilitiesFormatted[2]['id'], $result[2]['id']);
    }

    public function testShouldNewManyUserResponbilities()
    {
        $user = User::factory()->make(['id' => 1, 'role' => 'teknisi']);
        $input = [
            'category_id' => [1, 2, 3, 4, 5]
        ];
        $this->validator->expects($this->once())->method('post');
        $this->validator->expects($this->once())->method('validate')->willReturn(true);
        $this->userRepository->expects($this->once())->method('findById')->willReturn($user);
        $this->responbilityRepository->expects($this->once())->method('create')->willReturn(true);
        $result = $this->service->newResponbilities($input, 1);
        $this->assertEquals([
            'success' => true,
            'message' => 'sukses tambah tanggung jawab'
        ], $result);
    }

    public function testShouldDeleteSingleResponbilityById()
    {
        $this->responbilityRepository->expects($this->once())->method('delete')->willReturn(true);
        $result = $this->service->deleteResponbilityById(1);
        $this->assertEquals('sukses hapus data tanggung jawab', $result);
    }
}
