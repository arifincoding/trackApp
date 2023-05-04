<?php

use App\Models\Category;
use Laravel\Lumen\Testing\DatabaseTransactions;

class CategoryTest extends TestCase
{
    use DatabaseTransactions;
    // getAll
    public function setUp(): void
    {
        parent::setUp();
    }
    public function testShouldReturnAllCategories()
    {
        Category::factory()->count(3)->create();
        $this->get('/categories', ['Authorization' => 'Bearer ' . $this->getToken('pemilik')]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => ['*' => [
                'id',
                'name'
            ]]
        ]);
    }

    // getById
    public function testShouldReturnCategory()
    {
        $category = Category::factory()->create();
        $this->get("/categories/$category->id", ['Authorization' => 'Bearer ' . $this->getToken('pemilik')]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'name'
            ]
        ]);
    }

    // create
    public function testShouldCreateCategory()
    {
        $this->post("/categories", ['name' => 'testing create category'], ['Authorization' => 'Bearer ' . $this->getToken('pemilik')]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'category_id'
            ]
        ]);
    }

    // update
    public function testShouldUpdateCategory()
    {
        $category = Category::factory()->create();
        $this->put("/categories/$category->id", ['name' => 'testing update category'], ['Authorization' => 'Bearer ' . $this->getToken('pemilik')]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'category_id'
            ]
        ]);
    }

    // delete
    public function testShouldDeleteCategory()
    {
        $category = Category::factory()->create();
        $this->delete("/categories/$category->id", ['Authorization' => 'Bearer ' . $this->getToken('pemilik')]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
    }
}
