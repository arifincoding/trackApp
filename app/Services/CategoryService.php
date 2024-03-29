<?php

namespace App\Services;

use App\Services\Contracts\CategoryServiceContract;
use App\Repositories\CategoryRepository;
use App\Validations\CategoryValidation;
use Illuminate\Support\Facades\Log;

class CategoryService implements CategoryServiceContract
{
    private CategoryRepository $categoryRepository;
    private CategoryValidation $validator;

    public function __construct(CategoryRepository $category, CategoryValidation $validator)
    {
        $this->categoryRepository = $category;
        $this->validator = $validator;
    }

    public function getAllCategory(array $inputs): array
    {
        Log::info("trying to access all categories data", ["query" => $inputs]);
        $this->validator->query();
        $this->validator->validate($inputs, 'categories');
        $limit = $inputs['limit'] ?? 0;
        $search = $inputs['cari'] ?? '';
        $data = $this->categoryRepository->getListData($limit, $search);
        Log::info("user is accessing all categories data");
        return $data->toArray();
    }

    public function getCategoryById(int $id): array
    {
        Log::info("User trying to accessing a single category data by id category", ['id category' => $id]);
        $data = $this->categoryRepository->getDataById($id);
        Log::info("User is accessing a single category data", ["id category" => $data->idKategori]);
        return $data->toArray();
    }

    public function getCategoryNotInResponbility(string $username): array
    {
        Log::info("trying to accessing all categories data not in tecnicion responbility by username", ["username" => $username]);
        $data = $this->categoryRepository->getDataNotInResponbility($username);
        Log::info("User is accessing all categories data not in tecnicion responbility by username");
        return $data->toArray();
    }

    public function newCategory(array $inputs): array
    {
        Log::info("User is trying to create a single category data", ['data' => $inputs]);
        $this->validator->post();
        $this->validator->validate($inputs, 'create');
        $data = $this->categoryRepository->save($inputs);
        Log::info("User create a single category data successfully", ["id category" => $data->id]);
        return ['idKategori' => $data->id];
    }

    public function updateCategoryById(array $inputs, int $id): array
    {
        Log::info("User is trying to update a single category data by id category", ["id category" => $id, "data" => $inputs]);
        $this->validator->post($id);
        $this->validator->validate($inputs, 'update');
        $data = $this->categoryRepository->save($inputs, $id);
        Log::info("User update a single category data by id category successfully", ["id category" => $data->id]);
        return ['idKategori' => $data->id];
    }

    public function deleteCategoryById(int $id): string
    {
        Log::info("trying to deleting a single category data by id category", ["id category" => $id]);
        $this->categoryRepository->delete($id);
        Log::info("User delete a single category data by id category successfully", ['id category' => $id]);
        return 'sukses hapus data kategori';
    }
}