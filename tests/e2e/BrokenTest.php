<?php

use App\Models\Service;
use App\Models\Broken;
use Laravel\Lumen\Testing\DatabaseTransactions;

class BrokenTest extends TestCase
{

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
    }

    // create broken by id service
    public function testShouldCreateBroken()
    {
        $service = Service::factory()->create();
        $parameters = [
            'title' => 'testing baru',
            'description' => 'ini adalah testing baru'
        ];
        $header = ['Authorization' => 'Bearer ' . $this->getToken('teknisi')];
        $this->post("/services/$service->id/brokens", $parameters, $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'broken_id'
            ]
        ]);
    }

    // get all brokens by id service
    public function testShouldReturnAllBroken()
    {
        $service = Service::factory()->create();
        Broken::factory()->count(3)->create(['service_id' => $service->id]);
        $header = ['Authorization' => 'Bearer ' . $this->getToken('teknisi')];
        $this->get("/services/$service->id/brokens", $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => ['*' => [
                'id',
                'title',
                'cost',
                'is_approved'
            ]]
        ]);
    }

    // update broken by id
    public function testShouldUpdateBroken()
    {
        $broken = Broken::factory()->create();
        $parameters = [
            'title' => 'tisting update',
            'description' => 'ini adalah testing update'
        ];
        $header = ['Authorization' => 'Bearer ' . $this->getToken('teknisi')];
        $this->put("/services/brokens/$broken->id", $parameters, $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'broken_id',
                'service_id'
            ]
        ]);
    }

    // get brokens by id
    public function testShouldReturnBroken()
    {
        $broken = Broken::factory()->create();
        $header = ['Authorization' => 'Bearer ' . $this->getToken('teknisi')];
        $this->get("/services/brokens/$broken->id", $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'service_id',
                'title',
                'description',
                'cost',
                'is_approved'
            ]
        ]);
    }

    // update broken cost
    public function testShouldUpdateBrokenCost()
    {
        $broken = Broken::factory()->create();
        $parameters = [
            'cost' => 3000
        ];
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];
        $this->put("/services/brokens/$broken->id/cost", $parameters, $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'broken_id'
            ]
        ]);
    }

    // update broken confirmation
    public function testShouldUpdateBrokenConfirmation()
    {
        $broken = Broken::factory()->create();
        $parameters = [
            'is_approved' => true,
        ];
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];
        $this->put("/services/brokens/$broken->id/confirm", $parameters, $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'broken_id'
            ]
        ]);
    }

    // delete broken
    public function testShouldDeleteBroken()
    {
        $broken = Broken::factory()->create();
        $header = ['Authorization' => 'Bearer ' . $this->getToken('teknisi')];
        $this->delete("/services/brokens/$broken->id", $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
    }
}
