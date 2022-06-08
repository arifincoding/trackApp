<?php

namespace App\Http\Controllers;

use App\Repositories\CustomerRepository;

class CustomerController extends Controller{
    private $customerRepository;
    public function __construct(CustomerRepository $customer){
        $this->customerRepository = $customer;
    }
    public function getCustomerById(int $id){
        $data = $this->customerRepository->getDataById($id);
        return $this->jsonSuccess('sukses',200,$data);
    }
}

?>