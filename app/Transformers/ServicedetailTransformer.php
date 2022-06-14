<?php

namespace App\Transformers;

use App\Models\Service;
use League\Fractal\TransformerAbstract;
use App\Helpers\Formatter;
use Illuminate\Support\Carbon;

class ServicedetailTransformer extends TransformerAbstract{
    public function transform(Service $data){
        $yangHarusDibayar = $data->totalBiaya;
        if($data->uangMuka !== null){
            $yangHarusDibayar = $data->totalBiaya - $data->uangMuka;
        }
        $tanggalAmbil = null;
        $jamAmbil = null;
        if($data->waktuAmbil !== null){
            $tanggalAmbil = Carbon::parse($data->waktuAmbil)->format('d-m-Y');
            $jamAmbil = Carbon::parse($data->waktuAmbil)->format('H:i');
        }
        return [
            'idService' => $data->id
            ,'idCustomer'=>$data->idCustomer
            ,'idProduk'=>$data->idProduct
            ,'kode' => $data->kode
            ,'keluhan' => $data->keluhan
            ,'status' => $data->status
            ,'totalBiaya' => $data->totalBiaya
            ,'totalBiayaString'=>Formatter::currency($data->totalBiaya)
            ,'diambil' => Formatter::boolval($data->diambil)
            ,'disetujui'=> Formatter::boolval($data->disetujui)
            ,'estimasiBiaya'=> $data->estimasiBiaya
            ,'estimasiBiayaString'=>Formatter::currency($data->estimasiBiaya)
            ,'uangMuka'=>$data->uangMuka
            ,'uangMukaString'=>Formatter::currency($data->uangMuka)
            ,'yangHarusDibayar'=> Formatter::currency($yangHarusDibayar)
            ,'tanggalMasuk'=>Carbon::parse($data->waktuMasuk)->format('d-m-Y')
            ,'jamMasuk'=>Carbon::parse($data->waktuMasuk)->format('H:i')
            ,'tanggalAmbil'=>$tanggalAmbil
            ,'jamAmbil'=>$jamAmbil
            ,'garansi'=>$data->garansi
            ,'usernameCS'=>$data->usernameCS
            ,'usernameTeknisi'=>$data->usernameTeknisi,
            'butuhPersetujuan'=> Formatter::boolval($data->butuhPersetujuan),
            'sudahKonfirmasiBiaya'=> Formatter::boolval($data->konfirmasiBiaya),
        ];
    }
}

?>