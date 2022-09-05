<?php

namespace App\Repositories\Contracts;

interface UserRepoContract
{
    public function getlistData(array $inputs);
    public function getDataById(int $id): array;
    public function findByUsername(string $username): array;
    public function create(array $attributs): array;
    public function update(array $attributs, int $id): array;
    public function deleteById(int $id): bool;
    public function changePassword(array $inputs, string $username): bool;
    public function registerUser(int $id): array;
}