<?php

namespace App\Transformers;

use App\Models\Service;
use League\Fractal\TransformerAbstract;
use App\Helpers\Formatter;

class ServicesTransformer extends TransformerAbstract
{

    public function transform(Service $service)
    {
        return [
            'id' => $service->id,
            'code' => $service->code,
            'complaint' => $service->complaint,
            'status' => $service->status,
            'total_cost' => Formatter::currency($service->total_cost),
            'is_take' => Formatter::boolval($service->is_take),
            'is_approved' => Formatter::boolval($service->is_approved),
            'product' => [
                'name' => $service->product_name,
                'category' => [
                    'name' => $service->category
                ],
                'customer' => [
                    'name' => $service->customer_name,
                    'telp' => $service->telp
                ]
            ]
        ];
    }
}
