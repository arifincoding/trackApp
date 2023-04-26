<?php

namespace App\Services;

use App\Services\Contracts\CategoryServiceContract;
use App\Repositories\CategoryRepository;
use App\Validations\CategoryValidation;

class CategoryService implements CategoryServiceContract
{
    private CategoryRepository $categoryRepository;
    private CategoryValidation $validator;

    public function __construct(CategoryRepository $category, CategoryValidation $validator)
    {
        $this->categoryRepository = $category;
        $this->validator = $validator;
    }

    public function getAllCategory(array $inputs = []): array
    {
        $this->validator->query();
        $this->validator->validate($inputs, 'categories');
        $limit = $inputs['limit'] ?? 0;
        $search = $inputs['search'] ?? null;
        $data = $this->categoryRepository->getListData($limit, $search);
        return $data->toArray();
    }

    public function getCategoryById(int $id): array
    {
        $data = $this->categoryRepository->getDataById($id);
        return $data->toArray();
    }

    public function getCategoryNotInResponbility(string $username): array
    {
        $data = $this->categoryRepository->getDataNotInResponbility($username);
        return $data->toArray();
    }

    public function newCategory(array $inputs): array
    {
        $this->validator->post();
        $this->validator->validate($inputs, 'create');
        $data = $this->categoryRepository->save($inputs);
        return ['category_id' => $data->id];
    }

    public function updateCategoryById(array $inputs, int $id): array
    {
        $this->validator->post($id);
        $this->validator->validate($inputs, 'update');
        $data = $this->categoryRepository->save($inputs, $id);
        return ['category_id' => $data->id];
    }

    public function deleteCategoryById(int $id): string
    {
        $this->categoryRepository->delete($id);
        return 'sukses hapus data kategori';
    }
}
