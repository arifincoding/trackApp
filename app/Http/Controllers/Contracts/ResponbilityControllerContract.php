<?php

namespace App\Http\Controllers\Contracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Validations\ResponbilityValidation;

interface ResponbilityControllerContract{
    public function all(string $id): JsonResponse;
    public function create(Request $request, int $id, ResponbilityValidation $validator): JsonResponse;
    public function delete(int $id): JsonResponse;
}