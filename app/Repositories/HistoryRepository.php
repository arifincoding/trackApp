<?php

namespace App\Repositories;

use App\Repositories\Repository;
use App\Models\History;
use Illuminate\Support\Carbon;
use App\Repositories\Contracts\HistoryRepoContract;

class HistoryRepository extends Repository implements HistoryRepoContract
{

    function __construct(History $model)
    {
        parent::__construct($model);
    }

    function create(array $input, int $id): array
    {
        $attributs = [
            'pesan' => $input['pesan'],
            'status' => $input['status'],
            'idService' => $id,
            'waktu' => Carbon::now('GMT+7')
        ];

        $data = $this->save($attributs);
        return [
            'idRiwayat' => $data->id
        ];
    }

    function deleteByIdService(int $id): array
    {
        $find = $this->model->where('idService', $id)->first();
        if ($find) {
            $data = $this->delete($id, 'idService');
        }
        return ['sukses' => true];
    }
}