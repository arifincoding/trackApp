<?php

namespace App\Services;

use App\Services\Contracts\HistoryServiceContract;
use App\Validations\HistoryValidation;
use App\Repositories\HistoryRepository;

class HistoryService implements HistoryServiceContract
{
    private HistoryRepository $historyRepository;
    private HistoryValidation $validator;

    public function __construct(HistoryRepository $history, HistoryValidation $validator)
    {
        $this->historyRepository = $history;
        $this->validator = $validator;
    }

    public function newHistory(array $inputs, int $idService): array
    {
        $this->validator->validate($inputs, 'create');
        $inputs['service_id'] = $idService;
        $data = $this->historyRepository->save($inputs);
        return [
            'history_id' => $data->id
        ];
    }
}
