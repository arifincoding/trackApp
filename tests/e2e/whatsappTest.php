<?php

use App\Models\Service;
use App\Models\Customer;
use Illuminate\Support\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;

class whatsappTest extends TestCase
{
    use DatabaseMigrations;
    // send message
    private function create()
    {
        $this->markTestSkipped("");
        $customer = Customer::create([
            'nama' => 'ujiCoba',
            'noHp' => '6285715463861',
            'bisaWA' => true
        ]);
        $service = Service::create([
            'keluhan' => 'testing',
            'status' => 'antri',
            'idCustomer' => $customer->id,
            'idProduct' => 1,
            'butuhPersetujuan' => true,
            'konfirmasiBiaya' => false,
            'diambil' => false,
            'waktuMasuk' => Carbon::now('GMT+7'),
            'usernameCS' => '2206003'
        ]);
        return $service;
    }
    public function testShouldSendWhatsappMessage()
    {
        $this->markTestSkipped();
        $data = $this->create();
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];
        $parameters = ['pesan' => 'uji coba'];
        $this->post('/services/' . $data->id . '/chat', $parameters, $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
        Customer::where('id', $data->idCustomer)->delete();
        Service::where('id', $data->id)->delete();
    }
}