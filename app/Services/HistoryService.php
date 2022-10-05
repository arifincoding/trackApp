<?php

namespace App\Services;

use App\Services\Contracts\HistoryServiceContract;
use App\Validations\HistoryValidation;
use App\Repositories\HistoryRepository;
use Illuminate\Support\Carbon;

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
        $inputs += [
            'idService' => $id,
            'waktu' => Carbon::now('GMT+7')
        ];
        $data = $this->historyRepository->save($inputs);
        return [
            'idRiwayat' => $data->id
        ];
    }
}