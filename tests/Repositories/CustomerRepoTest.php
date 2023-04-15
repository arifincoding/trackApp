<?php

use App\Models\Customer;
use Laravel\Lumen\Testing\DatabaseTransactions;

class CustomerRepoTest extends TestCase
{

    use DatabaseTransactions;

    private $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make('App\Repositories\CustomerRepository');
    }

    public function testShoudCreateSingleCustomerData()
    {
        $inputs = [
            'name' => 'mark',
            'telp' => '6285235489032',
            'is_whatsapp' => true
        ];

        $result = $this->repository->create($inputs);
        $customer = Customer::orderByDesc('id')->first();
        $this->assertEquals($customer->toArray(), $result->toArray());
    }
}
