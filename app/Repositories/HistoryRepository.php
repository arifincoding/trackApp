<?php

namespace App\Repositories;

use App\Repositories\Repository;
use App\Models\History;
use App\Repositories\Contracts\HistoryRepoContract;

class HistoryRepository extends Repository implements HistoryRepoContract
{

    function __construct(History $model)
    {
        parent::__construct($model, 'history');
    }

    function deleteByIdService(int $id): bool
    {
        $data = $this->delete($id, 'service_id', false);
        return $data;
    }
}
