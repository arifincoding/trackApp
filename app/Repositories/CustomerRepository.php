<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Repositories\Repository;
use App\Exceptions\Handler;
use Illuminate\Support\Facades\DB;
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
        $findData = $this->model->where('name',$inputs['namaCustomer'])->where('phoneNumber',$inputs['noHp'])->first();
        if($findData){
            $attributs = [
                'count'=> $findData->count + 1
            ];
            $data = $this->save($attributs, $findData->id);
            return ['idCustomer'=>$data->id];
        }
        return false;
    }

    public function update(array $inputs, string $idService){
        $getIdCustomer = DB::table('services')->where('id',$idService)->first();
        
        if(!$getIdCustomer){
            throw new ModelNotFoundException();
        }

        $findData = $this->findById($getIdCustomer->idCustomer);
        
        $noHp = $inputs['noHp'] ?? null;
        $wa = false;
        if($noHp !== null){
            $wa = filter_var($inputs['mendukungWhatsapp'],FILTER_VALIDATE_BOOLEAN);
        }

        if($inputs['namaCustomer'] !== $findData->name || $inputs['noHp'] !== $findData->phoneNumber){
            if($findData->count > 1){
                $this->save(['count' => $findData->count - 1],$getIdCustomer->idCustomer);
                return $this->create($inputs);
            }
        }
        
        $attributs = [
            'name'=>$inputs['namaCustomer'],
            'gender'=>$inputs['jenisKelamin'],
            'phoneNumber'=>$inputs['noHp'],
            'whatsapp'=> $wa,
        ];
        
        $data = $this->save($attributs, $getIdCustomer->idCustomer);
        return ['idCustomer'=>$data->id];
    }

    public function deleteById($idService){
        $getIdCustomer = DB::table('services')->where('id',$idService)->first();
        if(!$getIdCustomer){
            throw new ModelNotFoundException();
        }
        $findData = $this->findById($getIdCustomer->idCustomer);
        if($findData->count > 1){
            $data = $this->save(['count' => $findData->count - 1],$getIdCustomer->idCustomer);
            return ['sukses'=>true];
        }
        $data = $this->delete($getIdCustomer->idCustomer);
        return ['sukses'=>true];
    }
}

?>