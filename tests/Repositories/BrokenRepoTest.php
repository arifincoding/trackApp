<?php

use App\Models\Broken;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Laravel\Lumen\Testing\DatabaseMigrations;

class BrokenRepoTest extends TestCase
{

    use DatabaseMigrations;

    private $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make('App\Repositories\BrokenRepository');
    }

    public function testShoudGetListBrokenByIdService()
    {
        $data = [
            'disetujui' => [true, null, null, true, true, false, false, false, null],
            'idService' => [2, 1, 1, 1, 1, 1, 1, 2, 2]
        ];
        Broken::factory()->count(9)->sequence(function ($sequence) use ($data) {
            return [
                'disetujui' => $data['disetujui'][$sequence->index],
                'idService' => $data['idService'][$sequence->index]
            ];
        })->create();
        $broken = Broken::whereIn('id', [2, 3, 4, 5, 6, 7])->get();
        $result = $this->repository->getListDataByIdService(1);
        $this->assertEquals($broken->toArray(), $result->toArray());
    }

    public function testShouldGetBrokenById()
    {
        $data = [
            'disetujui' => [null, true, false],
            'biaya' => [null, '50000', '100000']
        ];
        Broken::factory()->count(3)->sequence(function ($sequence) use ($data) {
            return [
                'disetujui' => $data['disetujui'][$sequence->index],
                'biaya' => $data['biaya'][$sequence->index]
            ];
        })->create();
        $attributs = ['id as idKerusakan', 'idService', 'judul', 'deskripsi', 'biaya', 'disetujui'];
        $broken = Broken::select($attributs)->where('id', 2)->first();
        $brokenArr = $broken->toArray();
        $brokenArr += [
            'disetujui' => true,
            'biayaString' => 'Rp. 50.000'
        ];
        $result = $this->repository->getDataById(2);
        $this->assertEquals($brokenArr, $result->toArray());
    }

    public function testShouldFindOneBrokenDataByWhere()
    {
        $biaya = ['1000', null, null, null, '2000', '3000'];
        $broken = Broken::factory()->count(6)->sequence(function ($sequence) use ($biaya) {
            return ['biaya' => $biaya[$sequence->index]];
        })->create(['idService' => 1]);
        $result = $this->repository->findOneDataByWhere(['idService' => 1, 'biaya' => null]);
        $this->assertEquals($broken[1]->toArray(), $result->toArray());
    }

    public function testShouldSetCostInNotAgreeToZero()
    {
        $data = [
            'idService' => [1, 1, 2, 2, 2, 2, 2, 2, 3, 3],
            'disetujui' => [false, false, true, true, null, null, false, false, false, false]
        ];
        $broken = Broken::factory()->count(10)->sequence(function ($sequence) use ($data) {
            return [
                'idService' => $data['idService'][$sequence->index],
                'disetujui' => $data['disetujui'][$sequence->index]
            ];
        })->create();
        $brokenArr = [$broken[6]->toArray(), $broken[7]->toArray()];
        $brokenArr[0]['biaya'] = 0;
        $brokenArr[1]['biaya'] = 0;
        $result = $this->repository->setCostInNotAgreeToZero(2);
        $this->assertEquals(true, $result);
        $resultArr = Broken::where('biaya', 0)->get();
        $this->assertEquals($brokenArr, $resultArr->toArray());
    }

    public function testShouldDeleteListBrokenByIdService()
    {
        $id = [1, 1, 1, 2, 2, 2, 3, 3, 3];
        Broken::factory()->count(9)->sequence(function ($sequence) use ($id) {
            return ['idService' => $id[$sequence->index]];
        })->create();
        $result = $this->repository->deleteByIdService(2);
        $this->assertEquals(true, $result);
        $this->assertEquals(null, Broken::where('idService', 2)->first());
    }

    public function testDeleteListBrokenByIdServiceShouldReturnFalse()
    {
        Broken::factory()->count(3)->create(['idService' => 1]);
        $result = $this->repository->deleteByIdService(2);
        $this->assertEquals(false, $result);
    }
}