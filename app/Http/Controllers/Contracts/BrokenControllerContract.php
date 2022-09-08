<?php

namespace App\Http\Controllers\Contracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface BrokenControllerContract{
    public function getListByIdService(int $id): JsonResponse;
    public function newByIdService(Request $request,int $id): JsonResponse;
    public function getBrokenById(int $id): JsonResponse;
    public function update(Request $request, int $id): JsonResponse;
    public function updateCost(Request $request, int $id): JsonResponse;
    public function updateCofirmation(Request $request, int $id): JsonResponse;
    public function delete(int $id): JsonResponse;
}