<?php

namespace App\Repositories;

use App\Repositories\Repository;
use App\Models\History;
use App\Repositories\Contracts\HistoryRepoContract;

class HistoryRepository extends Repository implements HistoryRepoContract
{

    function __construct(History $model)
    {
        parent::__construct($model);
    }

    function deleteByIdService(int $id): void
    {
        $find = $this->model->where('idService', $id)->first();
        if ($find) {
            $this->delete($id, 'idService');
        }
    }
}