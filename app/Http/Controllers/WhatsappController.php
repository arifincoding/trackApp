<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Repositories\CustomerRepository;
use App\Repositories\ServiceRepository;

class WhatsappController extends Controller{


    private $customerRepository;
    private $serviceRepository;

    public function __construct(CustomerRepository $customer, ServiceRepository $service){
        $this->customerRepository = $customer;
        $this->serviceRepository = $service;
    }

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

    public function chat(Request $request,$id){
        $findService = $this->serviceRepository->findDataById($id);
        $findCustomer = $this->customerRepository->findDataById($findService->idCustomer);
        if($findCustomer['bisaWA'] === 1){
            if($this->check() === true){
                $response = Http::post('http://127.0.0.1:4000/chats/send',[
                    'id'=>'owner',
                    'receiver'=>$findCustomer['noHp'],
                    'message'=>urldecode($request->input('pesan'))
                ]);
                return $this->jsonMessageOnly('sukses mengirim pesan whatsapp');
            }
            return $this->jsonMessageOnly('session kedaluarsa harap scan ulang kode qr'); 
        }
        return $this->jsonMessageOnly('customer tidak memiliki whatsapp');
    }
}