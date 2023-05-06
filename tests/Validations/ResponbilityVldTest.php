<?php

use App\Models\Category;
use App\Models\User;
use App\Validations\ResponbilityValidation;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ResponbilityVldTest extends TestCase
{
    use DatabaseTransactions;
    private ResponbilityValidation $validator;

    public function setUp(): void
    {
        parent::setUp();
        $this->validator = new ResponbilityValidation();
    }

    public function testShouldSuccessValidateInput()
    {
        $user = User::factory()->create();
        $category = Category::factory()->count(2)->create();
        $input = ['category_id' => [$category[0]->id, $category[1]->id]];
        $this->validator->post($user->id, $input);
        $result = $this->validator->validate($input, 'create');
        $this->assertEquals(true, $result);
    }
}
