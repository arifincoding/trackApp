<?php

namespace App\Repositories;

use App\Repositories\Repository;
use App\Models\Broken;
use App\Helpers\Formatter;
use App\Repositories\Contracts\BrokenRepoContract;

class BrokenRepository extends Repository implements BrokenRepoContract
{
    function __construct(Broken $model)
    {
        parent::__construct($model);
    }

    function getListDataByIdService(int $idService, array $filter = [])
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

    function findDataByIdService(int $id, string $filter)
    {
        if ($filter === 'biaya') {
            return $this->model->where('idService', $id)->where('biaya', null)->first();
        } else if ($filter === 'disetujui') {
            return $this->model->where('idService', $id)->where('disetujui', null)->first();
        }
    }

    function create(array $attributs, int $idService, int $confirmed = 0): array
    {
        $confirm = null;
        if ($confirmed === 0) {
            $confirm = true;
        }
        $attributs += [
            'idService' => $idService,
            'disetujui' => $confirm,
        ];
        $data = $this->save($attributs);
        return ['idKerusakan' => $data->id];
    }

    function update(array $attributs, int $id): array
    {
        $data = $this->save($attributs, $id);
        return [
            'idKerusakan' => $data->id,
            'idService' => $data->idService
        ];
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