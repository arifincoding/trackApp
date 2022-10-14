<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface ResponbilityRepoContract
{
    public function getListDataByUsername(string $username): ?Collection;
    public function create(array $inputs, string $role, string $username): bool;
    public function deleteByUsername(string $username): void;
}