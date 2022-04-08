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
                $wa = filter_var($inputs['bisaWA'],FILTER_VALIDATE_BOOLEAN);
            }
            $attributs = [
                'nama'=>$inputs['namaCustomer'],
                'noHp'=>$inputs['noHp'],
                'bisaWA'=> $wa,
                'jumlahService'=>1
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
            $findData = $this->model->where('nama',$inputs['namaCustomer'])->where('noHp',$inputs['noHp'])->first();
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
        $attributs['jumlahService'] = 0;
        $findData = $this->findById($id);
        if($operator === 'plus'){
            $attributs['jumlahService'] = $findData->jumlahService + 1;
        }else if($operator === 'minus'){
            $attributs['jumlahService'] = $findData->jumlahService - 1;
        }
        $data = $this->save($attributs,$findData->id);
        return ['idCustomer'=>$data->id];
    }

    public function update(array $inputs, string $idCustomer){

        $findData = $this->findById($idCustomer);
        
        $noHp = $inputs['noHp'] ?? null;
        $wa = false;
        if($noHp !== null){
            $wa = filter_var($inputs['bisaWA'],FILTER_VALIDATE_BOOLEAN);
        }

        if($inputs['namaCustomer'] !== $findData->nama || $inputs['noHp'] !== $findData->noHp){
            if($findData->jumlahService > 1){
                $this->save(['jumlahService' => $findData->jumlahService - 1],$idCustomer);
                return $this->create($inputs);
            }
        }
        
        $attributs = [
            'nama'=>$inputs['namaCustomer'],
            'noHp'=>$inputs['noHp'],
            'bisaWA'=> $wa,
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