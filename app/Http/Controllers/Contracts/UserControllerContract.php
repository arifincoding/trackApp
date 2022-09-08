<?php

namespace App\Http\Controllers\Contracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface UserControllerContract
{
    public function login(Request $request): JsonResponse;
    public function createRefreshToken(): JsonResponse;
    public function logout(): JsonResponse;
    public function getMyAccount(): JsonResponse;
    public function updateMyAccount(Request $request): JsonResponse;
    public function changePassword(Request $request): JsonResponse;
    public function all(Request $request): JsonResponse;
    public function show(int $id): JsonResponse;
    public function create(Request $request): JsonResponse;
    public function update(Request $request, int $id): JsonResponse;
    public function delete(int $id): JsonResponse;
}