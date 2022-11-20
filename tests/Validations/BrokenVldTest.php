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
        $input = ['judul' => 'test', 'deskripsi' => 'testing broken'];
        $result = $this->validator->validate($input, 'create');
        $this->assertEquals(true, $result);
    }

    public function testShouldSuccessValidateInputConfirmation()
    {
        $input = ['disetujui' => true];
        $this->validator->confirm();
        $result = $this->validator->validate($input, 'updateConfirm');
        $this->assertEquals(true, $result);
    }

    public function testShouldSuccessValidateInputCost()
    {
        $input = ['biaya' => 1000];
        $this->validator->cost();
        $result = $this->validator->validate($input, 'updateCost');
        $this->assertEquals(true, $result);
    }
}