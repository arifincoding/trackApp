<?php

namespace App\Transformers;

use App\Models\Service;
use League\Fractal\TransformerAbstract;
use App\Transformers\ProductTransformer;
use App\Helpers\Formatter;

class ServicequeueTransformer extends TransformerAbstract{

    protected array $defaultIncludes = ['Product'];

    public function transform(Service $service){
        return [
            'id'=>$service->id,
            'kode'=>$service->kode,
            'keluhan'=>$service->keluhan,
            'status'=>$service->status,
            'disetujui'=>Formatter::boolval($service->disetujui)
        ];
    }
    
    public function includeProduct(Service $service){
        return $this->item($service->product, new ProductTransformer);
    }
}

?>