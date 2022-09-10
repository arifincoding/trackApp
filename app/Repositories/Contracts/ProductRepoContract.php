<?php

namespace App\Repositories\Contracts;

interface ProductRepoContract
{
    public function saveData(array $attributs, int $id): int;
    public function deleteById(int $id): array;
}