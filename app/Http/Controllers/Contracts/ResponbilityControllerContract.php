<?php

namespace App\Http\Controllers\Contracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface ResponbilityControllerContract
{
    public function all(string $id): JsonResponse;
    public function create(Request $request, int $id): JsonResponse;
    public function delete(int $id): JsonResponse;
}