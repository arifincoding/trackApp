<?php

namespace App\Services\Contracts;

use App\Validations\CategoryValidation;

interface CategoryServiceContract
{
    public function getAllCategory(array $inputs, CategoryValidation $validator): array;
    public function getCategoryById(int $id): array;
    public function getCategoryNotInResponbility(string $id): array;
    public function create(array $inputs, CategoryValidation $validator): array;
    public function update(array $inputs, int $id, CategoryValidation $validator): array;
    public function delete(int $id): string;
}