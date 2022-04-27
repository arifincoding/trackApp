<?php

namespace App\Repositories;

use App\Models\Service;
use App\Repositories\Repository;
use App\Exceptions\Handler;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Helpers\DateAndTime;
use Illuminate\Support\Facades\DB;
use App\Helpers\Formatter;

class ServiceRepository extends Repository{
    public function __construct(Service $model)
    {
        parent::__construct($model);
    }
    
    public function getListDataJoinCustomer(int $limit=0, array $inputs=[]){
        $columns = $this->setSelectColumn();
        $filters = [
            'limit'=>$limit,
            'where'=>[
                'services.kategori' => $inputs['kategori'] ?? null,
                'services.status' => $inputs['status'] ?? null
            ]
        ];
        $cari = $inputs['cari'] ?? null;
        if($cari){
            $filters['likeWhere'] = [
                'services.nama'=> $cari,
                'customers.nama'=> $cari,
                'services.kode'=> $cari,
                'customers.noHp'=> $cari
            ];
        }
        $table1= ['table'=>'services', 'key'=>'idCustomer'];
        $table2= ['table'=>'customers', 'key'=>'id'];
        $data = $this->getAllWithInnerJoin($table1,$table2,$filters)->orderByDesc('services.id')->get($columns);
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

    public function getListDataQueue(array $responbility, int $limit=0, array $inputs=[]){

        $resp = [];
        foreach($responbility as $item){
            array_push($resp,$item->kategori);
        }
        $filters=[
            'limit'=>$limit,
            'where'=>[
                'status'=>'antri',
                'kategori'=> $inputs['kategori'] ?? null
            ],
            'orWhere'=>['kategori'=>$resp]
        ];
        $cari = $inputs['cari'] ?? null;
        if($cari){
            $filters['likeWhere'] = [
                'kode' => $cari,
                'nama' => $cari,
            ];
        }
        $attributs=['id as idService','kode','nama','kategori','keluhan','status'];
        $data = $this->getWhere($attributs,$filters);
        return $data->toArray();
    }

    public function getListDataMyProgress(string $username=null,int $limit=0,array $inputs=[]){
        $filters = [
            'limit'=>$limit,
            'where'=>[
                'usernameTeknisi'=>$username,
                'status'=> $inputs['status'] ?? null,
                'kategori'=> $inputs['kategori'] ?? null
            ]
        ];
        $cari = $inputs['cari'] ?? null;
        if($cari){
            $filters['likeWhere'] = [
                'nama'=>$cari,
                'kode'=>$cari
            ];
        }
        $attributs=['id as idService','kode','nama','kategori','keluhan','status','dikonfirmasi'];
        $data = $this->getWhere($attributs,$filters);
        return $data->toArray();
    }

    public function getDataJoinCustomerById($id){
        $columns = $this->setSelectColumn(true);
        $table1= ['table'=>'services', 'key'=>'idCustomer'];
        $table2= ['table'=>'customers', 'key'=>'id'];
        $filters = ['where'=>['services.id'=>$id]];
        $data = $this->getAllWithInnerJoin($table1,$table2,$filters)->first($columns);

        if(!$data){
            throw new ModelNotFoundException('data tidak ditemukan');
        }
        return $this->setReturnData($data,true);
    }

    public function getDataByCode(string $code){
        $attributs = ['id as idService','kode','nama','kategori','status','dikonfirmasi','totalBiaya'];
        $data = $this->model->select($attributs)->where('kode',$code)->firstOrFail();
        $data->dikonfirmasi = Formatter::boolval($data->dikonfirmasi);
        $data->totalBiaya = Formatter::currency($data->totalBiaya);
        $data->uangMuka = Formatter::currency($data->uangMuka);
        return $data->toArray();
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

    public function updateWarranty(array $attributs, string $id=null){
        $data = $this->save($attributs,$id);
        return ['idService'=>$data->id];
    }

    public function setDataTake(string $id){
        $attributs = [
            'diambil'=>true,
            'tanggalAmbil'=>DateAndTime::getDateNow(),
            'jamAmbil'=>DateAndTime::getTimeNow()
        ];
        $data = $this->save($attributs, $id);
        return [
            'idService'=>$data->id
        ];
    }

    public function setDataConfirmCost(string $id){
        $attributs = [
            'konfirmasibiaya'=>true
        ];
        $data = $this->save($attributs,$id);
        return [
            'idService'=>$data->id
        ];
    }

    public function setDataConfirmation(string $id,array $attributs){
        $data = $this->save($attributs,$id);
        return [
            'idService'=>$data->id
        ];
    }

    public function updateTotalPrice(string $id, int $totalCost){
        $attributs['totalBiaya'] = $totalCost;
        $data = $this->save($attributs,$id);
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
            'bisaWA',
            'kode',
            'kategori','keluhan','status','totalBiaya','diambil',
            'services.id as idService'
        ];
        if($first === true){
            $columns2 = [
                'kelengkapan','catatan','estimasiBiaya','uangMuka','cacatProduk','tanggalMasuk','jamMasuk','tanggalAmbil','jamAmbil','garansi','usernameCS','usernameTeknisi','butuhKonfirmasi','dikonfirmasi','konfirmasiBiaya'
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
            'bisaWA' => Formatter::boolval($data->bisaWA)
        ];

        $arrData['product'] = [
            'id' => $data->idService
            ,'nama' => $data->namaProduk
            ,'kategori' => $data->kategori
            ,'kode' => $data->kode
            ,'keluhan' => $data->keluhan
            ,'status' => $data->status
            ,'totalBiaya' => $data->totalBiaya
            ,'diambil' => Formatter::boolval($data->diambil)
        ];

        if($isById === true){
            $yangHarusDibayar = $data->totalBiaya - $data->uangMuka;
            $product = [
                'kelengkapan'=>$data->kelengkapan
                ,'totalBiayaString'=>Formatter::currency($data->totalBiaya)
                ,'cacatProduk'=>$data->cacatProduk
                ,'catatan'=>$data->catatan
                ,'estimasiBiaya'=>Formatter::currency($data->estimasiBiaya)
                ,'uangMuka'=>$data->uangMuka
                ,'uangMukaString'=>Formatter::currency($data->uangMuka)
                ,'yangHarusDiBayar'=> Formatter::currency($yangHarusDibayar)
                ,'tanggalMasuk'=>$data->tanggalMasuk
                ,'jamMasuk'=>$data->jamMasuk
                ,'tanggalAmbil'=>$data->tanggalAmbil
                ,'jamAmbil'=>$data->jamAmbil
                ,'garansi'=>$data->garansi
                ,'usernameCS'=>$data->usernameCS
                ,'usernameTeknisi'=>$data->usernameTeknisi,
                'butuhKonfirmasi'=> Formatter::boolval($data->butuhKonfirmasi),
                'sudahdikonfirmasi'=> Formatter::boolval($data->dikonfirmasi),
                'sudahKonfirmasiBiaya'=> Formatter::boolval($data->konfirmasiBiaya),
            ];
            $arrData['product'] = array_merge($arrData['product'],$product);
        }
        return $arrData;
    }

    private function setAttributs(array $inputs,string $idCustomer, bool $isUpdate = false){
        $confirmed=null;
        if($inputs['butuhKonfirmasi'] === false){
            $confirmed=true;
        }
        $attributs = [
            'nama'=>$inputs['namaProduk'],
            'kategori'=>$inputs['kategori'],
            'keluhan'=>$inputs['keluhan'],
            'idCustomer'=>$idCustomer,
            'butuhKonfirmasi'=>$inputs['butuhKonfirmasi'],
            'kelengkapan'=> $inputs['kelengkapan'] ?? null,
            'catatan'=> $inputs['catatan'] ?? null,
            'uangMuka'=> $inputs['uangMuka'] ?? null,
            'estimasiBiaya'=> $inputs['estimasiBiaya'] ?? null,
            'cacatProduk'=> $inputs['cacatProduk'] ?? null
        ];
        if($isUpdate === false){
            $attributs['status']='antri';
            $attributs['konfirmasiBiaya']=false;
            $attributs['diambil']=false;
            $attributs['dikonfirmasi']=$confirmed;
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
            'kode'=>$date->format('y').$date->format('m').$date->format('d').$dataCtgr->id.sprintf("%03d",$inputs['id'])
        ];
        $data = $this->save($attributs, $inputs['id']);
    }
}