<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\CategoryRepository;
use App\Validations\CategoryValidation;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller{

    private $repository;
    
    function __construct(CategoryRepository $repository){
        $this->repository = $repository;
    }

    function all(Request $request, CategoryValidation $validator): JsonResponse
    {
        $validator->query();
        $validation = $validator->validate($request->only(['limit','cari']));
        $limit = $request->query('limit') ?? 0;
        $search = $request->query('cari') ?? '';
        $data = $this->repository->getListData($limit,$search);
        return $this->jsonSuccess('sukses',200,$data);
    }

    function show($id): JsonResponse
    {
        $data = $this->repository->getDataById($id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    function getCategoryNotInResponbility(string $id): JsonResponse
    {
        $data = $this->repository->getDataNotInResponbility($id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    function create(Request $request, CategoryValidation $validator): JsonResponse
    {
        $input = $request->only('nama');
        $validator->post();
        $validation = $validator->validate($input);
        $data = $this->repository->saveData($input);
        return $this->jsonSuccess('sukses',200,$data);
    }

    function update(Request $request, $id, CategoryValidation $validator): JsonResponse
    {
        $input = $request->only('nama');
        $validator->post($id);
        $validation = $validator->validate($input);
        $data = $this->repository->saveData($input,$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    function delete($id): JsonResponse
    {
        $data = $this->repository->deleteDataById($id);
        return $this->jsonMessageOnly('sukses hapus data kategori');
    }
}