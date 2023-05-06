<?php

use App\Models\User;
use App\Validations\UserValidation;
use Laravel\Lumen\Testing\DatabaseTransactions;

class UserVldTest extends TestCase
{

    use DatabaseTransactions;

    private UserValidation $validator;

    public function setUp(): void
    {
        parent::setUp();
        $this->validator = new UserValidation();
    }

    public function testShouldSuccessValidateInput()
    {
        $input = [
            'firstname' => 'son',
            'lastname' => 'goku',
            'gender' => 'pria',
            'telp' => 6286777888999,
            'email' => 'songoku@test.com',
            'role' => 'teknisi'
        ];
        $this->validator->post();
        $result = $this->validator->validate($input, 'create');
        $this->assertEquals(true, $result);
    }

    public function testShouldSuccessValidateInputUpdate()
    {
        $user = User::factory()->create(['email' => 'songoku@test.com']);
        $input = [
            'firstname' => 'son',
            'lastname' => 'goku',
            'gender' => 'pria',
            'telp' => 6286777888999,
            'email' => 'songoku@test.com',
            'role' => 'teknisi'
        ];
        $this->validator->post($user->id);
        $result = $this->validator->validate($input, 'update');
        $this->assertEquals(true, $result);
    }

    public function testShouldSuccessValidateInputLogin()
    {
        User::factory()->create(['username' => '2211001']);
        $input = [
            'username' => '2211001',
            'password' => 'rahasia'
        ];
        $this->validator->login();
        $result = $this->validator->validate($input, 'login');
        $this->assertEquals(true, $result);
    }

    public function testShouldSuccessValidateInputGet()
    {
        $input = ['limit' => 10];
        $this->validator->get();
        $result = $this->validator->validate($input, 'allUser');
        $this->assertEquals(true, $result);
    }

    public function testShouldSuccessValidateInputUpdateAccount()
    {
        $user = User::factory()->create();
        $input = [
            'email' => 'songoku@test.com',
            'telp' => 625777222333,
            'address' => 'testing kota'
        ];
        $this->validator->update($user->id);
        $result = $this->validator->validate($input, 'updateAccount');
        $this->assertEquals(true, $result);
    }

    public function testShouldSuccessValidateInputChangePassword()
    {
        $this->getToken('pemilik');
        $input = [
            'old_password' => 'rahasia',
            'new_password' => 'testingg'
        ];
        $this->validator->changePassword();
        $result = $this->validator->validate($input, 'changePassword');
        $this->assertEquals(true, $result);
    }
}
