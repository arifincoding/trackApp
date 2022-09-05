<?php

namespace App\Repositories\Contracts;

interface CustomerRepoContract
{
    public function create(array $inputs): int;
    public function getDataById(int $id): array;
    public function findDataById(int $id): array;
    public function update(array $inputs, int $id): array;
    public function deleteById(int $id): array;
}