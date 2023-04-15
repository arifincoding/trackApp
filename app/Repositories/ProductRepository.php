<?php

namespace App\Repositories;

use App\Repositories\Repository;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepoContract;

class ProductRepository extends Repository implements ProductRepoContract
{

    public function __construct(Product $model)
    {
        parent::__construct($model, 'product');
    }

    public function create(array $attributs, int $customerId): Product
    {
        $attributs['customer_id'] = $customerId;
        $data = $this->save($attributs);
        return $data;
    }
}
