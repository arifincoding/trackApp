<?php

namespace App\Services;

use App\Services\Contracts\HistoryServiceContract;
use App\Validations\HistoryValidation;
use App\Repositories\HistoryRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

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
        Log::info("User is trying to create a single history data by id service", ["id service" => $idService, 'data' => $inputs]);
        $this->validator->validate($inputs, 'create');
        $inputs += [
            'idService' => $idService,
            'waktu' => Carbon::now('GMT+7')
        ];
        $data = $this->historyRepository->save($inputs);
        Log::info("User create a single history data successfully", ["id history" => $data->id]);
        return [
            'idRiwayat' => $data->id
        ];
    }
}