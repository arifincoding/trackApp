<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Validations\EmployeeValidation;
use Illuminate\Http\JsonResponse;

class EmployeeController extends Controller{
    function __construct(UserRepository $repository){
        $this->repository = $repository;
    }

    function getListEmployee(Request $request, EmployeeValidation $validator): JsonResponse
    {
        $filters = $request->only(['limit','status']);
        $validation = $validator->validate($filters);
        $data = $this->repository->getListData($filters);
        return $this->jsonSuccess('sukses',200,$data);
    }

    function getEmployeeById($id): JsonResponse
    {
        $data = $this->repository->getDataById($id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    function createEmployee(Request $request, EmployeeValidation $validator): JsonResponse
    {

        $validator->post();
        $validation = $validator->validate($request->all());
        $data = $this->repository->create($request->all());
        return $this->jsonSuccess('sukses',200,$data);
    }

    function updateEmployee(Request $request,$id, EmployeeValidation $validator): JsonResponse
    {
        $validator->post($id);
        $validation = $validator->validate($request->all());
        $data = $this->repository->update($request->all(), $id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    function changeStatusEmployee(Request $request, $id, EmployeeValidation $validator): JsonResponse
    {
        $validator->status();
        $validation = $validator->validate($request->only(['status']));
        $data = $this->repository->changeStatus($request->input('status'),$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

}