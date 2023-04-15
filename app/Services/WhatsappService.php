<?php

namespace App\Services;

use App\Services\Contracts\WhatsappServiceContract;
use Illuminate\Support\Facades\Http;
use App\Repositories\CustomerRepository;
use App\Repositories\ServiceRepository;

class WhatsappService implements WhatsappServiceContract
{
    private ServiceRepository $serviceRepository;
    private CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customer, ServiceRepository $service)
    {
        $this->serviceRepository = $service;
        $this->customerRepository = $customer;
    }

    public function scanQr(): string
    {
        $this->check() === true ? $this->delete() : null;
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
        $findService = $this->serviceRepository->findById($id);
        $findCustomer = $this->customerRepository->findById($findService->idCustomer);
        if ($findCustomer->is_whatsapp === true) {
            if ($this->check() === true) {
                Http::post('http://127.0.0.1:4000/chats/send', [
                    'id' => 'owner',
                    'receiver' => $findCustomer->telp,
                    'message' => urldecode($inputs['message'])
                ]);
                return 'sukses mengirim pesan whatsapp';
            }
            abort(400, 'session kedaluarsa harap scan ulang kode qr');
        }
        abort(400, 'customer tidak memiliki whatsapp');
    }
}
