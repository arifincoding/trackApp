<?php

namespace App\Repositories;

use App\Models\Responbility;
use App\Repositories\Repository;
use App\Repositories\Contracts\ResponbilityRepoContract;
use Illuminate\Database\Eloquent\Collection;

class ResponbilityRepository extends Repository implements ResponbilityRepoContract
{

    function __construct(Responbility $model)
    {
        $this->model = $model;
    }

    function getListDataByUsername(string $username): Collection
    {
        $data = $this->model->with('kategori')->where('username', $username)->get();
        if ($data->toArray() == []) {
            return null;
        }
        return $data;
    }

    function create(array $inputs, string $role, string $username): bool
    {
        $arrAtribut = [];
        foreach ($inputs['idKategori'] as $key => $item) {
            $arrAtribut[$key]['username'] = $username;
            $arrAtribut[$key]['idKategori'] = $item;
        }
        $data = $this->model->insert($arrAtribut);
        return true;
    }

    function deleteDataById(int $id): array
    {
        $data = $this->delete($id);
        return [
            'sukses' => $data
        ];
    }

    function deleteByUsername(string $username): array
    {
        $find = $this->model->where('username', $username)->first();
        if ($find) {
            $data = $this->model->where('username', $username)->delete();
        }
        return ['sukses' => true];
    }
}