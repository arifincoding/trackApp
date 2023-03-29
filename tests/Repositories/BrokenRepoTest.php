<?php

use App\Models\Broken;
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
        $data = [
            'is_approved' => [true, null, null, true, true, false, false, false, null],
            'service_id' => [2, 1, 1, 1, 1, 1, 1, 2, 2]
        ];
        Broken::factory()->count(9)->sequence(function ($sequence) use ($data) {
            return [
                'is_approved' => $data['is_approved'][$sequence->index],
                'service_id' => $data['service_id'][$sequence->index]
            ];
        })->create();
        $broken = Broken::whereIn('id', [2, 3, 4, 5, 6, 7])->get();
        $result = $this->repository->getListDataByIdService(1);
        $this->assertEquals($broken->toArray(), $result->toArray());
    }

    public function testShouldGetBrokenById()
    {
        $data = [
            'is_approved' => [null, true, false],
            'cost' => [null, '50000', '100000']
        ];
        Broken::factory()->count(3)->sequence(function ($sequence) use ($data) {
            return [
                'is_approved' => $data['is_approved'][$sequence->index],
                'cost' => $data['cost'][$sequence->index]
            ];
        })->create();
        $attributs = ['id as broken_id', 'service_id', 'title', 'description', 'cost', 'is_approved'];
        $broken = Broken::select($attributs)->where('id', 2)->first();
        $brokenArr = $broken->toArray();
        $brokenArr += [
            'is_approved' => true,
            'costString' => 'Rp. 50.000'
        ];
        $result = $this->repository->getDataById(2);
        $this->assertEquals($brokenArr, $result->toArray());
    }

    public function testShouldFindOneBrokenDataByWhere()
    {
        $cost = ['1000', null, null, null, '2000', '3000'];
        $broken = Broken::factory()->count(6)->sequence(function ($sequence) use ($cost) {
            return ['cost' => $cost[$sequence->index]];
        })->create(['service_id' => 1]);
        $result = $this->repository->findOneDataByWhere(['service_id' => 1, 'cost' => null]);
        $this->assertEquals($broken[1]->toArray(), $result->toArray());
    }

    public function testShouldSetCostInNotAgreeToZero()
    {
        $data = [
            'service_id' => [1, 1, 2, 2, 2, 2, 2, 2, 3, 3],
            'is_approved' => [false, false, true, true, null, null, false, false, false, false]
        ];
        $broken = Broken::factory()->count(10)->sequence(function ($sequence) use ($data) {
            return [
                'service_id' => $data['service_id'][$sequence->index],
                'is_approved' => $data['is_approved'][$sequence->index]
            ];
        })->create();
        $brokenArr = [$broken[6]->toArray(), $broken[7]->toArray()];
        $brokenArr[0]['cost'] = 0;
        $brokenArr[1]['cost'] = 0;
        $result = $this->repository->setCostInNotAgreeToZero(2);
        $this->assertEquals(true, $result);
        $resultArr = Broken::where('cost', 0)->get();
        $this->assertEquals($brokenArr, $resultArr->toArray());
    }

    public function testShouldDeleteListBrokenByIdService()
    {
        $id = [1, 1, 1, 2, 2, 2, 3, 3, 3];
        Broken::factory()->count(9)->sequence(function ($sequence) use ($id) {
            return ['service_id' => $id[$sequence->index]];
        })->create();
        $result = $this->repository->deleteByIdService(2);
        $this->assertEquals(true, $result);
        $this->assertEquals(null, Broken::where('service_id', 2)->first());
    }

    public function testDeleteListBrokenByIdServiceShouldReturnFalse()
    {
        Broken::factory()->count(3)->create(['service_id' => 1]);
        $result = $this->repository->deleteByIdService(2);
        $this->assertEquals(false, $result);
    }
}
