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
        parent::__construct($model, 'category');
    }

    function getListData(?int $limit = null, ?string $search = null): Collection
    {
        $attributs = ['id as category_id', 'name'];
        $data = $this->model->select($attributs);
        if ($search) {
            $data->search($search);
        }
        if ($limit) {
            $data->take($limit);
        }
        return $data->get();
    }

    function getDataById(int $id): Category
    {
        $attributs = ['id as category_id', 'name'];
        $data = $this->findById($id, $attributs);
        return $data;
    }

    function getDataNotInResponbility(string $username): Collection
    {
        $data = $this->model->whereNotIn('id', DB::table('responbilities')->select('category_id')->where('username', $username))->get();
        return $data;
    }
}
