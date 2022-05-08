<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    function jsonSuccess(string $message="ok", int $status=200, $data = []){
        if(!isset($data)){
            $data = [];
        }
        return response()->json([
            'status'=>$status,
            'message'=>$message,
            'data'=>$data
        ],$status);
    }

    function jsonToken(string $token){
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => null
        ], 200);
    }

    function jsonMessageOnly(string $massage){
        return response()->json([
            'status'=>200,
            'message'=>$massage
        ],200);
    }

    function jsonValidationError($errors){
        return response()->json([
            'status'=>422,
            'message'=>'kesalahan validasi',
            'errors'=>$errors
        ],422);
    }
}