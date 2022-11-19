<?php

use App\Models\Service;
use App\Models\Broken;
use Laravel\Lumen\Testing\DatabaseMigrations;

class BrokenTest extends TestCase
{

    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        Broken::factory()->count(3)->create(['idService' => 1, 'biaya' => null, 'disetujui' => null]);
    }

    // create broken by id service
    public function testShouldCreateBroken()
    {
        Service::factory()->create();
        $parameters = [
            'judul' => 'ganti testing',
            'deskripsi' => 'ini adalah testing'
        ];
        $header = ['Authorization' => 'Bearer ' . $this->getToken('teknisi')];
        $this->post('/services/1/brokens', $parameters, $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'idKerusakan'
            ]
        ]);
    }

    // get all brokens by id service
    public function testShouldReturnAllBroken()
    {
        Service::factory()->create();
        $header = ['Authorization' => 'Bearer ' . $this->getToken('teknisi')];
        $this->get('/services/1/brokens', $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => ['*' => [
                'id',
                'judul',
                'biaya',
                'disetujui'
            ]]
        ]);
    }

    // update broken by id
    public function testShouldUpdateBroken()
    {
        $parameters = [
            'judul' => 'ganti coba',
            'deskripsi' => 'ini adalah coba testing'
        ];
        $header = ['Authorization' => 'Bearer ' . $this->getToken('teknisi')];
        $this->put('/services/brokens/1', $parameters, $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'idKerusakan',
                'idService'
            ]
        ]);
    }

    // get brokens by id
    public function testShouldReturnBroken()
    {
        $header = ['Authorization' => 'Bearer ' . $this->getToken('teknisi')];
        $this->get('/services/brokens/1', $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'idKerusakan',
                'idService',
                'judul',
                'deskripsi',
                'biaya',
                'disetujui'
            ]
        ]);
    }

    // update broken cost
    public function testShouldUpdateBrokenCost()
    {
        $parameters = [
            'biaya' => '3000'
        ];
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];
        $this->put('/services/brokens/1/cost', $parameters, $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'idKerusakan'
            ]
        ]);
    }

    // update broken confirmation
    public function testShouldUpdateBrokenConfirmation()
    {
        $parameters = [
            'disetujui' => true,
        ];
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];
        $this->put('/services/brokens/1/confirm', $parameters, $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'idKerusakan'
            ]
        ]);
    }

    // delete broken
    public function testShouldDeleteBroken()
    {
        $header = ['Authorization' => 'Bearer ' . $this->getToken('teknisi')];
        $this->delete('/services/brokens/1', $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
    }
}