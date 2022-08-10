<?php

use App\Models\Category;

class CategoryTest extends TestCase{
    // getAll
    public function testShouldReturnAllCategories(){
        $this->get('/categories',['Authorization'=>'Bearer '.$this->owner()]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => ['*'=>[
                'idKategori',
                'nama'
            ]]
            ]);
    }

    // getById
    public function testShouldReturnCategory(){
        $data = Category::first();
        $this->get('/categories/'.$data->id,['Authorization'=>'Bearer '.$this->owner()]);
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
    public function testShouldCreateCategory(){
        $this->post('/categories',['nama'=>'testing'],['Authorization'=>'Bearer '.$this->owner()]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data'=>[
                'idKategori'
            ]
        ]);
    }

    // update
    public function testShouldUpdateCategory(){
        $data = Category::orderByDesc('id')->first();
        $this->put('/categories/'.$data->id,['nama'=>'php test'],['Authorization'=>'Bearer '.$this->owner()]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data'=>[
                'idKategori'
            ]
        ]);
    }

    // delete
    public function testShouldDeleteCategory(){
        $data = Category::orderByDesc('id')->first();
        $this->delete('/categories/'.$data->id,['Authorization'=>'Bearer '.$this->owner()]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
    }
}