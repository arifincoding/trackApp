<?php

use App\Models\Category;
use App\Models\Responbility;
use App\Models\User;
use App\Repositories\ResponbilityRepository;
use App\Repositories\UserRepository;
use App\Services\ResponbilityService;
use App\Transformers\ResponbilitiesTransformer;
use App\Validations\ResponbilityValidation;
use Laravel\Lumen\Testing\DatabaseMigrations;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class ResponbilitySrvTest extends TestCase
{
    use DatabaseMigrations;

    private ResponbilityRepository $responbilityRepository;
    private UserRepository $userRepository;
    private ResponbilityValidation $validator;
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
        $category = Category::factory()->create();
        $responbilities = Responbility::factory()->count(3)->for($category, 'kategori')->create(['username' => '2211001']);
        $this->responbilityRepository->expects($this->once())->method('getListDataByUsername')->willReturn($responbilities);
        $fractal = new Manager();
        $responbilitiesFormatted = $fractal->createData(new Collection($responbilities, new ResponbilitiesTransformer))->toArray();
        $result = $this->service->getAllRespobilities('2211001');
        $this->assertEquals($responbilitiesFormatted, $result);
        $this->assertEquals($responbilitiesFormatted[2]['id'], $result[2]['id']);
    }

    public function testShouldNewManyUserResponbilities()
    {
        $user = User::factory()->make(['id' => 1, 'peran' => 'teknisi']);
        $input = [
            'idKategori' => [1, 2, 3, 4, 5]
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