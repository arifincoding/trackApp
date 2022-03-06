<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ResponbilityRepository;
use App\Repositories\UserRepository;
use App\Validations\ResponbilityValidation;
use Illuminate\Http\JsonResponse;

class ResponbilityController extends Controller{
    
    public function __construct(ResponbilityRepository $responbility, UserRepository $user){
        $this->responbilityRepository = $repository;
        $this->userRepository = $user;
    }

    function newTechnicianResponbilities(Request $request, $id, ResponbilityValidation $validator){
        $validator->post($id,$request->only(['idKategori']));
        $validation = $validator->validate($request->all());
        $findUser = $this->userRepository->getDataById($id);
        $data = $this->responbilityRepository->create($request->all(), $findUser['peran'],$findUser['namaPengguna']);
        return $this->jsonSuccess('sukses',200,$data);
    }
    public function delete($id){
        $data = $this->responbilityRepository->deleteDataById($id);
        return $this->jsonSuccess('sukses hapus tanggung jawab',200, $data);
    }
}