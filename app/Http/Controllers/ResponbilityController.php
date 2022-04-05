<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ResponbilityRepository;
use App\Repositories\UserRepository;
use App\Validations\ResponbilityValidation;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ResponbilityController extends Controller{
    
    public function __construct(ResponbilityRepository $responbility, UserRepository $user){
        $this->responbilityRepository = $responbility;
        $this->userRepository = $user;
    }

    function getTechnicianResponbilities(){
        $data = $this->responbilityRepository->getListDataByUsername(auth()->payload()->get('username'));
        if($data){
            return $this->jsonSuccess('sukses',200,$data);
        }
        throw new ModelNotFoundException('data tidak ditemukan');
    }

    function newTechnicianResponbilities(Request $request, $id, ResponbilityValidation $validator){
        $input = $request->only(['idKategori']);
        $validator->post($id,$input);
        $validation = $validator->validate($input);
        $findUser = $this->userRepository->getDataById($id);
        $data = $this->responbilityRepository->create($input, $findUser['peran'],$findUser['username']);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function delete($id){
        $data = $this->responbilityRepository->deleteDataById($id);
        return $this->jsonSuccess('sukses',200, $data);
    }
}