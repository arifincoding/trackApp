<?php

namespace App\Repositories;

use App\Repositories\Repository;
use App\Models\Service;
use App\Models\Diagnosa;
use App\Exceptions\Handler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class DiagnosaRepository extends Repository{
    function __construct(Diagnosa $model, Service $service){
        parent::__construct($model);
        $this->service = $service;
    }

    function create(array $inputs,string $idService){
        $checkService = DB::table('services')->where('id',$idService)->first();
        if(!$checkService){
            throw new ModelNotFoundException();
        }
        $confirm = null;
        if($checkService->confirmed === 0){
            $confirm = true;
        }
        $attributs = [
            'title'=>$inputs['judul'],
            'idService'=>$idService,
            'status'=>'antri',
            'confirmed'=>$confirm,
        ];
        $data = $this->save($attributs);
        return ['idDiagnosa'=>$data->id];
    }

    function getListDataByIdService(string $idService){
        $data = $this->model->where('idService',$idService)->get();
        $arrData = [];
        foreach($data as $key=>$item){
            $arrData[$key]=[
                'idService' => $item->idService,
                'idDiagnosa' => $item->id,
                'judul'=>$item->title,
                'status'=>$item->status,
                'konfirmasi'=>$item->confirmed
            ];
        }
        if($arrData == []){
            throw new ModelNotFoundException();
        }
        return $arrData;
    }

    function getDataById(string $id){
        $data = $this->findById($id);
        return [
            'judul'=>$data->title,
            'status'=>$data->status,
            'harga'=>$data->price,
            'konfirmasi'=>$data->confirmed
        ];
    }

    function update(array $inputs, string $id){
        $attributs = [
            'title'=> $inputs['judul'],
            'status'=> $inputs['status']
        ];

        $data = $this->save($attributs,$id);
        return [
            'idDiagnosa'=>$data->id,
            'idService'=>$data->idService
        ];
    }

    function updateStatus(array $inputs, string $id){
        $attributs = [
            'status'=>$inputs['status']
        ];
        $data = $this->save($attributs, $id);
        return [
            'idDiagnosa'=>$data->id
        ];
    }

    function updateCost(array $inputs, string $id){
        $find = $this->findById($id);
        $service = $this->service->where('id',$find->idService)->first();
        $attributs=[];
        if($find->price !== null){
            $attributs['totalPrice'] = $service->totalPrice + ($inputs['biaya'] - $find->price);
        }
        else if($service->totalPrice !== null){
            $attributs['totalPrice'] = $service->totalPrice + $inputs['biaya'];
        }else{
            $attributs['totalPrice'] = $inputs['biaya'];
        }
        $this->service->where('id',$service->id)->update($attributs);
        $data = $this->save(['price'=>$inputs['biaya']],$id);
        return [
            'idDiagnosa'=>$data->id
        ];
    }

    function deleteById(string $id){
        $data = $this->delete($id);
        return[
            'sukses'=>$data
        ];
    }
}

?>