<?php

namespace App\Repositories\Contracts;

interface CustomerRepoContract
{
    public function saveData(array $attributs, ?int $id): int;
    public function getDataById(int $id): array;
    public function findDataById(int $id): array;
    public function deleteById(int $id): array;
}