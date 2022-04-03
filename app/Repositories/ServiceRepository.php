<?php

namespace App\Repositories;

use App\Models\Service;
use App\Repositories\Repository;
use App\Exceptions\Handler;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Helpers\DateAndTime;
use Illuminate\Support\Facades\DB;

class ServiceRepository extends Repository{
    public function __construct(Service $model)
    {
        parent::__construct($model);
    }
    
    public function getListDataJoinCustomer(int $limit=0, array $filters=[]){
        $columns = $this->setSelectColumn();
        $where = [
            'services.kategori' => $filters['kategori'] ?? null,
            'services.status' => $filters['status'] ?? null
        ];
        $likeWhere = [];
        $cari = $filters['cari'] ?? null;
        if($cari){
            $likeWhere = [
                'services.nama'=> $cari,
                'customers.nama'=> $cari,
                'services.kode'=> $cari,
                'customers.noHp'=> $cari
            ];
        }
        $table1= ['table'=>'services', 'key'=>'idCustomer'];
        $table2= ['table'=>'customers', 'key'=>'id'];
        $data = $this->getAllWithInnerJoin($table1,$table2,$limit,$where,$likeWhere)->get($columns);
        $arrData = [];
        foreach($data as $key=>$item){
            $arrData[$key] = $this->setReturnData($item);
        }
        return ['data'=>$arrData];
    }

    public function getDataById(string $id){
        $data = $this->findById($id);
        return $data->toArray();
    }

    public function getListDataQueue(array $responbility, int $limit=0, array $filter=[]){

        $resp = [];
        $likeWhere = [];
        $orWhere = [];
        
        $where = [
            'status'=>'antri',
            'kategori'=> $filter['kategori'] ?? null
        ];

        foreach($responbility as $item){
            array_push($resp,$item['kategori']);
        }
        
        if(isset($filter['cari'])){
            $likeWhere = [
                'kode' => $filter['cari'],
                'nama' => $filter['cari'],
            ];
        }
        
        $orWhere = ['kategori'=>$resp];
        $attributs=['id as idService','kode','nama','kategori','keluhan','status'];
        $data = $this->getWhere($limit,$where,$orWhere,$likeWhere);
        return $data->toArray();
    }

    public function getListDataMyProgress(string $username=null,int $limit=0,array $filter=[]){
        $likeWhere = [];
        $where = [
            'usernameTeknisi'=>$username,
            'status'=> $filter['status'] ?? null,
            'kategori'=> $filter['kategori'] ?? null
        ];
        $limit = $limit;
        if(isset($filter['cari'])){
            $likeWhere = [
                'nama'=>$filter['cari'],
                'kode'=>$filter['cari']
            ];
        }
        $attributs=['id as idService','kode','kategori','keluhan','status'];
        $data = $this->getWhere($attributs,$limit,$where,[],$likeWhere);
        return $data->toArray();
    }

    public function getDataJoinCustomerById($id){
        $columns = $this->setSelectColumn(true);
        $table1= ['table'=>'services', 'key'=>'idCustomer'];
        $table2= ['table'=>'customers', 'key'=>'id'];
        $where = ['services.id'=>$id];
        $data = $this->getAllWithInnerJoin($table1,$table2,0,$where)->first($columns);

        if(!$data){
            throw new ModelNotFoundException('data tidak ditemukan');
        }
        return $this->setReturnData($data,true);
    }

    public function create(array $inputs,string $idCustomer):array
    {
        $attributs = $this->setAttributs($inputs, $idCustomer);
        $data = $this->save($attributs);
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
            'diambil'=>filter_var($inputs['ambil'],FILTER_VALIDATE_BOOLEAN),
            'tanggalAmbil'=>DateAndTime::getDateNow(),
            'jamAmbil'=>DateAndTime::getTimeNow()
        ];
        $data = $this->save($attributs, $id);
        return [
            'idService'=>$data->id
        ];
    }

    public function updateConfirmCost(array $inputs, string $id){
        $attributs = [
            'konfirmasiHarga'=>filter_var($inputs['konfirmasiBiaya'],FILTER_VALIDATE_BOOLEAN)
        ];
        $data = $this->save($attributs,$id);
        return [
            'idService'=>$data->id
        ];
    }

    public function updateWarranty(array $inputs, string $id){
        $attributs = [
            'garansi'=>$inputs['garansi']
        ];
        $data = $this->save($attributs,$id);
        return [
            'idService'=>$data->id
        ];
    }

    public function updateConfirmation(array $inputs, string $id){
        $attributs = [
            'dikonfirmasi'=>$inputs['konfirmasi']
        ];
        $data = $this->save($attributs,$id);
        return [
            'idService'=>$data->id
        ];
    }

    public function updateTotalPrice(string $id, int $totalPrice){
        $attributs['totalBiaya'] = $totalPrice;
        $data = $this->save($attributs,$id);
    }

    public function updateDataStatus(array $inputs, string $id){
        $attributs = [
            'usernameTeknisi'=>auth()->payload()->get('username'),
            'status'=>$inputs['status']
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
            'customers.nama as namaCustomer',
            'services.nama as namaProduk',
            'noHp',
            'whatsapp',
            'kode',
            'kategori','keluhan','status','totalBiaya','diambil',
            'services.id as idService'
        ];
        if($first === true){
            $columns2 = [
                'kelengkapan','catatan','estimasiBiaya','biaya','uangMuka','cacatProduk','tanggalMasuk','jamMasuk','tanggalAmbil','jamAmbil','garansi','usernameCS','usernameTeknisi','butuhKonfirmasi','dikonfirmasi','konfirmasiHarga'
            ];
            $columns = array_merge($columns,$columns2);
        }
        return $columns;
    }

    private function setReturnData($data,$isById=false){
        $arrData = [];

        $arrData['customer'] = [
            'nama' => $data->namaCustomer,
            'noHp' => $data->noHp,
            'mendukungWhatsapp' => boolval($data->whatsapp)
        ];

        $arrData['product'] = [
            'id' => $data->idService
            ,'nama' => $data->namaProduk
            ,'kategori' => $data->kategori
            ,'kode' => $data->kode
            ,'keluhan' => $data->keluhan
            ,'status' => $data->status
            ,'totalHarga' => $data->totalBiaya
            ,'diambil' => boolval($data->diambil)
        ];

        if($isById === true){
            $product = [
                'kelengkapan'=>$data->kelengkapan
                ,'cacatProduk'=>$data->cacatProduk
                ,'catatan'=>$data->catatan
                ,'estimasiHarga'=>$data->estimasiBiaya
                ,'harga'=>$data->biaya
                ,'uangMuka'=>$data->uangMuka
                ,'tanggalMasuk'=>$data->tanggalMasuk
                ,'jamMasuk'=>$data->jamMasuk
                ,'tanggalAmbil'=>$data->tanggalAmbil
                ,'jamAmbil'=>$data->jamAmbil
                ,'lamaGaransi'=>$data->garansi
                ,'customerService'=>$data->usernameCS
                ,'teknisi'=>$data->usernameTeknisi,
                'membutuhkanKonfirmasi'=>boolval($data->butuhKonfirmasi),
                'sudahdikonfirmasi'=>boolval($data->dikonfirmasi),
                'sudahKonfirmasiBiaya'=>boolval($data->konfirmasiHarga),
            ];
            $arrData['product'] = array_merge($arrData['product'],$product);
        }
        return $arrData;
    }

    private function setAttributs(array $inputs,string $idCustomer, bool $isUpdate = false){
        
        $attributs = [
            'nama'=>$inputs['namaBarang'],
            'kategori'=>$inputs['kategori'],
            'keluhan'=>$inputs['keluhan'],
            'idCustomer'=>$idCustomer,
            'butuhKonfirmasi'=>filter_var($inputs['membutuhkanKonfirmasi'],FILTER_VALIDATE_BOOLEAN),
            'kelengkapan'=> $inputs['kelengkapan'] ?? null,
            'catatan'=> $inputs['catatan'] ?? null,
            'uangMuka'=> $inputs['uangMuka'] ?? null,
            'estimasiBiaya'=> $inputs['estimasiHarga'] ?? null,
            'cacatProduk'=> $inputs['cacatProduk'] ?? null
        ];
        if($isUpdate === false){
            $attributs['status']='antri';
            $attributs['konfirmasiHarga']=false;
            $attributs['diambil']=false;
            $attributs['tanggalMasuk']= DateAndTime::getDateNow();
            $attributs['jamMasuk']= DateAndTime::getTimeNow();
            $attributs['usernameCS']=  auth()->payload()->get('username');
        }
        return $attributs;
    }

    private function setCodeService(array $inputs){
        $dataCtgr = DB::table('categories')->where('nama',$inputs['kategori'])->first();
        $date = DateAndTime::setDateFromString($inputs['tanggalMasuk']);
        $attributs = [
            'code'=>$date->format('y').$date->format('m').$date->format('d').$dataCtgr->id.sprintf("%03d",$inputs['id'])
        ];
        $data = $this->save($attributs, $inputs['id']);
    }
}