<?php

namespace App\Repositories;

use App\Models\Warranty;
use App\Repositories\Repository;
use DateTime;
use DateTimeZone;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class WarrantyRepository extends Repository{
    function __construct(Warranty $model){
        parent::__construct($model);
    }
    function create(array $inputs, string $idService){
        $find = $this->model->where('idService',$idService)->orderBy('id','desc')->first();
        if($find && $find->pickDate == null){
            throw new Exception('gagal membuat garansi baru,karena barang belum diambil');
        }
        $now = new DateTime();
        $now->setTimeZone(new DateTimeZone('Asia/Jakarta'));
        $attributs = [
            'idService'=>$idService,
            'completeness'=>$inputs['kelengkapan'],
            'complaint'=>$inputs['keluhan'],
            'productDefects'=>$inputs['cacatProduk'],
            'note'=>$inputs['catatan'],
            'entryDate'=> $now->format('d-m-Y'),
            'entryTime'=> $now->format('H:i'),
            'csName'=> $inputs['customerService']
        ];
        $data = $this->save($attributs);
        return ['idGaransi'=>$data->id];
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
}