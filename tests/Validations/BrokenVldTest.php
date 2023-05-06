<?php

use App\Validations\BrokenValidation;

class BrokenVldTest extends TestCase
{

    private BrokenValidation $validator;
    public function setUp(): void
    {
        parent::setUp();
        $this->validator = new BrokenValidation();
    }

    public function testShouldSuccessValidateInput()
    {
        $input = ['title' => 'test', 'description' => 'testing broken'];
        $result = $this->validator->validate($input, 'create');
        $this->assertEquals(true, $result);
    }

    public function testShouldSuccessValidateInputConfirmation()
    {
        $input = ['is_approved' => true];
        $this->validator->confirm();
        $result = $this->validator->validate($input, 'updateConfirm');
        $this->assertEquals(true, $result);
    }

    public function testShouldSuccessValidateInputCost()
    {
        $input = ['cost' => 1000];
        $this->validator->cost();
        $result = $this->validator->validate($input, 'updateCost');
        $this->assertEquals(true, $result);
    }
}
