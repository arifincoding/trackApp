<?php

namespace App\Services;

use App\Services\Contracts\CategoryServiceContract;
use App\Repositories\CategoryRepository;
use App\Validations\CategoryValidation;

class CategoryService implements CategoryServiceContract
{
    private $categoryRepository;
    private $categoryValidator;

    public function __construct(CategoryRepository $category, CategoryValidation $validator)
    {
        $this->categoryRepository = $category;
        $this->categoryValidator = $validator;
    }

    public function getAllCategory(array $inputs): array
    {
        $this->categoryValidator->query();
        $this->categoryValidator->validate($inputs);
        $limit = $inputs['limit'] ?? 0;
        $search = $inputs['cari'] ?? '';
        $data = $this->categoryRepository->getListData($limit, $search);
        return $data;
    }

    public function getCategoryById(int $id): array
    {
        $data = $this->categoryRepository->getDataById($id);
        return $data;
    }

    public function getCategoryNotInResponbility(string $id): array
    {
        $data = $this->categoryRepository->getDataNotInResponbility($id);
        return $data->toArray();
    }

    public function newCategory(array $inputs): array
    {
        $this->categoryValidator->post();
        $this->categoryValidator->validate($inputs);
        $data = $this->categoryRepository->save($inputs);
        return ['idKategori' => $data->id];
    }

    public function updateCategoryById(array $inputs, int $id): array
    {
        $this->categoryValidator->post($id);
        $this->categoryValidator->validate($inputs);
        $data = $this->categoryRepository->save($inputs, $id);
        return ['idKategori' => $data->id];
    }

    public function deleteCategoryById(int $id): string
    {
        $this->categoryRepository->deleteDataById($id);
        return 'sukses hapus data kategori';
    }
}