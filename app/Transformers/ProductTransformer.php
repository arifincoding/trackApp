<?php

namespace App\Transformers;

use App\Models\Category;
use App\Models\Product;
use League\Fractal\TransformerAbstract;
use App\Transformers\CategoryTransformer;

class ProductTransformer extends TransformerAbstract
{

    protected array $defaultIncludes = ['category'];

    public function transform(Product $product)
    {
        return [
            'name' => $product->name
        ];
    }

    public function includeCategory(Product $product)
    {
        return $this->item($product->category, new CategoryTransformer);
    }
}
