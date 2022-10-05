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
        parent::__construct($model);
    }

    function getListDataByIdService(int $idService, array $filter = []): Collection
    {
        $filters = [
            'where' => [
                'idService' => $idService,
                'disetujui' => $filter['disetujui'] ?? null
            ]
        ];
        $data = $this->getWhere(['*'], $filters);
        return $data;
    }

    function getDataById(int $id): array
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
        return $data->toArray();
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
        $data = $this->model->where('idService', $idService)->where('disetujui', 0)->update(['biaya' => 0]);
        return true;
    }

    function deleteById(int $id): array
    {
        $data = $this->delete($id);
        return [
            'sukses' => $data
        ];
    }

    function deleteByIdService(int $id): array
    {
        $find = $this->model->where('idService', $id)->first();
        if ($find) {
            $data = $this->model->where('idService', $id)->delete();
        }
        return ['sukses' => true];
    }
}