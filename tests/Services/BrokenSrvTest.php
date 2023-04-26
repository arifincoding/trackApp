<?php

use App\Repositories\BrokenRepository;
use App\Repositories\ServiceRepository;
use App\Services\BrokenService;
use App\Validations\BrokenValidation;
use App\Models\Broken;
use App\Models\Service;
use App\Transformers\BrokensTransformer;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Laravel\Lumen\Testing\DatabaseTransactions;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class BrokenSrvTest extends TestCase
{

    use DatabaseTransactions;

    private $brokenRepository;
    private $serviceRepository;
    private $validator;
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
        $broken = Broken::factory()->count(3)->sequence(fn (Sequence $sequence) => ['id' => $sequence->index + 1])->make(['service_id' => 1]);
        $this->brokenRepository->expects($this->once())->method('getListDataByIdService')->willReturn($broken);
        $fractal = new Manager();
        $brokenFormated = $fractal->createData(new Collection($broken, new BrokensTransformer))->toArray();
        $result = $this->service->getListBrokenByIdService(1);
        $this->assertEquals($brokenFormated, $result);
    }

    public function testShouldNewSingleBrokenDataByIdService()
    {

        $service = Service::factory()->make(['id' => 2]);
        $this->validator->expects($this->once())->method('validate')->willReturn(true);
        $this->serviceRepository->expects($this->once())->method('findById')->willReturn($service);
        $input = ['title' => 'ini test', 'description' => 'ini test untuk tambah data kerusakan'];
        $broken = Broken::factory()->sequence($input)->make(['id' => 1, 'service_id' => $service->id]);
        $this->brokenRepository->expects($this->once())->method('save')->willReturn($broken);
        $result = $this->service->newBrokenByIdService($input, 2);
        $this->assertEquals(['broken_id' => 1], $result);
    }

    public function testShouldGetSingleBrokenById()
    {
        $broken = Broken::factory()->make(['id' => 1, 'service_id' => 2]);
        $this->brokenRepository->expects($this->once())->method('getDataById')->willReturn($broken);
        $result = $this->service->getBrokenById(1);
        $this->assertEquals($broken->toArray(), $result);
    }

    public function testShouldUpdateSingleBrokenById()
    {
        $input = ['title' => 'ini test', 'description' => 'test untuk update data kerusakan'];
        $broken = Broken::factory()->sequence($input)->make(['id' => 1, 'service_id' => 2]);
        $this->validator->expects($this->once())->method('validate')->willReturn(true);
        $this->brokenRepository->expects($this->once())->method('save')->willReturn($broken);
        $result = $this->service->updateBroken($input, 1);
        $this->assertEquals(['broken_id' => 1, 'service_id' => 2], $result);
    }

    public function testShouldUpdateCostInSingleBrokenDataById()
    {
        $input = ['cost' => 25000];
        $broken = Broken::factory()->sequence($input)->make(['id' => 1]);
        $this->validator->expects($this->once())->method('cost');
        $this->validator->expects($this->once())->method('validate')->willReturn(true);
        $this->brokenRepository->expects($this->once())->method('save')->willReturn($broken);
        $result = $this->service->updateBrokenCost($input, 1);
        $this->assertEquals(['broken_id' => 1], $result);
    }

    public function testShouldUpdateConfirmationInSingleBrokenDataById()
    {
        $input = ['is_approved' => true];
        $broken = Broken::factory()->sequence($input)->make(['id' => 1, 'service_id' => 2]);
        $this->validator->expects($this->once())->method('confirm');
        $this->validator->expects($this->once())->method('validate')->willReturn(true);
        $this->brokenRepository->expects($this->once())->method('save')->willReturn($broken);
        $result = $this->service->updateBrokenConfirmation($input, 1);
        $this->assertEquals(['broken_id' => 1], $result);
    }

    public function testShouldDeleteSingleBrokenById()
    {
        $this->brokenRepository->expects($this->once())->method('delete')->willReturn(true);
        $result = $this->service->deleteBrokenById(1);
        $this->assertEquals('sukses hapus data kerusakan', $result);
    }
}
