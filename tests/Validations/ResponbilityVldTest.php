<?php

use App\Models\Category;
use App\Models\User;
use App\Validations\ResponbilityValidation;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ResponbilityVldTest extends TestCase
{
    use DatabaseMigrations;
    private ResponbilityValidation $validator;

    public function setUp(): void
    {
        parent::setUp();
        $this->validator = new ResponbilityValidation();
    }

    public function testShouldSuccessValidateInput()
    {
        User::factory()->create();
        Category::factory()->count(2)->create();
        $input = ['idKategori' => [1, 2]];
        $this->validator->post(1, $input);
        $result = $this->validator->validate($input, 'create');
        $this->assertEquals(true, $result);
    }
}