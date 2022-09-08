<?php

namespace App\Http\Controllers\Contracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Validations\UserValidation;

interface UserControllerContract
{
    public function login(Request $request, UserValidation $validator): JsonResponse;
    public function createRefreshToken(): JsonResponse;
    public function logout(): JsonResponse;
    public function getMyAccount(): JsonResponse;
    public function updateMyAccount(Request $request, UserValidation $validator): JsonResponse;
    public function changePassword(Request $request, UserValidation $validator): JsonResponse;
    public function all(Request $request, UserValidation $validator): JsonResponse;
    public function show(int $id): JsonResponse;
    public function create(Request $request, UserValidation $validator): JsonResponse;
    public function update(Request $request, int $id, UserValidation $validator): JsonResponse;
    public function delete(int $id): JsonResponse;
}