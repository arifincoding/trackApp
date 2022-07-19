<?php

namespace App\Repositories;

use App\Models\Service;
use App\Repositories\Repository;
use Illuminate\Support\Carbon;

class ServiceRepository extends Repository{
    public function __construct(Service $model)
    {
        parent::__construct($model);
    }
    
    public function getListData(int $limit=0, array $inputs=[]){
        $data = $this->model->with('klien','produk')->orderByDesc('id');
        // filter status service
        if(isset($inputs['status'])){
            $data->where('status',$inputs['status']);
        }
        // filter kategori produk
        if(isset($inputs['kategori'])){
            $data->whereHas('produk', function ($q) use($inputs){
                $q->where('kategori',$inputs['kategori']);
            });
        }
        //filter cari
        if(isset($inputs['cari'])){
            $data->where(function ($q) use($inputs){
                $q->orWhere('kode','LIKE','%'.$inputs['cari'].'%');
            });
            $data->orWhereHas('klien',function ($q) use($inputs){
                $q->where('nama','LIKE','%'.$inputs['cari'].'%');
                $q->orWhere('noHp','LIKE','%'.$inputs['cari'].'%');
            });
            $data->orWhereHas('produk', function ($q) use($inputs){
                $q->where('nama','LIKE','%'.$inputs['cari'].'%');
            });
        }
        return $data->get();
    }

    public function getDataWithRelationById(int $id){
        return $this->model->with(['klien','produk','kerusakan'=>function($q){$q->orderByDesc('id');}])->where('id',$id)->first();
    }
    public function findDataById(int $id){
        return $this->findById($id);
    }

    public function getListDataQueue($responbility, int $limit=0, array $inputs=[]){
        $resp = [];
        foreach($responbility as $item){
            array_push($resp,$item->kategori->nama);
        }
        $data = $this->model->with('produk')->where('status','antri')->orderByDesc('id');
        $data->whereHas('produk',function ($q) use($resp){
            foreach($resp as $item){
                $q->orWhere('kategori',$item);
            }
        });
        if(isset($inputs['kategori'])){
            $data->whereHas('produk',function ($q) use($inputs){
                $q->where('kategori',$inputs['kategori']);
            });
        }
        if(isset($inputs['cari'])){
            $data->where('kode','LIKE','%'.$inputs['cari'].'%');
            $data->orWhereHas('produk',function ($q) use($inputs){
                $q->where('nama','LIKE','%'.$inputs['cari'].'%');
            });
        }
        return $data->get();
    }

    public function getListDataMyProgress(string $username=null,int $limit=0,array $inputs=[]){
        $data = $this->model->with('produk')->where('usernameTeknisi',$username)->orderByDesc('id');
        if(isset($inputs['status'])){
            $data->where('status',$inputs['status']);
        }
        if(isset($inputs['kategori'])){
            $data->whereHas('produk',function ($q) use($inputs){
                $q->where('kategori',$inputs['kategori']);
            });
        }
        if(isset($inputs['cari'])){
            $data->where('kode','LIKE','%'.$inputs['cari'].'%');
            $data->orWhereHas('produk',function ($q) use ($inputs){
                $q->where('nama','LIKE','%'.$inputs['cari'].'%');
            });
        }
        return $data->get();
    }

    public function getDataByCode(string $code){
        return $this->model->with(['produk','kerusakan'=>function ($q){
            $q->orderByDesc('id');
        },'riwayat'=>function ($q){
            $q->orderByDesc('id');
        }])->where('kode',$code)->first();
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

    public function update(array $attributs,int $id):array
    {
        $data = $this->save($attributs,$id);
        return [
            'idService'=>$data->id
        ];
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

    public function deleteById(string $id){
        $data = $this->delete($id);
        return ['sukses'=>$data];
    }
}