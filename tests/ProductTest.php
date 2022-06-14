<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Product;

class ProductTest extends TestCase{
    
    // get by id
    public function testShouldReturnProduct(){
        $data = Product::first();
        $this->get('/products/'.$data->id,['Authorization'=>'Bearer '.$this->owner()]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'idProduk',
                'nama',
                'kategori',
                'cacatProduk',
                'kelengkapan',
                'catatan'
            ]
            ]);
    }
}

?>