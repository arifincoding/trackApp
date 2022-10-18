<?php

namespace App\Services;

use App\Services\Contracts\WhatsappServiceContract;
use Illuminate\Support\Facades\Http;
use App\Repositories\CustomerRepository;
use App\Repositories\ServiceRepository;
use Illuminate\Support\Facades\Log;

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
        Log::info("user is trying to accessing whatsapp qr code");
        if ($this->check() === true) {
            Log::warning("oldest signing whatsapp account session in this app found");
            $this->delete();
            Log::info("deleting oldest signing whatsapp account session in this app successfully");
        }
        $response = Http::post('http://127.0.0.1:4000/sessions/add', [
            'id' => 'owner',
            'isLegacy' => false
        ]);
        $data = $response->object();
        Log::info("user is accessing whatsapp qr code");
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
        Log::info("user trying to send a whatsapp message to customer by id service", ["id service" => $id, "data" => $inputs]);
        $findService = $this->serviceRepository->findById($id);
        Log::info("data service found for sending a whatsapp message to customer", ["id  service" => $findService->id]);
        $findCustomer = $this->customerRepository->findById($findService->idCustomer);
        Log::info("data customer found for sending a whatsapp message to customer", ["id  customer" => $findCustomer->id]);
        if ($findCustomer->bisaWA === true) {
            if ($this->check() === true) {
                Http::post('http://127.0.0.1:4000/chats/send', [
                    'id' => 'owner',
                    'receiver' => $findCustomer->noHp,
                    'message' => urldecode($inputs['pesan'])
                ]);
                Log::info("user send a whatsapp message to customer successfully", ["id customer" => $findCustomer->id]);
                return 'sukses mengirim pesan whatsapp';
            }
            Log::warning("user send a whatsapp message to customer failed caused whatsapp sign in session in this app is expired");
            return 'session kedaluarsa harap scan ulang kode qr';
        }
        Log::warning("user send a whatsapp message to customer failed caused this user is not have whatsapp", ["id customer" => $findCustomer->id]);
        return 'customer tidak memiliki whatsapp';
    }
}