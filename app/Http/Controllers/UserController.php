<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Validations\UserValidation;

class UserController extends Controller{

    private $repository;
    
    function __construct(UserRepository $repository){
        $this->repository = $repository;
    }

    function getMyAccount(){
        $data = $this->repository->findByUsername(auth()->payload()->get('username'));
        return $this->jsonSuccess('sukses ambil data',200,$data);
    }

    function updateMyAccount(Request $request, UserValidation $validator){
        $input = $request->only(['email','noHp','alamat']);
        $find = $this->repository->findByUsername(auth()->payload()->get('username'));
        $validator->update($find['id']);
        $validator->validate($input);
        $data = $this->repository->update($input,$find['id']);
        return $this->jsonSuccess('sukses update akun',200,$data);
    }

    function changeMyPassword(Request $request, UserValidation $validator){
        $input = $request->only(['sandiLama','sandiBaru']);
        $validator->changePassword();
        $validator->validate($input);
        $data = $this->repository->changePassword($input,auth()->payload()->get('username'));
        return $this->jsonMessageOnly('sukses merubah sandi akun');
    }
}

?>