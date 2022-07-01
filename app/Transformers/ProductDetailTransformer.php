<?php

namespace App\Transformers;

use App\Models\Product;
use League\Fractal\TransformerAbstract;

class ProductDetailTransformer extends TransformerAbstract{
    public function transform(Product $data){
        return [
            'nama'=>$data->nama,
            'kategori'=>$data->kategori,
            'cacatProduk'=>$data->cacatProduk,
            'kelengkapan'=>$data->kelengkapan,
            'catatan'=>$data->catatan
        ];
    }
}

?>