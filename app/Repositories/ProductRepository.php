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

    public function create(array $attributs): int
    {
        $data = $this->save($attributs);
        return $data->id;
    }
}