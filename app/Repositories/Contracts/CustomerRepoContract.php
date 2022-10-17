<?php

namespace App\Repositories\Contracts;

interface CustomerRepoContract
{
    public function create(array $attributs): int;
}