<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ServiceTrackRepository;
use App\Validations\HistoryValidation;

class HistoryController extends Controller{

    private $historyRepository;

    public function __construct(ServiceTrackRepository $history){
        $this->historyRepository = $history;
    }

    public function create(Request $request, $id, HistoryValidation $validator){
        $input = $request->only(['status','pesan','idService']);
        $validator->validate($input);
        $data = $this->historyRepository->create($input,$id);
        return $this->jsonSuccess('sukses',200,$data);
    }
}