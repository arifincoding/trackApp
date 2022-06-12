<?php

namespace App\Repositories;

use App\Repositories\Repository;
use App\Models\History;
use Illuminate\Support\Carbon;

class HistoryRepository extends Repository{

    function __construct(History $model)
    {
        parent::__construct($model);
    }

    public function getAllByIdService(int $idService):array
    {
        $attributs = ['status','pesan','waktu'];
        $data = $this->model->select($attributs)->where('idService',$idService)->orderByDesc('id')->get();
        $arrData = [];
        foreach($data as $key=>$item){
            $arrData[$key]= [
                'status'=> $item->status,
                'pesan'=> $item->pesan,
                'tanggal'=> $item->waktu->format('d-m-Y'),
                'jam'=> $item->waktu->format('H:i')
            ];
        }
        return $arrData;
    }

    function create(array $input, int $id):array
    {
        $attributs = [
            'pesan'=> $input['pesan'],
            'status'=> $input['status'],
            'idService'=>$id,
            'waktu'=>Carbon::now('GMT+7')
        ];
        
        $data = $this->save($attributs);
        return [
            'idRiwayat'=>$data->id
        ];
    }

    function deleteByIdService(string $id):array
    {
        $data = $this->delete($id,'idService');
        return ['sukses'=>$data];
    }
}