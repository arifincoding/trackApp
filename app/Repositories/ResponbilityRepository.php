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

    function getListDataByUsername(string $username): ?Collection
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
        $this->model->insert($arrAtribut);
        return true;
    }

    function deleteByUsername(string $username): void
    {
        $find = $this->model->where('username', $username)->first();
        if ($find) {
            $this->model->where('username', $username)->delete();
        }
    }
}