<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Repositories\Repository;
use App\Exceptions\Handler;

class CustomerRepository extends Repository{
    function __construct(Customer $model){
        parent::__construct($model);
    }

    public function create(array $inputs):array
    {
        try{
            $noHp = isset($inputs['noHp']) ? $inputs['noHp'] : null;
            $wa = false;
            if($noHp !== null){
                $wa = filter_var($inputs['mendukungWhatsapp'],FILTER_VALIDATE_BOOLEAN);
            }
            $attributs = [
                'name'=>$inputs['namaCustomer'],
                'gender'=>$inputs['jenisKelamin'],
                'phoneNumber'=>$inputs['noHp'],
                'whatsapp'=> $wa
            ];
            $data = $this->save($attributs);
            return ['idCustomer'=>$data->id];
        }catch(Handler $e){
            return [];
        }
    }

    public function isCustomerExist(array $inputs)
    {
        $data = $this->model->where('name',$inputs['namaCustomer'])->where('phoneNumber',$inputs['noHp'])->first();
        if($data){
            return ['idCustomer'=>$data->id];
        }
        return false;
    }
}

?>