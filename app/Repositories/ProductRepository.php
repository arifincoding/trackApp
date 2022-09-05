<?php

namespace App\Repositories;

use App\Repositories\Repository;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepoContract;

class ProductRepository extends Repository implements ProductRepoContract
{

    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function create(array $attributs):int
    {
        $attributs['nama'] = $attributs['namaProduk'];
        unset($attributs['namaProduk']);
        $data = $this->save($attributs);
        return $data->id;
    }

    public function update(array $attributs, int $id):array
    {
        $attributs['nama'] = $attributs['namaProduk'];
        unset($attributs['namaProduk']);
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