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
    public function __construct(Service $model,CustomerRepository $customer, ServiceTrackRepository $serviceTrack)
    {
        parent::__construct($model);
        $this->customer = $customer;
        $this->serviceTrack = $serviceTrack;
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

    public function getDataQueue($resp){
        $responbility = $resp->getListDataByUsername(auth()->payload()->get('username'));
        if($responbility !== false){
        $data = $this->getAll()->where('status','antri')->where(function ($q) use ($responbility){
            $this->setFilterDataQueue($q,$responbility);
        })->get();
        $arrData = [];
        foreach($data as $key=>$item){
            $arrData[$key]=[
                'idService'=>$item->id,
                'kode'=>$item->code,
                'nama'=>$item->name,
                'kategori'=>$item->category,
                'keluhan'=>$item->complaint,
                'status'=>$item->status
            ];
        }
        if($arrData === []){
            throw new ModelNotFoundException();
        }
        return $arrData;
        }
        throw new ModelNotFoundException();
    }

    public function getListDataByTechUsername(string $username){
        $data = $this->getAll()->where('technicianUserName',$username)->get();
        $arrData = [];
        foreach($data as $key=>$item){
            $arrData[$key]=[
                'idService'=>$item->id,
                'kode'=>$item->code,
                'nama'=>$item->name,
                'kategori'=>$item->category,
                'keluhan'=>$item->complaint,
                'status'=>$item->status
            ];
        }
        if($arrData === []){
            throw new ModelNotFoundException();
        }
        return $arrData;
    }

    public function updateDataStatus(array $inputs, string $id){
        $this->addServiceTrack($inputs['status'],$id);
        $attributs = [
            'technicianUserName'=>auth()->payload()->get('username'),
            'status'=>$inputs['status']
        ];
        $data = $this->save($attributs,$id);
        return [
            'idService'=>$data->id
        ];
    }

    public function getDataById($id){
        $columns = $this->setSelectColumn(true);
        $data = $this->getAllWithInnerJoin('services','customers','idCustomer','id')->where('services.id',$id)->first($columns);

        if(!$data){
            throw new ModelNotFoundException('data tidak ditemukan');
        }
        return $this->setReturnData($data,true);
    }

    public function create(array $inputs,string $idCustomer):array
    {
        $attributs = $this->setAttributs($inputs, $idCustomer);
        $data = $this->save($attributs);
        $this->addServiceTrack($data->status,$data->id);
        $this->setCodeService($data->toArray());
        return ['idService'=>$data->id];
    }

    public function update(array $inputs, string $idCustomer,$id):array{
        $attributs = $this->setAttributs($inputs,$idCustomer,true);
        $data = $this->save($attributs,$id);
        return ['idService'=>$data->id];
    }

    public function updateTake(array $inputs, string $id){
        $attributs = [
            'picked'=>filter_var($inputs['ambil'],FILTER_VALIDATE_BOOLEAN),
            'pickDate'=>DateAndTime::getDateNow(),
            'pickTime'=>DateAndTime::getTimeNow()
        ];
        $data = $this->save($attributs, $id);
        $this->addServiceTrack('diambil',$id);
        return [
            'idService'=>$data->id
        ];
    }

    public function updateConfirmCost(array $inputs, string $id){
        $attributs = [
            'confirmCost'=>filter_var($inputs['konfirmasiBiaya'],FILTER_VALIDATE_BOOLEAN)
        ];
        $data = $this->save($attributs,$id);
        return [
            'idService'=>$data->id
        ];
    }

    public function updateWarranty(array $inputs, string $id){
        $attributs = [
            'warranty'=>$inputs['garansi']
        ];
        $data = $this->save($attributs,$id);
        return [
            'idService'=>$data->id
        ];
    }

    public function deleteById(string $id){
        $data = $this->delete($id);
        return ['sukses'=>$data];
    }
    
    // private function
    
    private function setSelectColumn($first=false){
        $columns = [
            'customers.name as customerName',
            'services.name as productName',
            'phoneNumber',
            'whatsapp','gender',
            'code',
            'category','complaint','status','totalPrice','picked',
            'services.id as idService'
        ];
        if($first === true){
            $columns2 = [
                'completeness','note','estimatePrice','price','downPayment','productDefects','entryDate','entryTime','pickDate','pickTime','warranty','csUserName','technicianUserName','needConfirm','confirmed','confirmCost'
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
                ,'teknisi'=>$data->technicianUserName,
                'membutuhkanKonfirmasi'=>boolval($data->needConfirm),
                'sudahdikonfirmasi'=>boolval($data->confirmed),
                'sudahKonfirmasiBiaya'=>boolval($data->confirmCost),
            ];
            $arrData['product'] = array_merge($arrData['product'],$product);
        }
        return $arrData;
    }

    private function setAttributs(array $inputs,string $idCustomer, bool $isUpdate = false){
        
        $attributs = [
            'name'=>$inputs['namaBarang'],
            'category'=>$inputs['kategori'],
            'complaint'=>$inputs['keluhan'],
            'idCustomer'=>$idCustomer,
            'needConfirm'=>filter_var($inputs['membutuhkanKonfirmasi'],FILTER_VALIDATE_BOOLEAN),
            'completeness'=> $inputs['kelengkapan'] ?? null,
            'note'=> $inputs['catatan'] ?? null,
            'downPayment'=> $inputs['uangMuka'] ?? null,
            'estimatePrice'=> $inputs['estimasiHarga'] ?? null,
            'productDefects'=> $inputs['cacatProduk'] ?? null
        ];
        if($isUpdate === false){
            $attributs['status']='antri';
            $attributs['confirmed']=false;
            $attributs['confirmCost']=false;
            $attributs['picked']=false;
            $attributs['entryDate']= DateAndTime::getDateNow();
            $attributs['entryTime']= DateAndTime::getTimeNow();
            $attributs['csUserName']=  auth()->payload()->get('username');
        }
        return $attributs;
    }

    private function setCodeService(array $inputs){
        $dataCtgr = DB::table('categories')->where('title',$inputs['category'])->first();
        $date = DateAndTime::setDateFromString($inputs['entryDate']);
        $attributs = [
            'code'=>$date->format('y').$date->format('m').$date->format('d').$dataCtgr->id.sprintf("%03d",$inputs['id'])
        ];
        $data = $this->save($attributs, $inputs['id']);
    }

    private function setFilterDataQueue($q,$responbility){
        foreach($responbility as $item){
            $q->orWhere('category',$item['kategori']);
        }
    }
    
    private function addServiceTrack(string $status, string $id){
        $message = '';
        $service = DB::table('services')->where('id',$id)->first();
        if($status=='antri'){
            $message = 'barang service masuk dan menunggu untuk di diagnosa';
        }else if($status === 'diagnosa'){
            $message = $service->category.' anda sedang dalam proses diagnosa';
        }else if($status ==  'selesai diagnosa'){
            $message = $service->category.' anda selesai di diagnosa';
        }else if($status ==  'proses'){
            $message = $service->category.' anda sedang dalam proses perbaikan';
        }else if($status == 'selesai'){
            $message = $service->category.' anda telah selesai diperbaiki';
        }else if($status == 'diambil'){
            $message = $service->category.' anda telah diambil';
        }
        $attributs = [
            'idService'=>$id,
            'title'=>$message,
            'status'=>$status
        ];
        $this->serviceTrack->create($attributs);
    }
}