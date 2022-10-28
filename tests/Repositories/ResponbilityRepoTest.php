<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class ResponbilityRepoTest extends TestCase
{

    use DatabaseMigrations;

    private $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make('App\Repositories\ResponbilityRepository');
    }
}