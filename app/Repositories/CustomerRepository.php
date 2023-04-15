<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Repositories\Repository;
use App\Repositories\Contracts\CustomerRepoContract;

class CustomerRepository extends Repository implements CustomerRepoContract
{
    function __construct(Customer $model)
    {
        parent::__construct($model, 'customer');
    }

    public function create(array $attributs): Customer
    {
        $data = $this->save($attributs);
        return $data;
    }
}
