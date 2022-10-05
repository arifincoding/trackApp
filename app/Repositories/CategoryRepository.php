<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use App\Repositories\Contracts\CategoryRepoContract;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository extends Repository implements CategoryRepoContract
{

    function __construct(Category $model)
    {
        parent::__construct($model);
    }

    function getListData(int $limit = 0, string $search = ''): array
    {
        $filters = [
            'limit' => $limit
        ];
        if ($search !== '') {
            $filters['likeWhere'] = [
                'nama' => $search
            ];
        }
        $attributs = ['id as idKategori', 'nama'];
        $data = $this->getWhere($attributs, $filters);
        return $data->toArray();
    }

    function getDataById(int $id): array
    {
        $attributs = ['id as idKategori', 'nama'];
        $data = $this->findById($id, $attributs);
        return $data->toArray();
    }

    function getDataNotInResponbility(string $username): Collection
    {
        $responbilityIdCategory = DB::table('responbilities')->where('username', $username)->pluck('idKategori');
        $data = DB::table('categories')->whereNotIn('id', $responbilityIdCategory)->select('id as idKategori', 'nama')->get();
        return $data;
    }
}