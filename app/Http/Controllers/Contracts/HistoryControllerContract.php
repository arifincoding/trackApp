<?php

namespace App\Http\Controllers\Contracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface HistoryControllerContract{
    public function create(Request $request, int $id): JsonResponse;
}