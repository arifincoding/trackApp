<?php

namespace App\Repositories;

use App\Models\Service;
use App\Repositories\Repository;
use App\Exceptions\Handler;
use App\Repositories\CustomerRepository;
use App\Repositories\DiagnosaRepository;
use App\Repositories\WarrantyRepository;
use App\Repositories\ServiceTrackRepository;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Helpers\DateAndTime;

class ServiceRepository extends Repository{
    public function __construct(Service $model,CustomerRepository $customer, DiagnosaRepository $diagnosa,WarrantyRepository $warranty,ServiceTrackRepository $serviceTrack)
    {
        parent::__construct($model);
        $this->customer = $customer;
        $this->diagnosa = $diagnosa;
        $this->warranty = $warranty;
        $this->serviceTrack = $serviceTrack;
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

    public function createDiagnosa(array $inputs,string $idService){
        $checkService = $this->model->find($idService);
        if(!$checkService){
            throw new Exception('gagal tambah data diagnosa, data service tidak ditemukan');
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
        $data = $this->diagnosa->create($attributs);
        return $data;
    }

    public function createWarranty(array $inputs, string $idService){
        $checkService = $this->model->find($idService);
        if(!$checkService){
            throw new Exception('gagal membuat garansi baru, karena data service tidak ditemukan');
        }
        if($checkService->pickDate == null){
            throw new Exception('gagal membuat garansi baru,karena barang belum diambil');
        }
        $data = $this->warranty->create($inputs,$idService);
        return $data;
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
            'idCategory','complaint','status','totalPrice','picked',
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
            ,'idKategori' => $data->idCategory
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
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone("Asia/Jakarta"));
        $attributs = [
            'name'=>$inputs['namaBarang'],
            'idCategory'=>$inputs['idKategori'],
            'complaint'=>$inputs['keluhan'],
            'status'=>'mulai',
            'idCustomer'=>$idCustomer,
            'specialised'=>filter_var($inputs['membutuhkanSpesialis'],FILTER_VALIDATE_BOOLEAN),
            'confirmed'=>filter_var($inputs['membutuhkanKonfirmasi'],FILTER_VALIDATE_BOOLEAN),
            'picked'=>false,
            'entryDate'=> $now->format("d-m-Y"),
            'entryTime'=> $now->format("H:i"),
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
        $date = DateAndTime::setDateFromString($inputs['entryDate']);
        $attributs = [
            'code'=>$date->format('y').$date->format('m')->$date->format('d').$inputs['idCategory'].sprintf("%03d",$inputs['id'])
        ];
        $data = $this->save($attributs, $inputs['id']);
    }
}