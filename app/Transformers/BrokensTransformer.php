<?php

namespace App\Transformers;

use App\Models\Broken;
use League\Fractal\TransformerAbstract;
use App\Helpers\Formatter;

class BrokensTransformer extends TransformerAbstract{

    public function transform(Broken $data){
        return [
            'id'=>$data->id,
            'judul'=>$data->judul,
            'biaya'=>Formatter::currency($data->biaya),
            'disetujui'=>Formatter::boolval($data->disetujui)
        ];
    }
}

?>