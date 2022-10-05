<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface CategoryRepoContract
{
    public function saveData(array $attributs = [], ?int $id): array;
    public function getListData(int $limit = 0, string $search = ''): array;
    public function getDataById(int $id): array;
    public function getDataNotInResponbility(string $username): Collection;
    public function deleteDataById(int $id): array;
}