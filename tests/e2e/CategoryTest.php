<?php

use App\Models\Category;
use Laravel\Lumen\Testing\DatabaseMigrations;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;
    // getAll
    public function setUp(): void
    {
        parent::setUp();
        Category::factory()->count(3)->create();
    }
    public function testShouldReturnAllCategories()
    {
        $this->get('/categories', ['Authorization' => 'Bearer ' . $this->getToken('pemilik')]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => ['*' => [
                'idKategori',
                'nama'
            ]]
        ]);
    }

    // getById
    public function testShouldReturnCategory()
    {
        $this->get('/categories/1', ['Authorization' => 'Bearer ' . $this->getToken('pemilik')]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'idKategori',
                'nama'
            ]
        ]);
    }

    // create
    public function testShouldCreateCategory()
    {
        $this->post('/categories', ['nama' => 'testing'], ['Authorization' => 'Bearer ' . $this->getToken('pemilik')]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'idKategori'
            ]
        ]);
    }

    // update
    public function testShouldUpdateCategory()
    {
        $this->put('/categories/1', ['nama' => 'php test'], ['Authorization' => 'Bearer ' . $this->getToken('pemilik')]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'idKategori'
            ]
        ]);
    }

    // delete
    public function testShouldDeleteCategory()
    {
        $this->delete('/categories/1', ['Authorization' => 'Bearer ' . $this->getToken('pemilik')]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
    }
}