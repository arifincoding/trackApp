<?php

namespace App\Transformers;

use App\Models\Customer;
use League\Fractal\TransformerAbstract;
use App\Helpers\Formatter;

class CustomerTransformer extends TransformerAbstract{
    public function transform(Customer $customer){
        return [
            'nama'=>$customer->nama,
            'noHp'=>$customer->noHp,
            'bisaWA'=>Formatter::boolval($customer->bisaWA)
        ];
    }
}

?>