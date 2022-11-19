<?php

use App\Models\History;
use App\Models\Service;
use Laravel\Lumen\Testing\DatabaseMigrations;

class HistoryTest extends TestCase
{

    use DatabaseMigrations;

    // create
    public function testShouldCreateHistory()
    {
        $params = [
            'status' => 'perbaikan selesai',
            'pesan' => 'perbaikan selesai cuy'
        ];
        $this->post('/services/1/history', $params, ['Authorization' => 'Bearer ' . $this->getToken('teknisi')]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'idRiwayat'
            ]
        ]);
    }
}