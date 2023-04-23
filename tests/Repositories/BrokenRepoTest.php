<?php

use App\Models\Broken;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Laravel\Lumen\Testing\DatabaseTransactions;

class BrokenRepoTest extends TestCase
{

    use DatabaseTransactions;

    private $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make('App\Repositories\BrokenRepository');
    }

    public function testShoudGetListBrokenByIdService()
    {
        $serviceFactory = Service::factory()->count(3)->create();
        $brokenFactory = Broken::factory()->count(6)->state(new Sequence(
            ['service_id' => $serviceFactory[0]->id],
            ['service_id' => $serviceFactory[1]->id],
            ['service_id' => $serviceFactory[2]->id]
        ))->create();
        $result = $this->repository->getListDataByIdService($serviceFactory[1]->id);
        $broken = Broken::whereIn('id', [$brokenFactory[1]->id, $brokenFactory[4]->id])->get();
        $this->assertEquals($broken->toArray(), $result->toArray());
    }

    public function testShouldGetBrokenById()
    {
        $brokenFactory = Broken::factory()->count(3)->sequence(
            ['is_approved' => null, 'cost' => 5000],
            ['is_approved' => true, 'cost' => 9000],
            ['is_approved' => false, 'cost' => 0]
        )->create();
        $result = $this->repository->getDataById($brokenFactory[1]->id);
        $brokenFactory[1]->costString = 'Rp. 9.000';
        $brokenFactory[1]->is_approved = true;
        $this->assertEquals($brokenFactory[1]->toArray(), $result->toArray());
    }

    public function testShouldFindOneBrokenDataByWhere()
    {
        $serviceFactory = Service::factory()->create();
        $brokenFactory = Broken::factory()->count(3)->sequence(
            ['cost' => 1000],
            ['cost' => 0],
            ['cost' => 12000]
        )->create(['service_id' => $serviceFactory->id]);
        $result = $this->repository->findOneDataByWhere(['service_id' => $serviceFactory->id, 'cost' => 0]);
        $this->assertEquals($brokenFactory[1]->toArray(), $result->toArray());
    }

    public function testShouldSetCostInNotAgreeToZero()
    {
        $serviceFactory  = Service::factory()->count(2)->create();
        Broken::factory()->create(['service_id' => $serviceFactory[0]->id, 'is_approved' => false]);
        $brokenFactory = Broken::factory()->count(6)->state(new Sequence(
            ['is_approved' => null],
            ['is_approved' => false],
            ['is_approved' => true]
        ))->create(['service_id' => $serviceFactory[1]->id]);
        $result = $this->repository->setCostInNotAgreeToZero($serviceFactory[1]->id);
        $this->assertEquals(true, $result);
        $brokenCount = Broken::where('service_id', $serviceFactory[1]->id)->where('cost', 0)->count();
        $this->assertEquals(2, $brokenCount);
    }

    public function testShouldDeleteListBrokenByIdService()
    {
        $serviceFactory = Service::factory()->count(3)->create();
        Broken::factory()->count(6)->state(new Sequence(
            ['service_id' => $serviceFactory[0]->id],
            ['service_id' => $serviceFactory[1]->id],
            ['service_id' => $serviceFactory[2]->id]
        ))->create();
        $result = $this->repository->deleteByIdService($serviceFactory[1]->id);
        $this->assertEquals(true, $result);
        $this->assertEquals(0, Broken::where('service_id', $serviceFactory[1]->id)->count());
    }

    public function testDeleteListBrokenByIdServiceShouldReturnFalse()
    {
        $brokenFactory = Broken::factory()->create();
        Broken::where('id', $brokenFactory->id)->delete();
        $result = $this->repository->deleteByIdService($brokenFactory->service_id);
        $this->assertEquals(false, $result);
    }

    public function testShouldSumCostByServiceId()
    {
        $serviceFactory = Service::factory()->count(2)->create();
        Broken::factory()->count(5)->sequence(
            ['cost' => 2000, 'service_id' => $serviceFactory[0]->id],
            ['cost' => 1000, 'service_id' => $serviceFactory[1]->id],
            ['cost' => 5000, 'service_id' => $serviceFactory[1]->id],
            ['cost' => 3000, 'service_id' => $serviceFactory[0]->id],
            ['cost' => 8000, 'service_id' => $serviceFactory[1]->id]
        )->create();
        $result = $this->repository->sumCostByServiceId($serviceFactory[1]->id);
        $this->assertEquals(14000, $result);
    }
}
