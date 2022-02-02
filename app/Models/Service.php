<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model{
    protected $fillable = [
        'name','category','complaint','productDefects','completeness','note','downPayment','status','estimatePrice','idCustomer','needConfirm','confirmed','confirmCost','picked','entryDate','pickDate','pickTime','entryTime','csUserName','technicianUserName'
    ];
}

?>