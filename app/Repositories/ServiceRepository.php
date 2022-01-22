<?php

namespace App\Repositories;

use App\Models\Service;
use App\Repositories\Repository;
use App\Exceptions\Handler;
use App\Repositories\CustomerRepository;
use App\Repositories\ServiceTrackRepository;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Helpers\DateAndTime;
use Illuminate\Support\Facades\DB;

class ServiceRepository extends Repository{
    public function __construct(Service $model,CustomerRepository $customer, ServiceTrackRepository $serviceTrack, DB $query)
    {
        parent::__construct($model);
        $this->customer = $customer;
        $this->serviceTrack = $serviceTrack;
        $this->query = $query;
    }
    
    public function create(array $inputs,string $idCustomer):array
    {
        $attributs = $this->setAttributs($inputs, $idCustomer);
        $data = $this->save($attributs);
        $track = [
            'idService'=>$data->id,
            'title'=>'barang service masuk dan menunggu untuk di diagnosa',
            'status'=>'antri'
        ];
        $this->setCodeService($data->toArray());
        $this->serviceTrack->create($track);
        return ['idService'=>$data->id];
    }

    public function getListData(){
        $columns = $this->setSelectColumn();
        $data = $this->getAllWithInnerJoin('services','customers','idCustomer','id')->get($columns);
        $arrData = [];
        foreach($data as $key=>$item){
            $arrData[$key] = $this->setReturnData($item);
        }
        return ['data'=>$arrData];
    }

    public function getDataById($id){
        $columns = $this->setSelectColumn(true);
        $data = $this->getAllWithInnerJoin('services','customers','idCustomer','id')->where('services.id',$id)->first($columns);

        if(!$data){
            throw new ModelNotFoundException('data tidak ditemukan');
        }
        return $this->setReturnData($data,true);
    }

    private function setSelectColumn($first=false){
        $columns = [
            'customers.name as customerName',
            'services.name as productName',
            'phoneNumber',
            'whatsapp',
            'code',
            'category','complaint','status','totalPrice','picked',
            'services.id as idService'
        ];
        if($first === true){
            $columns2 = [
                'completeness','note','estimatePrice','price','downPayment','productDefects','entryDate','entryTime','pickDate','pickTime','warranty','csUseName','technicianUserName'
            ];
            $columns = array_merge($columns,$columns2);
        }
        return $columns;
    }

    private function setReturnData($data,$isById=false){
        $arrData = [];

        $arrData['customer'] = [
            'nama' => $data->customerName,
            'jenisKelamin'=>$data->gender,
            'noHp' => $data->phoneNumber,
            'mendukungWhatsapp' => boolval($data->whatsapp)
        ];

        $arrData['product'] = [
            'id' => $data->idService
            ,'nama' => $data->productName
            ,'kategori' => $data->category
            ,'kode' => $data->code
            ,'keluhan' => $data->complaint
            ,'status' => $data->status
            ,'totalHarga' => $data->totalPrice
            ,'diambil' => boolval($data->picked)
        ];

        if($isById === true){
            $product = [
                'kelengkapan'=>$data->completeness
                ,'cacatProduk'=>$data->productDefects
                ,'catatan'=>$data->note
                ,'estimasiHarga'=>$data->estimatePrice
                ,'harga'=>$data->price
                ,'uangMuka'=>$data->downPayment
                ,'tanggalMasuk'=>$data->entryDate
                ,'jamMasuk'=>$data->entryTime
                ,'tanggalAmbil'=>$data->pickDate
                ,'jamAmbil'=>$data->pickTime
                ,'lamaGaransi'=>$data->warranty
                ,'customerService'=>$data->csUserName
                ,'teknisi'=>$data->technicianUserName
            ];
            $arrData['product'] = array_merge($arrData['product'],$product);
        }
        return $arrData;
    }

    private function setAttributs(array $inputs,string $idCustomer){
        
        $attributs = [
            'name'=>$inputs['namaBarang'],
            'category'=>$inputs['kategori'],
            'complaint'=>$inputs['keluhan'],
            'status'=>'mulai',
            'idCustomer'=>$idCustomer,
            'specialised'=>filter_var($inputs['membutuhkanSpesialis'],FILTER_VALIDATE_BOOLEAN),
            'confirmed'=>filter_var($inputs['membutuhkanKonfirmasi'],FILTER_VALIDATE_BOOLEAN),
            'picked'=>false,
            'entryDate'=> DateAndTime::getDateNow(),
            'entryTime'=> DateAndTime::getTimeNow(),
            'csUserName'=>auth()->payload()->get('user'),
            'completeness'=> $inputs['kelengkapan'] ?? null,
            'note'=> $inputs['catatan'] ?? null,
            'downPayment'=> $inputs['uangMuka'] ?? null,
            'estimatePrice'=> $inputs['estimasiHarga'] ?? null,
            'productDefects'=> $inputs['cacatProduk'] ?? null
        ];
        return $attributs;
    }

    private function setCodeService(array $inputs){
        $dataCtgr = $this->query->table('categories')->where('title',$inputs['category'])->first();
        $date = DateAndTime::setDateFromString($inputs['entryDate']);
        $attributs = [
            'code'=>$date->format('y').$date->format('m')->$date->format('d').$dataCtgr->id.sprintf("%03d",$inputs['id'])
        ];
        $data = $this->save($attributs, $inputs['id']);
    }
}