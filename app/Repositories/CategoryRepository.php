<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class CategoryRepository extends Repository{
    
    function __construct(Category $model){
        parent::__construct($model);
    }

    function saveData(array $attributs=[], string $id=null){
        $data = $this->save($attributs,$id);
        return [
            'idKategori'=>$data->id
        ];
    }

    function getListData(int $limit = 0, string $search = ''){
        $filters = [
            'limit'=>$limit
        ];
        if($search !== ''){
            $filters['likeWhere'] = [
                'nama'=>$search
            ];
        }
        $attributs = ['id as idKategori','nama'];
        $data = $this->getWhere($attributs,$filters);
        return $data->toArray();
    }

    function getDataById(string $id){
        $attributs = ['id as idKategori','nama'];
        $data = $this->findById($id,$attributs);
        return $data->toArray();
    }

    function getDataNotInResponbility(string $username){
        $responbilityIdCategory = DB::table('responbilities')->where('username',$username)->pluck('idKategori');
        $data = DB::table('categories')->whereNotIn('id',$responbilityIdCategory)->select('id as idKategori','nama')->get();
        return $data;
    }

    function deleteDataById(string $id){
        $data = $this->delete($id);
        return [
            'sukses'=>$data
        ];
    }
}