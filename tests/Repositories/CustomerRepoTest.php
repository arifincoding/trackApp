<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class CustomerRepoTest extends TestCase
{

    use DatabaseMigrations;

    private $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make('App\Repositories\CustomerRepository');
    }

    public function testShoudCreateSingleCustomerData()
    {
        $inputs = [
            'nama' => 'mark',
            'noHp' => '6285235489032',
            'bisaWA' => true
        ];

        $result = $this->repository->create($inputs);
        $this->assertEquals(1, $result);
    }
}