<?php

namespace App\Transformers;

use App\Models\History;
use League\Fractal\TransformerAbstract;
use Illuminate\Support\Carbon;

class HistorytrackTransformer extends TransformerAbstract
{

    public function transform(History $data)
    {
        return [
            'status' => $data->status,
            'message' => $data->message,
            'date' => Carbon::parse($data->created_at)->format('d-m-Y'),
            'time' => Carbon::parse($data->created_at)->format('H:i')
        ];
    }
}
