<?php

namespace App\Transformers;

use App\Models\Service;
use App\Helpers\Formatter;
use App\Transformers\ProductTransformer;
use App\Transformers\BrokentrackTransformer;
use App\Transformers\HistorytrackTransformer;
use League\Fractal\TransformerAbstract;

class ServicetrackTransformer extends TransformerAbstract{
    
    protected array $defaultIncludes = ['produk','kerusakan','riwayat'];

    public function transform(Service $data){
        return [
            'kode'=>$data->kode,
            'status'=>$data->status,
            'disetujui'=>Formatter::boolval($data->disetujui),
            'totalBiaya'=>Formatter::currency($data->totalBiaya)
        ];
    }

    public function includeProduk(Service $data){
        return $this->item($data->produk,new ProductTransformer);
    }

    public function includeKerusakan(Service $data){
        return $this->collection($data->kerusakan,new BrokentrackTransformer);
    }

    public function includeRiwayat(Service $data){
        return $this->collection($data->riwayat,new HistorytrackTransformer);
    }
}