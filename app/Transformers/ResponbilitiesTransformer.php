<?php

namespace App\Transformers;

use App\Models\Responbility;
use App\Transformers\CategoryTransformer;
use League\Fractal\TransformerAbstract;

class ResponbilitiesTransformer extends TransformerAbstract{
    public array $defaultIncludes = ['kategori'];

    public function transform(Responbility $data){
        return [
            'id'=>$data->id
        ];
    }

    public function includeKategori(Responbility $data){
        return $this->item($data->kategori, new CategoryTransformer);
    }
} 