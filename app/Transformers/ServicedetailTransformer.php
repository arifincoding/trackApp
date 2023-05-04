<?php

namespace App\Transformers;

use App\Models\Service;
use League\Fractal\TransformerAbstract;
use App\Helpers\Formatter;
use Illuminate\Support\Carbon;
use App\Transformers\ProductDetailTransformer;
use App\Transformers\BrokensTransformer;

class ServicedetailTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'product', 'broken'
    ];

    public function transform(Service $data)
    {
        $toBePaid = $data->total_cost - $data->down_payment;
        $takedDate = null;
        $takedTime = null;
        if ($data->taked_at) {
            $takedDate = Carbon::parse($data->taked_at)->format('d-m-Y');
            $takedTime = Carbon::parse($data->taked_at)->format('H:i');
        }
        return [
            'id' => $data->id,
            'code' => $data->code,
            'complaint' => $data->complaint,
            'status' => $data->status,
            'total_cost' => [
                'int' => $data->total_cost,
                'string' => Formatter::currency($data->total_cost)
            ],
            'is_take' => Formatter::boolval($data->is_take),
            'is_approved' => Formatter::boolval($data->is_approved),
            'estimated_cost' => [
                'int' => $data->estimated_cost,
                'string' => Formatter::currency($data->estimated_cost)
            ],
            'down_payment' => [
                'int' => $data->down_payment,
                'string' => Formatter::currency($data->down_payment)
            ],
            'to_be_paid' => Formatter::currency($toBePaid),
            'entry' => [
                'date' => Carbon::parse($data->entry_at)->format('d-m-Y'),
                'time' => Carbon::parse($data->entry_at)->format('H:i')
            ],
            'taked' => [
                'date' => $takedDate,
                'time' => $takedTime
            ],
            'warranty' => $data->warranty,
            'username' => [
                'cs' => $data->cs_username,
                'tecnician' => $data->tecnician_username
            ],
            'need_approval' => Formatter::boolval($data->need_approval),
            'is_cost_confirmation' => Formatter::boolval($data->is_cost_confirmation),
            'note' => $data->note
        ];
    }
    public function includeProduct(Service $data)
    {
        return $this->item($data->product, new ProductDetailTransformer);
    }
    public function includeBroken(Service $data)
    {
        return $this->collection($data->broken, new BrokensTransformer);
    }
}
