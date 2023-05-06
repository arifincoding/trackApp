<?php

use App\Validations\CategoryValidation;
use Laravel\Lumen\Testing\DatabaseTransactions;

class CategoryVldTest extends TestCase
{
    use DatabaseTransactions;
    private CategoryValidation $validator;

    public function setUp(): void
    {
        parent::setUp();
        $this->validator = new CategoryValidation();
    }

    public function testShouldSuccessValidateInputQuery()
    {
        $input = ['limit' => 10, 'search' => 'test'];
        $this->validator->query();
        $result = $this->validator->validate($input, 'categories');
        $this->assertEquals(true, $result);
    }

    public function testShouldSuccessValidateInput()
    {
        $input = ['name' => 'test'];
        $this->validator->post();
        $result = $this->validator->validate($input, 'create');
        $this->assertEquals(true, $result);
    }
}
