<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;

class UserController extends Controller{
    function __construct(UserRepository $repository){
        $this->repository = $repository;
    }

    function changeMyPassword(Request $request){
        $data = $this->repository->changePassword($request->all(),auth()->payload()->get('username'));
        return $this->jsonMessageOnly('sukses merubah sandi akun');
    }
}

?>