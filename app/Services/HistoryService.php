<?php

namespace App\Services;

use App\Services\Contracts\HistoryServiceContract;
use App\Validations\HistoryValidation;
use App\Repositories\HistoryRepository;

class HistoryService implements HistoryServiceContract
{
    private $historyRepository;
    private $historyValidator;

    public function __construct(HistoryRepository $history, HistoryValidation $validator)
    {
        $this->historyRepository = $history;
        $this->historyValidator = $validator;
    }

    public function newHistory(array $inputs, int $id): array
    {
        $this->historyValidator->validate($inputs);
        $data = $this->historyRepository->create($inputs,$id);
        return $data;
    }
}