<?php

namespace App\Services;

use App\Services\Contracts\CategoryServiceContract;
use App\Repositories\CategoryRepository;
use App\Validations\CategoryValidation;
use Illuminate\Support\Facades\Log;

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
        Log::info("trying to access all categories data", ["query" => $inputs]);
        $this->categoryValidator->query();
        $this->categoryValidator->validate($inputs);
        $limit = $inputs['limit'] ?? 0;
        $search = $inputs['cari'] ?? '';
        $data = $this->categoryRepository->getListData($limit, $search);
        Log::info("user is accessing all categories data");
        return $data;
    }

    public function getCategoryById(int $id): array
    {
        Log::info("User trying to accessing a single category data by id category", ['id category' => $id]);
        $data = $this->categoryRepository->getDataById($id);
        Log::info("User is accessing a single category data", ["id category" => $data["idKategori"]]);
        return $data;
    }

    public function getCategoryNotInResponbility(string $id): array
    {
        Log::info("trying to accessing all categories data not in tecnicion responbility by username", ["username" => $id]);
        $data = $this->categoryRepository->getDataNotInResponbility($id);
        Log::info("User is accessing all categories data not in tecnicion responbility by username");
        return $data->toArray();
    }

    public function newCategory(array $inputs): array
    {
        Log::info("User is trying to create a single category data", ['data' => $inputs]);
        $this->categoryValidator->post();
        $this->categoryValidator->validate($inputs);
        $data = $this->categoryRepository->save($inputs);
        Log::info("User create a single category data successfully", ["id category" => $data->id]);
        return ['idKategori' => $data->id];
    }

    public function updateCategoryById(array $inputs, int $id): array
    {
        Log::info("User is trying to update a single category data by id category", ["id category" => $id, "data" => $inputs]);
        $this->categoryValidator->post($id);
        $this->categoryValidator->validate($inputs);
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