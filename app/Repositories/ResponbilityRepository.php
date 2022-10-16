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
        parent::__construct($model, 'responbility');
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
        $data = $this->delete($username, 'username', false);
    }
}