<?php

namespace App\Http\Controllers\Contracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Validations\BrokenValidation;

interface BrokenControllerContract{
    public function getListByIdService(int $id): JsonResponse;
    public function newByIdService(Request $request,int $id,BrokenValidation $validator): JsonResponse;
    public function getBrokenById(int $id): JsonResponse;
    public function update(Request $request, int $id, BrokenValidation $validator): JsonResponse;
    public function updateCost(Request $request, int $id, BrokenValidation $validator): JsonResponse;
    public function updateCofirmation(Request $request, int $id, BrokenValidation $validator): JsonResponse;
    public function delete(int $id): JsonResponse;
}

?>