<?php

namespace App\Repositories;

use App\Repositories\Repository;
use App\Models\ServiceTrack;
use App\Helpers\DateAndTime;

class ServiceTrackRepository extends Repository{

    function __construct(ServiceTrack $model){
        parent::__construct($model);
    }

    public function getAllByIdService(int $idService){
        $attributs = ['status','judul','tanggal','jam'];
        $data = $this->model->select($attributs)->where('idService',$idService)->orderByDesc('id')->get();
        return $data->toArray();
    }

    function create(array $input, int $id){
        $attributs = [
            'judul'=> $input['pesan'],
            'status'=> $input['status'],
            'idService'=>$id,
            'tanggal'=>DateAndTime::getDateNow(),
            'jam'=>DateAndTime::getTimeNow()
        ];
        
        $data = $this->save($attributs);
        return ['idHistory'=>$data->id];
    }

    function deleteByIdService(string $id){
        $data = $this->delete($id,'idService');
        return ['sukses'=>$data];
    }
}