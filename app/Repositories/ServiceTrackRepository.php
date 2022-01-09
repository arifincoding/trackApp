<?php

namespace App\Repositories;

use App\Repositories\Repository;
use App\Models\ServiceTrack;
use App\Helpers\DateAndTime;

class ServiceTrackRepository extends Repository{

    function __construct(ServiceTrack $model){
        parent::__construct($model);
    }

    function create(array $attributs):void{
        $attributs['date'] = DateAndTime::getDateNow();
        $attributs['time'] = DateAndTime::getTimeNow();
        $data = $this->save($attributs);
    }
}