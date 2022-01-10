<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\CategoryRepository;
use App\Validations\CategoryValidation;

class CategoryController extends Controller{
    function __construct(CategoryRepository $repository){
        $this->repository = $repository;
    }

    function getListCategory(Request $request, CategoryValidation $validator){
        $validator->query();
        $validation = $validator->validate($request->only(['limit','cari']));
        $limit = $request->query('limit') ?? null;
        $search = $request->query('cari') ?? '';
        $data = $this->repository->getListData($limit,$search);
        return $this->jsonSuccess('sukses',200,$data);
    }

    function getCategoryById($id){
        $data = $this->repository->getDataById($id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    function newCategory(Request $request, CategoryValidation $validator){
        $validator->post();
        $validation = $validator->validate($request->only(['kategori','kode']));
        $data = $this->repository->saveData($request->all());
        return $this->jsonSuccess('sukses',200,$data);
    }

    function updateCategory(Request $request, $id, CategoryValidation $validator){
        $validator->post($id);
        $validation = $validator->validate($request->only(['kategori','kode']));
        $data = $this->repository->saveData($request->all(),$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    function deleteCategory($id){
        $data = $this->repository->deleteDataById($id);
        return $this->jsonSuccess('sukses',200,$data);
    }
}