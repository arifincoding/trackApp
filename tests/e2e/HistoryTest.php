<?php

use App\Models\Service;
use Laravel\Lumen\Testing\DatabaseTransactions;

class HistoryTest extends TestCase
{

    use DatabaseTransactions;

    // create
    public function testShouldCreateHistory()
    {
        $service = Service::factory()->create();
        $params = [
            'status' => 'perbaikan selesai',
            'message' => 'perbaikan selesai cuy'
        ];
        $this->post("/services/$service->id/history", $params, ['Authorization' => 'Bearer ' . $this->getToken('teknisi')]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'history_id'
            ]
        ]);
    }
}
