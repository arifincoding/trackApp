<?php

namespace App\Http\Controllers;

use App\Repositories\ProductRepository;

class ProductController extends Controller {
    private $productRepository;

    public function __construct(ProductRepository $product)
    {
        $this->productRepository = $product;
    }

    public function show(int $id)
    {
        $data = $this->productRepository->getDataById($id);
        return $this->jsonSuccess('sukses',200,$data);
    }
}

?>