<?php

use App\Models\History;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Laravel\Lumen\Testing\DatabaseTransactions;

class HistoryRepoTest extends TestCase
{

    use DatabaseTransactions;

    private $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make('App\Repositories\HistoryRepository');
    }

    public function testShouldDeleteListHistoryByIdService()
    {
        $service = Service::factory()->count(2)->create();
        History::factory()->count(6)->state(new Sequence(['service_id' => $service[0]->id], ['service_id' => $service[1]->id]))->create();
        $result = $this->repository->deleteByIdService($service[1]->id);
        $this->assertEquals(true, $result);
        $this->assertEquals(0, History::where('service_id', $service[1]->id)->count());
        $this->assertEquals(3, History::where('service_id', $service[0]->id)->count());
    }

    public function testDeleteListHistoryByIdServiceShouldReturnFalse()
    {
        $service = Service::factory()->count(2)->create();
        History::factory()->count(3)->create([
            'service_id' => $service[1]->id
        ]);
        $result = $this->repository->deleteByIdService($service[0]->id);
        $this->assertEquals(false, $result);
    }
}
