<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Repositories\Repository;
use App\Exceptions\Handler;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CustomerRepository extends Repository{
    function __construct(Customer $model){
        parent::__construct($model);
    }

    public function create(array $inputs):array
    {
        try{
            $noHp = $inputs['noHp'] ?? null;
            $wa = false;
            if($noHp !== null){
                $wa = filter_var($inputs['mendukungWhatsapp'],FILTER_VALIDATE_BOOLEAN);
            }
            $attributs = [
                'name'=>$inputs['namaCustomer'],
                'gender'=>$inputs['jenisKelamin'],
                'phoneNumber'=>$inputs['noHp'],
                'whatsapp'=> $wa,
                'count'=>1
            ];
            $data = $this->save($attributs);
            return ['idCustomer'=>$data->id];
        }catch(Handler $e){
            return [];
        }
    }

    public function isCustomerExist(array $inputs)
    {
        if(!empty($inputs['noHp'])){
            $findData = $this->model->where('name',$inputs['namaCustomer'])->where('phoneNumber',$inputs['noHp'])->first();
            if($findData){
                return [
                    'exist'=>true,
                    'idCustomer'=>$findData->id
                ];
            }
        }
        return ['exist'=>false];
    }

    public function findDataById(string $id){
        $data = $this->findById($id);
        return $data->toArray();
    }

    public function updateCount(string $id, string $operator){
        $attributs['count'] = 0;
        $findData = $this->findById($id);
        if($operator === 'plus'){
            $attributs['count'] = $findData->count + 1;
        }else if($operator === 'minus'){
            $attributs['count'] = $findData->count - 1;
        }
        $data = $this->save($attributs,$findData->id);
        return ['idCustomer'=>$data->id];
    }

    public function update(array $inputs, string $idCustomer){

        $findData = $this->findById($idCustomer);
        
        $noHp = $inputs['noHp'] ?? null;
        $wa = false;
        if($noHp !== null){
            $wa = filter_var($inputs['mendukungWhatsapp'],FILTER_VALIDATE_BOOLEAN);
        }

        if($inputs['namaCustomer'] !== $findData->name || $inputs['noHp'] !== $findData->phoneNumber){
            if($findData->count > 1){
                $this->save(['count' => $findData->count - 1],$idCustomer);
                return $this->create($inputs);
            }
        }
        
        $attributs = [
            'name'=>$inputs['namaCustomer'],
            'gender'=>$inputs['jenisKelamin'],
            'phoneNumber'=>$inputs['noHp'],
            'whatsapp'=> $wa,
        ];
        
        $data = $this->save($attributs, $idCustomer);
        return ['idCustomer'=>$data->id];
    }

    public function deleteById(string $idCustomer){
        $data = $this->delete($idCustomer);
        return ['sukses'=>true];
    }
}

?>