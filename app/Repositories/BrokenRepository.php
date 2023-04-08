<?php

namespace App\Repositories;

use App\Repositories\Repository;
use App\Models\Broken;
use App\Helpers\Formatter;
use App\Repositories\Contracts\BrokenRepoContract;
use Illuminate\Database\Eloquent\Collection;

class BrokenRepository extends Repository implements BrokenRepoContract
{
    function __construct(Broken $model)
    {
        parent::__construct($model, 'broken');
    }

    function getListDataByIdService(int $idService, array $whereFilter = [], string $search = null): Collection
    {
        $data = $this->model->where('service_id', $idService);
        if (sizeof($whereFilter) > 0) {
            foreach ($whereFilter as $key => $where) {
                $data->where($key, $where);
            }
        }
        $search ? $data->search($search) : null;
        return $data->get();
    }

    function getDataById(int $id): Broken
    {
        $data = $this->findById($id);
        $data->is_approved = Formatter::boolval($data->is_approved);
        $data->costString = Formatter::currency($data->cost);
        return $data;
    }

    function findOneDataByWhere(array $filters = []): ?Broken
    {
        $data = $this->model->orderBy('id', 'asc');
        foreach ($filters as $key => $filter) {
            $data->where($key, $filter);
        }
        return $data->first();
    }

    function setCostInNotAgreeToZero(int $idService): bool
    {
        $this->model->where('service_id', $idService)->where('is_approved', 0)->update(['cost' => 0]);
        return true;
    }

    function deleteByIdService(int $id): bool
    {
        $data = $this->delete($id, 'service_id', false);
        return $data;
    }
}
