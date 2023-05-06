<?php

use App\Validations\HistoryValidation;

class HistoryVldTest extends TestCase
{
    private HistoryValidation $validator;

    public function setUp(): void
    {
        parent::setUp();
        $this->validator = new HistoryValidation();
    }

    public function testShouldSuccessValidateInput()
    {
        $input = ['status' => 'mulai diagnosa', 'message' => 'testing history'];
        $result = $this->validator->validate($input, 'create');
        $this->assertEquals(true, $result);
    }
}
