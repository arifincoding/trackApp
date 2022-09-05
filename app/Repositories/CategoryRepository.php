<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use App\Repositories\Contracts\CategoryRepoContract;

class CategoryRepository extends Repository implements CategoryRepoContract
{

    function __construct(Category $model)
    {
        parent::__construct($model);
    }

    function saveData(array $attributs = [], int $id = null): array
    {
        $data = $this->save($attributs, $id);
        return [
            'idKategori' => $data->id
        ];
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

    function getDataNotInResponbility(string $username)
    {
        $responbilityIdCategory = DB::table('responbilities')->where('username', $username)->pluck('idKategori');
        $data = DB::table('categories')->whereNotIn('id', $responbilityIdCategory)->select('id as idKategori', 'nama')->get();
        return $data;
    }

    function deleteDataById(int $id): array
    {
        $data = $this->delete($id);
        return [
            'sukses' => $data
        ];
    }
}