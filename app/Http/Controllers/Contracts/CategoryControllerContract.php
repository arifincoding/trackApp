<?php

namespace App\Http\Controllers\Contracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface CategoryControllerContract
{
    public function all(Request $request): JsonResponse;
    public function show(int $id): JsonResponse;
    public function getCategoryNotInResponbility(string $id): JsonResponse;
    public function create(Request $request): JsonResponse;
    public function update(Request $request, int $id): JsonResponse;
    public function delete(int $id): JsonResponse;
}