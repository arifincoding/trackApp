<?php

use App\Models\Category;
use App\Validations\CategoryValidation;
use Laravel\Lumen\Testing\DatabaseMigrations;

class CategoryVldTest extends TestCase
{
    use DatabaseMigrations;
    private CategoryValidation $validator;

    public function setUp(): void
    {
        parent::setUp();
        $this->validator = new CategoryValidation();
    }

    public function testShouldSuccessValidateInputQuery()
    {
        $input = ['limit' => 10, 'cari' => 'test'];
        $this->validator->query();
        $result = $this->validator->validate($input, 'categories');
        $this->assertEquals(true, $result);
    }

    public function testShouldSuccessValidateInput()
    {
        $input = ['nama' => 'test'];
        $this->validator->post();
        $result = $this->validator->validate($input, 'create');
        $this->assertEquals(true, $result);
    }
}