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

    public function saveData(array $attributs, ?int $id = null): int
    {
        $data = $this->save($attributs, $id);
        return $data->id;
    }

    public function deleteById(int $id): array
    {
        $data = $this->delete($id);
        return [
            'sukses' => $data
        ];
    }
}