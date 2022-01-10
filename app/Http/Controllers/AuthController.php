<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller{
    
    function __construct(UserRepository $repository){
        $this->repository = $repository;
    }

    function login(Request $request){
        $credentials = $request->only('username','password');

        if (!$token = auth()->attempt($credentials)){
            return response()->json([
                'status'=>401,
                'message'=>'login failed'
            ],401);
        }
        return $this->respondWithToken($token);
    }

    public function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => null
        ], 200);
    }
}