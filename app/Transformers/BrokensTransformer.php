<?php

namespace App\Transformers;

use App\Models\Broken;
use League\Fractal\TransformerAbstract;
use App\Helpers\Formatter;

class BrokensTransformer extends TransformerAbstract{

    public function transform(Broken $data){
        return [
            'id'=>$data->id,
            'title'=>$data->title,
            'cost'=>Formatter::currency($data->cost),
            'is_approved'=>Formatter::boolval($data->is_approved)
        ];
    }
}

?>