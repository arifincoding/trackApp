<?php

namespace App\Repositories\Contracts;

use App\Models\Product;

interface ProductRepoContract
{
    public function create(array $attributs, int $customerId): Product;
}
