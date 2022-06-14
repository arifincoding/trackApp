<?php

namespace App\Transformers;

use App\Models\Service;
use League\Fractal\TransformerAbstract;
use App\Transformers\CustomerTransformer;
use App\Transformers\ProductTransformer;
use App\Helpers\Formatter;

class ServicesTransformer extends TransformerAbstract{

    protected array $defaultIncludes = ['customer','product'];

    public function transform(Service $service){
        return [
            'id'=>$service->id,
            'kode'=>$service->kode,
            'keluhan'=>$service->keluhan,
            'status'=>$service->status,
            'totalBiaya'=>Formatter::currency($service->totalBiaya),
            'diambil'=>Formatter::boolval($service->diambil),
            'disetujui'=>Formatter::boolval($service->disetujui)
        ];
    }

    public function includeCustomer(Service $service){
        return $this->item($service->customer, new CustomerTransformer);
    }
    public function includeProduct(Service $service){
        return $this->item($service->product, new ProductTransformer);
    }
}

?>