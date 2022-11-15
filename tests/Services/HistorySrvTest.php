<?php

use App\Models\History;
use App\Repositories\HistoryRepository;
use App\Services\HistoryService;
use App\Validations\HistoryValidation;
use Laravel\Lumen\Testing\DatabaseMigrations;

class HistorySrvTest extends TestCase
{

    use DatabaseMigrations;

    private HistoryRepository $repository;
    private HistoryValidation $validator;
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
        $input = [
            'id' => 1,
            'status' => 'antri',
            'pesan' => 'test antri',
            'idService' => 34
        ];
        $history = History::factory()->make($input);
        $this->validator->expects($this->once())->method('validate')->willReturn(true);
        $this->repository->expects($this->once())->method('save')->willReturn($history);
        unset($input['id']);
        unset($input['idService']);
        $result = $this->service->newHistory($input, 34);
        $this->assertEquals(['idRiwayat' => 1], $result);
    }
}