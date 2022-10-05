<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface UserRepoContract
{
    public function getlistData(array $inputs): Collection;
    public function getDataById(int $id): array;
    public function findByUsername(string $username): array;
    public function deleteById(int $id): bool;
    public function changePassword(array $inputs, string $username): bool;
    public function registerUser(int $id): array;
}