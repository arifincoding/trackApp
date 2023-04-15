<?php

namespace App\Transformers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use League\Fractal\TransformerAbstract;
use App\Transformers\CategoryTransformer;
use App\Transformers\CustomerTransformer;

class ProductDetailTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = ['category', 'client'];

    public function transform(Product $data)
    {
        return [
            'name' => $data->name,
            'product_defects' => $data->product_defects,
            'completeness' => $data->completeness
        ];
    }

    public function includeCategory(Category $data)
    {
        return $this->item($data->category, new CategoryTransformer);
    }

    public function includeClient(Customer $data)
    {
        return $this->item($data->client, new CustomerTransformer);
    }
}
