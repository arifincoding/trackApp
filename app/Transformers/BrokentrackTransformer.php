<?php

namespace App\Transformers;

use App\Models\Broken;
use League\Fractal\TransformerAbstract;
use App\Helpers\Formatter;

class BrokentrackTransformer extends TransformerAbstract
{

    public function transform(Broken $data)
    {
        return [
            'title' => $data->title,
            'description' => $data->description,
            'cost' => Formatter::currency($data->cost),
            'is_approved' => Formatter::boolval($data->is_approved)
        ];
    }
}
