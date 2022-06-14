<?php

namespace App\Repositories;

use App\Models\Service;
use App\Repositories\Repository;
use Illuminate\Support\Carbon;
use App\Helpers\Formatter;

class ServiceRepository extends Repository{
    public function __construct(Service $model)
    {
        parent::__construct($model);
    }
    
    public function getListDataJoinCustomer(int $limit=0, array $inputs=[]){
        $data = $this->model->with('customer','product');
        // filter status service
        if(isset($inputs['status'])){
            $data->where('status',$inputs['status']);
        }
        // filter kategori product
        if(isset($inputs['kategori'])){
            $data->whereHas('product', function ($q) use($inputs){
                $q->where('kategori',$inputs['kategori']);
            });
        }
        //filter cari
        if(isset($inputs['cari'])){
            $data->where(function ($q) use($inputs){
                $q->orWhere('kode','LIKE','%'.$inputs['cari'].'%');
            });
            $data->orWhereHas('customer',function ($q) use($inputs){
                $q->where('nama','LIKE','%'.$inputs['cari'].'%');
                $q->orWhere('noHp','LIKE','%'.$inputs['cari'].'%');
            });
            $data->orWhereHas('product', function ($q) use($inputs){
                $q->where('nama','LIKE','%'.$inputs['cari'].'%');
            });
        }
        return $data->get();
    }

    public function getDataById(string $id){
        $data = $this->findById($id);
        return $data->toArray();
    }

    public function findDataById(int $id){
        return $this->findById($id);
    }

    public function getListDataQueue(array $responbility, int $limit=0, array $inputs=[]){
        $resp = [];
        foreach($responbility as $item){
            array_push($resp,$item->kategori);
        }
        $data = $this->model->with('product')->where('status','antri');
        $data->whereHas('product',function ($q) use($resp){
            foreach($resp as $item){
                $q->orWhere('kategori',$item);
            }
        });
        if(isset($inputs['kategori'])){
            $data->whereHas('product',function ($q) use($inputs){
                $q->where('kategori',$inputs['kategori']);
            });
        }
        if(isset($inputs['cari'])){
            $data->where('kode','LIKE','%'.$inputs['cari'].'%');
            $data->orWhereHas('product',function ($q) use($inputs){
                $q->where('nama','LIKE','%'.$inputs['cari'].'%');
            });
        }
        return $data->get();
    }

    public function getListDataMyProgress(string $username=null,int $limit=0,array $inputs=[]){
        $data = $this->model->with('product')->where('usernameTeknisi',$username);
        if(isset($inputs['status'])){
            $data->where('status',$inputs['status']);
        }
        if(isset($inputs['kategori'])){
            $data->whereHas('product',function ($q) use($inputs){
                $q->where('kategori',$inputs['kategori']);
            });
        }
        if(isset($inputs['cari'])){
            $data->where('kode','LIKE','%'.$inputs['cari'].'%');
            $data->orWhereHas('product',function ($q) use ($inputs){
                $q->where('nama','LIKE','%'.$inputs['cari'].'%');
            });
        }
        return $data->get();
    }

    public function getDataByCode(string $code){
        return $this->model->with('product','kerusakan','riwayat')->where('kode',$code)->first();
    }

    public function create(array $attributs):array
    {
        $attributs['status']='antri';
        $attributs['konfirmasiBiaya']=false;
        $attributs['diambil']=false;
        $attributs['disetujui']= $attributs['butuhPersetujuan'] ? null : true;
        $attributs['waktuMasuk']= Carbon::now('GMT+7');
        $attributs['usernameCS']=  auth()->payload()->get('username');
        $data = $this->save($attributs);
        return ['idService'=>$data->id];
    }

    public function setCodeService(int $idService){
        $date = Carbon::now('GMT+7');
        $attributs = [
            'kode'=>$date->format('y').$date->format('m').$date->format('d').sprintf("%03d",$idService)
        ];
        $data = $this->save($attributs, $idService);
    }

    public function update(array $attributs,$id):array{
        $data = $this->save($attributs,$id);
        return [
            'idService'=>$data->id
        ];
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
            'waktuAmbil'=>Carbon::now('GMT+7')
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
}