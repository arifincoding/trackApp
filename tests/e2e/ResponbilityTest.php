<?php

use App\Models\User;
use App\Models\Responbility;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ResponbilityTest extends TestCase
{

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
    }

    // create responbility
    public function testShouldCreateResponbility()
    {
        $user = User::factory()->create(['role' => 'teknisi']);
        $category = Category::factory()->count(2)->create();
        $parameters = [
            'category_id' => [$category[0]->id, $category[1]->id]
        ];
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];
        $this->post("/employes/$user->id/technician/responbilities", $parameters, $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
    }

    // get all responbility by username
    public function testShouldReturnAllRespobilityByUsername()
    {
        $user = User::factory()->create();
        Responbility::factory()->count(3)->create(['username' => $user->username]);
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];
        $this->get("/employes/$user->username/technician/responbilities", $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => ['*' => [
                'id',
                'category' => [
                    'name'
                ]
            ]]
        ]);
    }

    // delete responbility
    public function testShouldDeleteResponbility()
    {
        $resp = Responbility::factory()->create();
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];
        $this->delete("/employes/technician/responbilities/$resp->id", $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
    }
}
