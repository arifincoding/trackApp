<?php

use App\Models\History;
use Laravel\Lumen\Testing\DatabaseMigrations;

class HistoryRepoTest extends TestCase
{

    use DatabaseMigrations;

    private $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make('App\Repositories\HistoryRepository');
    }

    public function testShouldDeleteListHistoryByIdService()
    {
        History::factory()->count(2)->create([
            'idService' => 1
        ]);
        History::factory()->count(3)->create([
            'idService' => 2
        ]);
        History::factory()->create([
            'idService' => 3
        ]);
        $result = $this->repository->deleteByIdService(2);
        $this->assertEquals(true, $result);
    }

    public function testDeleteListHistoryByIdServiceShouldReturnFalse()
    {
        History::factory()->count(3)->create([
            'idService' => 1
        ]);
        $result = $this->repository->deleteByIdService(2);
        $this->assertEquals(false, $result);
    }
}