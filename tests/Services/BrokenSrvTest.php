<?php

use App\Repositories\BrokenRepository;
use App\Repositories\ServiceRepository;
use App\Services\BrokenService;
use App\Validations\BrokenValidation;
use Laravel\Lumen\Testing\DatabaseMigrations;
use App\Models\Broken;
use App\Models\Service;
use App\Transformers\BrokensTransformer;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class BrokenSrvTest extends TestCase
{

    use DatabaseMigrations;

    private BrokenRepository $brokenRepository;
    private ServiceRepository $serviceRepository;
    private BrokenValidation $validator;
    private BrokenService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->brokenRepository = $this->createMock(BrokenRepository::class);
        $this->serviceRepository = $this->createMock(ServiceRepository::class);
        $this->validator = $this->createMock(BrokenValidation::class);
        $this->service = new BrokenService($this->brokenRepository, $this->serviceRepository, $this->validator);
    }

    public function testShouldGetListBrokenByIdService()
    {
        $broken = Broken::factory()->count(3)->create(['idService' => 1]);
        $this->brokenRepository->expects($this->once())->method('getListDataByIdService')->willReturn($broken);
        $fractal = new Manager();
        $brokenFormated = $fractal->createData(new Collection($broken, new BrokensTransformer))->toArray();
        $result = $this->service->getListBrokenByIdService(1);
        $this->assertEquals($brokenFormated, $result);
    }

    public function testShouldNewSingleBrokenDataByIdService()
    {
        $service = Service::factory()->make(['id' => 1]);
        $this->validator->expects($this->once())->method('validate')->willReturn(true);
        $this->serviceRepository->expects($this->once())->method('findById')->willReturn($service);
        $input = [
            'id' => 1,
            'judul' => 'test',
            'deskripsi' => 'ini adalah test'
        ];
        $broken = Broken::factory()->make($input);
        $this->brokenRepository->expects($this->once())->method('save')->willReturn($broken);
        unset($input['id']);
        $result = $this->service->newBrokenByIdService($input, 1);
        $this->assertEquals(['idKerusakan' => 1], $result);
    }

    public function testShouldGetSingleBrokenById()
    {
        $broken = Broken::factory()->make(['idKerusakan' => 1]);
        $this->brokenRepository->expects($this->once())->method('getDataById')->willReturn($broken);
        $result = $this->service->getBrokenById(1);
        $this->assertEquals($broken->toArray(), $result);
    }

    public function testShouldUpdateSingleBrokenById()
    {
        $input = [
            'id' => 1,
            'judul' => 'test',
            'deskripsi' => 'ini adalah deskripsi',
            'idService' => 24
        ];
        $broken = Broken::factory()->make($input);
        $this->validator->expects($this->once())->method('validate')->willReturn(true);
        $this->brokenRepository->expects($this->once())->method('save')->willReturn($broken);
        unset($input['id']);
        unset($input['idService']);
        $result = $this->service->updateBroken($input, 1);
        $this->assertEquals(['idKerusakan' => 1, 'idService' => 24], $result);
    }

    public function testShouldUpdateCostInSingleBrokenDataById()
    {
        $input = ['biaya' => 25000, 'id' => 1];
        $broken = Broken::factory()->make($input);
        unset($input['id']);
        $this->validator->expects($this->once())->method('cost');
        $this->validator->expects($this->once())->method('validate')->willReturn(true);
        $this->brokenRepository->expects($this->once())->method('save')->willReturn($broken);
        $result = $this->service->updateBrokenCost($input, 1);
        $this->assertEquals(['idKerusakan' => 1], $result);
    }

    public function testShouldUpdateConfirmationInSingleBrokenDataById()
    {
        $input = ['disetujui' => true, 'id' => 1];
        $broken = Broken::factory()->make($input);
        unset($input['id']);
        $this->validator->expects($this->once())->method('confirm');
        $this->validator->expects($this->once())->method('validate');
        $this->brokenRepository->expects($this->once())->method('save')->willReturn($broken);
        $result = $this->service->updateBrokenConfirmation($input, 1);
        $this->assertEquals(['idKerusakan' => 1], $result);
    }

    public function testShouldDeleteSingleBrokenById()
    {
        $this->brokenRepository->expects($this->once())->method('delete')->willReturn(true);
        $result = $this->service->deleteBrokenById(1);
        $this->assertEquals('sukses hapus data kerusakan', $result);
    }
}