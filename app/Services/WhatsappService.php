<?php

namespace App\Services;

use App\Services\Contracts\WhatsappServiceContract;
use Illuminate\Support\Facades\Http;
use App\Repositories\CustomerRepository;
use App\Repositories\ServiceRepository;

class WhatsappService implements WhatsappServiceContract
{
    private $serviceRepository;
    private $customerRepository;

    public function __construct(CustomerRepository $customer, ServiceRepository $service)
    {
        $this->serviceRepository = $service;
        $this->customerRepository = $customer;
    }

    public function scanQr(): string
    {
        if ($this->check() === true) {
            $this->delete();
        }
        $response = Http::post('http://127.0.0.1:4000/sessions/add', [
            'id' => 'owner',
            'isLegacy' => false
        ]);
        $data = $response->object();
        return $data->data->qr;
    }

    private function check(): bool
    {
        $response = Http::get('http://127.0.0.1:4000/sessions/find/owner');
        $data = $response->object();
        return $data->success;
    }

    private function delete(): bool
    {
        $response = Http::delete('http://127.0.0.1:4000/sessions/delete/owner');
        return $response->object()->success;
    }

    public function sendMessage(array $inputs, int $id): string
    {
        $findService = $this->serviceRepository->findDataById($id);
        $findCustomer = $this->customerRepository->getDataById($findService->idCustomer);
        if ($findCustomer['bisaWA'] === true) {
            if ($this->check() === true) {
                Http::post('http://127.0.0.1:4000/chats/send', [
                    'id' => 'owner',
                    'receiver' => $findCustomer['noHp'],
                    'message' => urldecode($inputs['pesan'])
                ]);
                return 'sukses mengirim pesan whatsapp';
            }
            return 'session kedaluarsa harap scan ulang kode qr';
        }
        return 'customer tidak memiliki whatsapp';
    }
}