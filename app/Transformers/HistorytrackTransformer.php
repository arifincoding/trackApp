<?php

namespace App\Transformers;

use App\Models\History;
use League\Fractal\TransformerAbstract;
use App\Helpers\Formatter;
use Illuminate\Support\Carbon;

class HistorytrackTransformer extends TransformerAbstract{

    public function transform(History $data){
        return [
            'status'=>$data->status,
            'pesan'=>$data->pesan,
            'tanggal'=>Carbon::parse($data->waktu)->format('d-m-Y'),
            'jam'=>Carbon::parse($data->waktu)->format('H:i')
        ];
    }
}

?>