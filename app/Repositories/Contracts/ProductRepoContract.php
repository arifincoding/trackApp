<?php

namespace App\Repositories\Contracts;

interface ProductRepoContract
{
    public function create(array $attributs): int;
}