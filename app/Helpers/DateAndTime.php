<?php

namespace App\Helpers;

use DateTime;
use DateTimeZone;

class DateAndTime{
    
    public static function getDateNow($isFormat=true){
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone("Asia/Jakarta"));
        if($isFormat===true){
            return $now->format('d-m-Y');
        }
        return $now;
    }

    public static function getTimeNow(){
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone("Asia/Jakarta"));
        return $now->format('H:i');
    }

    public static function setDateFromString(string $value, string $format ='d-m-Y'){
        $date = DateTime::createFromFormat($format, $value);
        return $date;
    }
}