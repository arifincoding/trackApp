<?php

namespace App\Http\Controllers\Contracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface WhatsappControllerContract{
    public function scan(): JsonResponse;
    public function chat(Request $request,int $id): JsonResponse;
}

?>