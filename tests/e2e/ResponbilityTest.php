<?php

use App\Models\User;
use App\Models\Responbility;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ResponbilityTest extends TestCase
{

    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
    }

    // create responbility
    public function testShouldCreateResponbility()
    {
        User::factory()->create(['peran' => 'teknisi', 'username' => '2211002']);
        Category::factory()->create();
        $parameters = [
            'idKategori' => [1]
        ];
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];
        $this->post('/employes/1/technician/responbilities', $parameters, $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
    }

    // get all responbility by username
    public function testShouldReturnAllRespobilityByUsername()
    {
        User::factory()->create(['peran' => 'teknisi', 'username' => '2211002']);
        Category::factory()->count(3)->create();
        Responbility::factory()->count(3)->sequence(fn ($sequence) => ['idKategori' => $sequence->index + 1])->create(['username' => '2211002']);
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];
        $this->get('/employes/2211002/technician/responbilities', $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => ['*' => [
                'id',
                'kategori' => [
                    'nama'
                ]
            ]]
        ]);
    }

    // delete responbility
    public function testShouldDeleteResponbility()
    {
        Responbility::factory()->create();
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];
        $this->delete('/employes/technician/responbilities/1', $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
    }
}