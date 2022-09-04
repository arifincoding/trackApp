<?php

namespace App\Http\Controllers\Contracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Validations\HistoryValidation;

interface HistoryControllerContract{
    public function create(Request $request, int $id, HistoryValidation $validator): JsonResponse;
}

?>