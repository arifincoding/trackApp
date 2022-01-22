<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ResponbilityRepository;
use App\Validations\ResponbilityValidation;
use Illuminate\Http\JsonResponse;

class ResponbilityController extends Controller{
    
    public function __construct(ResponbilityRepository $repository){
        $this->repository = $repository;
    }

    function newTechnicianResponbilities(Request $request, $id, ResponbilityValidation $validator){
        $validator->post($id,$request->only(['kategori']));
        $validation = $validator->validate($request->all());
        $data = $this->repository->create($request->all(), $id);
        return $this->jsonSuccess('sukses',200,$data);
    }
    public function delete($id){
        $data = $this->repository->deleteDataById($id);
        return $this->jsonSuccess('sukses hapus tanggung jawab',200, $data);
    }
}