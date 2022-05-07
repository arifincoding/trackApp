<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class WhatsappController extends Controller{

    public function scan(){
        if($this->check() === true){
            $this->delete();
        }
        $response = Http::post('http://127.0.0.1:4000/sessions/add',[
            'id'=>'owner',
            'isLegacy'=>false
        ]);
        $data = $response->object();
        $qr = $data->data->qr;
        return $this->jsonSuccess('sukses',200,['qr'=>$qr]);
    }

    private function check(){
        $response = Http::get('http://127.0.0.1:4000/sessions/find/owner');
        $data = $response->object();
        return $data->success;
    }

    private function delete(){
        $response = Http::delete('http://127.0.0.1:4000/sessions/delete/owner');
        return $response->object()->success;
    }

    public function chat(){
        $response = Http::post('http://127.0.0.1:4000/chats/send',[
                'id'=>'owner',
                'receiver'=>'6285715463861',
                'message'=>'woe'
        ]);
        return $this->jsonSuccess('sukses',200,$response->object());
    }
}