<?php

namespace App\Repositories\Contracts;

use App\Models\Customer;

interface CustomerRepoContract
{
    public function create(array $attributs): int;
    public function getDataById(int $id): Customer;
}