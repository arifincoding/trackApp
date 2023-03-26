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
        $id = [1, 1, 2, 2, 2, 3];
        History::factory()->count(6)->sequence(function ($sequence) use ($id) {
            return  ['idService' => $id[$sequence->index]];
        })->create();
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