<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Contracts\ControllerContract;

class Controller extends BaseController implements ControllerContract
{
    function jsonSuccess(string $message="ok", int $status=200, $data = []):JsonResponse
    {
        if(!isset($data)){
            $data = [];
        }
        return response()->json([
            'status'=>$status,
            'message'=>$message,
            'data'=>$data
        ],$status);
    }

    function jsonToken(string $token):JsonResponse
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => null
        ], 200);
    }

    function jsonMessageOnly(string $massage):JsonResponse
    {
        return response()->json([
            'status'=>200,
            'message'=>$massage
        ],200);
    }

    function jsonValidationError($errors):JsonResponse
    {
        return response()->json([
            'status'=>422,
            'message'=>'kesalahan validasi',
            'errors'=>$errors
        ],422);
    }
}