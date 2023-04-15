<?php

namespace App\Transformers;

use App\Models\Service;
use App\Helpers\Formatter;
use App\Transformers\ProductTransformer;
use App\Transformers\BrokentrackTransformer;
use App\Transformers\HistorytrackTransformer;
use League\Fractal\TransformerAbstract;

class ServicetrackTransformer extends TransformerAbstract
{

    protected array $defaultIncludes = ['product', 'broken', 'history'];

    public function transform(Service $data)
    {
        return [
            'code' => $data->code,
            'status' => $data->status,
            'is_approved' => Formatter::boolval($data->is_approved),
            'total_cost' => Formatter::currency($data->total_cost)
        ];
    }

    public function includeProduct(Service $data)
    {
        return $this->item($data->product, new ProductTransformer);
    }

    public function includeBroken(Service $data)
    {
        return $this->collection($data->broken, new BrokentrackTransformer);
    }

    public function includeHistory(Service $data)
    {
        return $this->collection($data->history, new HistorytrackTransformer);
    }
}
