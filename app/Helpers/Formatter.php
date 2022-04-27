<?php

namespace App\Helpers;

class Formatter {
    public static function currency($value){
        return is_null($value) ? null : 'Rp. '.number_format($value,0,',','.');
    }

    public static function boolval($value){
        return is_null($value) ? null : boolval($value);
    }
}