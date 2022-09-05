<?php

namespace App\Http\Controllers\Contracts;
use Illuminate\Http\JsonResponse;

interface ControllerContract {
    public function jsonSuccess(string $message, int $status, $data):JsonResponse;
    public function jsonToken(string $token):JsonResponse;
    public function jsonMessageOnly(string $massage):JsonResponse;
    public function jsonValidationError($errors):JsonResponse;
}

?>