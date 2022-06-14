<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Customer;

class CustomerTest extends TestCase{
    
    // get by id
    public function testShouldReturnCustomer(){
        $data = Customer::first();
        $this->get('/customers/'.$data->id,['Authorization'=>'Bearer '.$this->owner()]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'idCustomer',
                'nama',
                'noHp',
                'bisaWA'
            ]
            ]);
    }
}
?>