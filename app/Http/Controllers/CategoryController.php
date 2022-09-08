<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Contracts\CategoryControllerContract;

class CategoryController extends Controller implements CategoryControllerContract
{

    private $service;

    function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    function all(Request $request): JsonResponse
    {
        $inputs = $request->only(['limit', 'cari']);
        $data = $this->service->getAllCategory($inputs);
        return $this->jsonSuccess('sukses', 200, $data);
    }

    function show(int $id): JsonResponse
    {
        $data = $this->service->getCategoryById($id);
        return $this->jsonSuccess('sukses', 200, $data);
    }

    function getCategoryNotInResponbility(string $id): JsonResponse
    {
        $data = $this->service->getCategoryNotInResponbility($id);
        return $this->jsonSuccess('sukses', 200, $data);
    }

    function create(Request $request): JsonResponse
    {
        $inputs = $request->only('nama');
        $data = $this->service->newCategory($inputs);
        return $this->jsonSuccess('sukses', 200, $data);
    }

    function update(Request $request, int $id): JsonResponse
    {
        $inputs = $request->only('nama');
        $data = $this->service->updateCategoryById($inputs, $id);
        return $this->jsonSuccess('sukses', 200, $data);
    }

    function delete(int $id): JsonResponse
    {
        $data = $this->service->deleteCategoryById($id);
        return $this->jsonMessageOnly($data);
    }
}