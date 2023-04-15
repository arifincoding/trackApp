<?php

namespace App\Transformers;

use App\Models\Service;
use League\Fractal\TransformerAbstract;
use App\Helpers\Formatter;

class ServicequeueTransformer extends TransformerAbstract
{
    public function transform(Service $service)
    {
        return [
            'id' => $service->id,
            'code' => $service->code,
            'complaint' => $service->complaint,
            'status' => $service->status,
            'is_approved' => Formatter::boolval($service->is_approved),
            'product' => [
                'name' => $service->product_name,
                'category' => [
                    'name' => $service->category
                ]
            ]
        ];
    }
}
