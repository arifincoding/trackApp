<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\HistoryRepository;
use App\Validations\HistoryValidation;
use Illuminate\Http\JsonResponse;

class HistoryController extends Controller{

    private $historyRepository;

    public function __construct(HistoryRepository $history)
    {
        $this->historyRepository = $history;
    }

    public function create(Request $request, $id, HistoryValidation $validator): JsonResponse
    {
        $input = $request->only(['status','pesan']);
        $validator->validate($input);
        $data = $this->historyRepository->create($input,$id);
        return $this->jsonSuccess('sukses',200,$data);
    }
}