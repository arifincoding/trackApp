<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;

class UserController extends Controller{

    private $repository;
    
    function __construct(UserRepository $repository){
        $this->repository = $repository;
    }

    function getMyAccount(){
        $data = $this->repository->findByUsername(auth()->payload()->get('username'));
        return $this->jsonSuccess('sukses ambil data',200,$data);
    }

    function updateMyAccount(Request $request){
        $input = $request->only(['email','noHp','alamat']);
        $find = $this->repository->findByUsername(auth()->payload()->get('username'));
        $data = $this->repository->update($input,$find['id']);
        return $this->jsonSuccess('sukses update akun',200,$data);
    }

    function changeMyPassword(Request $request){
        $data = $this->repository->changePassword($request->all(),auth()->payload()->get('username'));
        return $this->jsonMessageOnly('sukses merubah sandi akun');
    }
}

?>