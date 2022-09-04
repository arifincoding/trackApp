<?php

namespace App\Http\Controllers\Contracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Validations\ResponbilityValidation;

interface ResponbilityControllerContract{
    public function all(string $id): JsonResponse;
    public function create(Request $request, $id, ResponbilityValidation $validator): JsonResponse;
    public function delete($id): JsonResponse;
}

?>