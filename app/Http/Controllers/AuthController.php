<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Validations\AuthValidation;

class AuthController extends Controller{
    
    private $repository;

    function __construct(UserRepository $repository){
        $this->repository = $repository;
    }

    function login(Request $request, AuthValidation $validator){
        $credentials = $request->only('username','password');
        $validator->validate($credentials);
        if (!$token = auth()->attempt($credentials)){
            return $this->jsonValidationError([
                'password'=>[
                    'password salah'
                ]
                ]);
        }
        return $this->jsonToken($token);
    }

    public function createRefreshToken(){
        $newToken = auth()->refresh();
        return $this->jsonToken($newToken);
    }

    public function logout(){
        auth()->logout();
        return $this->jsonMessageOnly('sukses logout');
    }
}