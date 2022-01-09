<?php

namespace App\Helpers;

use DateTime;
use DateTimeZone;

class DateAndTime{
    
    public static function getDateNow(){
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone("Asia/Jakarta"));
        return $now->format('d-m-Y');
    }

    public static function getTimeNow(){
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone("Asia/Jakarta"));
        return $now->format('H:i');
    }
}