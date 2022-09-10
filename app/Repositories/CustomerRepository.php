<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Repositories\Repository;
use App\Helpers\Formatter;
use App\Repositories\Contracts\CustomerRepoContract;

class CustomerRepository extends Repository implements CustomerRepoContract
{
    function __construct(Customer $model)
    {
        parent::__construct($model);
    }

    public function saveData(array $attributs, int $id = null): int
    {
        $data = $this->save($attributs);
        return $data->id;
    }

    public function getDataById(int $id): array
    {
        $attributs = ['id as idCustomer', 'nama', 'noHp', 'bisaWA'];
        $data = $this->findById($id, $attributs);
        $data->bisaWA = Formatter::boolval($data->bisaWA);
        return $data->toArray();
    }

    public function findDataById(int $id): array
    {
        $data = $this->findById($id);
        return $data->toArray();
    }

    public function deleteById(int $id): array
    {
        $data = $this->delete($id);
        return ['sukses' => true];
    }
}