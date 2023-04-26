<?php

namespace App\Repositories;

use App\Models\Responbility;
use App\Repositories\Repository;
use App\Repositories\Contracts\ResponbilityRepoContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ResponbilityRepository extends Repository implements ResponbilityRepoContract
{

    function __construct(Responbility $model)
    {
        parent::__construct($model, 'responbility');
    }

    function getListDataByUsername(string $username): ?Collection
    {
        $data = $this->model->with('category')->where('username', $username)->get();
        return $data;
    }

    function findOneDataByUsername(string $username)
    {
        return $this->model->where('username', $username)->first();
    }

    function findOneByUsernameAndCategory(string $username, ?string $category)
    {
        $data = $this->model->with('category')->where('username', $username)->whereHas('category', function (Builder $q) use ($category) {
            $q->where('name', $category);
        })->first();
        return $data;
    }

    function create(array $inputs, string $username): bool
    {
        $arrAtribut = [];
        foreach ($inputs['category_id'] as $key => $item) {
            $arrAtribut[$key]['username'] = $username;
            $arrAtribut[$key]['category_id'] = $item;
        }
        $this->model->insert($arrAtribut);
        return true;
    }

    function deleteByUsername(string $username): bool
    {
        $data = $this->delete($username, 'username', false);
        return $data;
    }
}
