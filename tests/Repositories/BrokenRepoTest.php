<?php

use App\Models\Broken;
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
        Broken::factory()->create([
            'idService' => 2
        ]);
        Broken::factory()->count(2)->create([
            'idService' => 1
        ]);
        Broken::factory()->count(2)->create([
            'disetujui' => true,
            'idService' => 1
        ]);
        Broken::factory()->count(2)->create([
            'disetujui' => false,
            'idService' => 1
        ]);
        Broken::factory()->create([
            'idService' => 2
        ]);
        $broken = Broken::where('idService', 1)->get();
        $result = $this->repository->getListDataByIdService(1);
        $this->assertEquals($broken->toArray(), $result->toArray());
    }

    public function testShouldGetBrokenById()
    {
        Broken::factory()->create([
            'biaya' => null
        ]);
        Broken::factory()->create([
            'disetujui' => true,
            'biaya' => '50000'
        ]);
        Broken::factory()->create([
            'disetujui' => false
        ]);
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
        Broken::factory()->create(['idService' => 1]);
        $broken = Broken::factory()->count(3)->create(['idService' => 1, 'biaya' => null]);
        Broken::factory()->count(2)->create(['idService' => 1]);
        $result = $this->repository->findOneDataByWhere(['idService' => 1, 'biaya' => null]);
        $this->assertEquals($broken[0]->toArray(), $result->toArray());
    }

    public function testShouldSetCostInNotAgreeToZero()
    {
        Broken::factory()->count(2)->create(['idService' => 1, 'disetujui' => false]);
        Broken::factory()->count(2)->create(['idService' => 2, 'disetujui' => true]);
        Broken::factory()->count(2)->create(['idService' => 2]);
        $broken = Broken::factory()->count(2)->create(['idService' => 2, 'disetujui' => false]);
        Broken::factory()->count(2)->create(['idService' => 3, 'disetujui' => false]);
        $result = $this->repository->setCostInNotAgreeToZero(2);
        $brokenArr = $broken->toArray();
        foreach ($broken as $key => $item) {
            $brokenArr[$key]['biaya'] = 0;
        }
        $this->assertEquals(true, $result);
        $resultArr = Broken::where('biaya', 0)->get();
        $this->assertEquals($brokenArr, $resultArr->toArray());
    }

    public function testShouldDeleteListBrokenByIdService()
    {
        Broken::factory()->count(3)->create(['idService' => 1]);
        Broken::factory()->count(3)->create(['idService' => 2]);
        Broken::factory()->count(3)->create(['idService' => 3]);

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