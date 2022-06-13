<?php

namespace App\Transformers;

use App\Models\Product;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract{
    public function transform(Product $customer){
        return [
            'nama'=>$customer->nama,
            'kategori'=>$customer->kategori,
        ];
    }
}

?>