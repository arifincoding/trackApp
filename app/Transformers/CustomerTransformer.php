<?php

namespace App\Transformers;

use App\Models\Customer;
use League\Fractal\TransformerAbstract;
use App\Helpers\Formatter;

class CustomerTransformer extends TransformerAbstract{
    public function transform(Customer $customer){
        return [
            'name'=>$customer->name,
            'telp'=>$customer->telp,
            'is_whatsapp'=>Formatter::boolval($customer->is_whatsapp)
        ];
    }
}