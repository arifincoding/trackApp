<?php

namespace App\Repositories;

use App\Models\Warranty;
use App\Repositories\Repository;
use App\Helpers\DateAndTime;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Support\Facades\DB;

class WarrantyRepository extends Repository{
    function __construct(Warranty $model){
        parent::__construct($model);
    }
    
    function getListDataByIdService(string $idService){
        $data = $this->model->where('idService',$idService)->orderBy('id','desc')->get();
        $arrData = [];
        foreach($data as $key=>$item){
            $arrData[$key]=[
                'idDiagnosa'=>$item->id,
                'idService'=>$item->idService,
                'kelengkapan'=>$item->completeness,
                'keluhan'=>$item->complaint,
                'cacatProduk'=>$item->productDefects,
                'catatan'=> $item->note,
                'customerService'=>$item->csName,
                'tanggalMasuk'=>$item->entryDate,
                'jamMasuk'=>$item->entryTime,
                'tanggalAmbil'=>$item->pickDate,
                'jamAmbil'=>$item->pickTime
            ];
        }
        if($arrData==[]){
            throw new ModelNotFoundException();
        }
        return $arrData;
    }

    function create(array $inputs, string $idService){
        
        $checkService = DB::table('services')->where('id',$idService)->first();
        
        if(!$checkService){
            throw new Exception('gagal membuat garansi baru, karena data service tidak ditemukan');
        }
        // else if($checkService->pickDate == null){
        //     throw new Exception('gagal membuat garansi baru,karena barang belum diambil');
        // }
        
        $checkWarranty = $this->model->where('idService',$idService)->orderBy('id','desc')->first();
        
        if($checkWarranty && $checkWarranty->pickDate == null){
            throw new Exception('gagal membuat garansi baru,karena barang belum diambil');
        }
        
        $attributs = [
            'idService'=>$idService,
            'completeness'=>$inputs['kelengkapan'],
            'complaint'=>$inputs['keluhan'],
            'productDefects'=>$inputs['cacatProduk'],
            'note'=>$inputs['catatan'],
            'entryDate'=> DateAndTime::getDateNow(),
            'entryTime'=> DateAndTime::getTimeNow(),
            'csName'=> 'arifin'
        ];
        $data = $this->save($attributs);
        return ['idGaransi'=>$data->id];
    }
    
    public function update(array $inputs, string $id){
        $attributs = [
            'completeness'=>$inputs['kelengkapan'],
            'complaint'=>$inputs['keluhan'],
            'productDefects'=>$inputs['cacatProduk'],
            'note'=>$inputs['catatan']
        ];
        $data = $this->save($attributs, $id);
        return [
            'idGaransi'=>$data->id
        ];
    }

    public function deleteById(string $id){
        $data = $this->delete($id);
        return [
            'sukses'=>$data
        ];
    }
}