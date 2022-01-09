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

    function jsonToken(string $message,string $token){
        return response()->json([
            'status'=>200,
            'message'=>$message,
            'token'=>$token
        ],200);
    }

    function jsonValidationError($errors){
        return response()->json([
            'status'=>400,
            'message'=>'kesalahan validasi',
            'errors'=>$errors
        ],400);
    }
}