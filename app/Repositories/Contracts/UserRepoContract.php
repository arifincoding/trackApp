<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepoContract
{
    public function getlistData(array $inputs): Collection;
    public function getDataById(int $id): User;
    public function findByUsername(string $username): User;
    public function changePassword(array $inputs, string $username): bool;
    public function registerUser(int $id): array;
}