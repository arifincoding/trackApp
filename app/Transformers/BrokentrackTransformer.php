<?php

namespace App\Transformers;

use App\Models\Broken;
use League\Fractal\TransformerAbstract;
use App\Helpers\Formatter;

class BrokentrackTransformer extends TransformerAbstract{

    public function transform(Broken $data){
        return [
            'judul'=>$data->judul,
            'deskripsi'=>$data->deskripsi,
            'biaya'=>Formatter::currency($data->biaya),
            'disetujui'=>Formatter::boolval($data->disetujui)
        ];
    }
}

?>