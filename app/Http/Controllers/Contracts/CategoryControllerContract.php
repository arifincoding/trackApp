<?php

namespace App\Http\Controllers\Contracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Validations\CategoryValidation;

interface CategoryControllerContract{
    public function all(Request $request, CategoryValidation $validator): JsonResponse;
    public function show($id): JsonResponse;
    public function getCategoryNotInResponbility(string $id): JsonResponse;
    public function create(Request $request, CategoryValidation $validator): JsonResponse;
    public function update(Request $request, $id, CategoryValidation $validator): JsonResponse;
    public function delete($id): JsonResponse;
}

?>