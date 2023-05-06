<?php

namespace App\Services\Contracts;

interface UserServiceContract
{
    public function login(array $inputs): string;
    public function createRefreshToken(): string;
    public function logout(): string;
    public function getMyAccount(): array;
    public function updateMyAccount(array $inputs): string;
    public function changePassword(array $inputs): string;
    public function getListUser(array $inputs): array;
    public function getUserById(int $id): array;
    public function newUser(array $inputs): array;
    public function updateUserById(array $inputs, int $id): array;
    public function deleteUserById(int $id): string;
}
