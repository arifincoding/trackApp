<?php

namespace App\Repositories;

use App\Repositories\Repository;
use App\Models\Product;

class ProductRepository extends Repository{

    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function getDataById(int $id):array
    {
        $attributs = ['id as idProduk','nama','kategori','cacatProduk','kelengkapan','catatan'];
        $data = $this->findById($id,$attributs);
        return $data->toArray();
    }

    public function create(array $attributs):array
    {
        $data = $this->save($attributs);
        return [
            'idProduk'=>$data->id
        ];
    }

    public function update(array $attributs, int $id):array
    {
        $data = $this->save($attributs, $id);
        return [
            'idProduk'=>$data->id
        ];
    }

    public function deleteById(int $id):array
    {
        $data = $this->delete($id);
        return [
            'sukses'=>$data
        ];
    }
}