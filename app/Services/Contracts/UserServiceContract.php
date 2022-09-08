<?php

namespace App\Services\Contracts;

use App\Validations\UserValidation;

interface UserServiceContract
{
    public function login(array $inputs, UserValidation $validator): array;
    public function createRefreshToken(): array;
    public function logout(): array;
    public function getMyAccount(): array;
    public function updateMyAccount(array $inputs, UserValidation $validator): array;
    public function changePassword(array $inputs, UserValidation $validator): array;
    public function getListUser(array $inputs, UserValidation $validator): array;
    public function getUserById(int $id): array;
    public function create(array $inputs, UserValidation $validator): array;
    public function update(array $request, int $id, UserValidation $validator): array;
    public function delete(int $id): string;
}