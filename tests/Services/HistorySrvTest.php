<?php

use App\Models\History;
use App\Repositories\HistoryRepository;
use App\Services\HistoryService;
use App\Validations\HistoryValidation;
use Laravel\Lumen\Testing\DatabaseTransactions;

class HistorySrvTest extends TestCase
{

    use DatabaseTransactions;

    private $repository;
    private $validator;
    private HistoryService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(HistoryRepository::class);
        $this->validator = $this->createMock(HistoryValidation::class);
        $this->service = new HistoryService($this->repository, $this->validator);
    }

    public function testShouldNewSingleHistory()
    {
        $input = ['status' => 'antri', 'message' => 'test antri'];
        $history = History::factory()->sequence($input)->make(['id' => 1, 'service_id' => 2]);
        $this->validator->expects($this->once())->method('validate')->willReturn(true);
        $this->repository->expects($this->once())->method('save')->willReturn($history);
        $result = $this->service->newHistory($input, 2);
        $this->assertEquals(['history_id' => 1], $result);
    }
}
