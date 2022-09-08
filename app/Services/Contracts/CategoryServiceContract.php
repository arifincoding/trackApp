<?php

namespace App\Services\Contracts;

interface CategoryServiceContract
{
    public function getAllCategory(array $inputs): array;
    public function getCategoryById(int $id): array;
    public function getCategoryNotInResponbility(string $id): array;
    public function newCategory(array $inputs): array;
    public function updateCategoryById(array $inputs, int $id): array;
    public function deleteCategoryById(int $id): string;
}