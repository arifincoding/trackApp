<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

interface RepositoryContract
{
    public function save(array $attribut, ?string $filter, string $filterName = 'id'): Model;
    public function findById(string $id, array $attributs = ['*'], bool $throwException = true): ?Model;
    public function delete(string $filter, string $filterName = 'id'): bool;
}