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

    function getListDataByIdService(int $idService, array $filter = []): Collection
    {
        $data = $this->model->where('idService', $idService);
        if (isset($filter['disetujui'])) {
            $data->where('disetujui', $filter['disetujui']);
        }
        return $data->get();
    }

    function getDataById(int $id): Broken
    {
        $attributs = [
            'id as idKerusakan',
            'idService',
            'judul',
            'deskripsi',
            'biaya',
            'disetujui'
        ];
        $data = $this->findById($id, $attributs);
        $data->disetujui = Formatter::boolval($data->disetujui);
        $data->biayaString = Formatter::currency($data->biaya);
        return $data;
    }

    function findDataByIdService(int $id, string $filter): ?Broken
    {
        if ($filter === 'biaya') {
            return $this->model->where('idService', $id)->where('biaya', null)->first();
        } else if ($filter === 'disetujui') {
            return $this->model->where('idService', $id)->where('disetujui', null)->first();
        }
    }

    function setCostInNotAgreeToZero(int $idService): bool
    {
        $this->model->where('idService', $idService)->where('disetujui', 0)->update(['biaya' => 0]);
        return true;
    }

    function deleteByIdService(int $id): bool
    {
        $data = $this->delete($id, 'idService', false);
        return $data;
    }
}