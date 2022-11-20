<?php

use App\Models\User;
use App\Validations\UserValidation;
use Laravel\Lumen\Testing\DatabaseMigrations;

class UserVldTest extends TestCase
{

    use DatabaseMigrations;

    private UserValidation $validator;

    public function setUp(): void
    {
        parent::setUp();
        $this->validator = new UserValidation();
    }

    public function testShouldSuccessValidateInput()
    {
        $input = [
            'namaDepan' => 'son',
            'namaBelakang' => 'goku',
            'jenisKelamin' => 'pria',
            'noHp' => 6286777888999,
            'email' => 'songoku@test.com',
            'peran' => 'teknisi'
        ];
        $this->validator->post();
        $result = $this->validator->validate($input, 'create');
        $this->assertEquals(true, $result);
    }

    public function testShouldSuccessValidateInputUpdate()
    {
        User::factory()->create(['email' => 'songoku@test.com']);
        $input = [
            'namaDepan' => 'son',
            'namaBelakang' => 'goku',
            'jenisKelamin' => 'pria',
            'noHp' => 6286777888999,
            'email' => 'songoku@test.com',
            'peran' => 'teknisi'
        ];
        $this->validator->post(1);
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
        User::factory()->create();
        $input = [
            'email' => 'songoku@test.com',
            'noHp' => 625777222333,
            'alamat' => 'testing kota'
        ];
        $this->validator->update(1);
        $result = $this->validator->validate($input, 'updateAccount');
        $this->assertEquals(true, $result);
    }

    public function testShouldSuccessValidateInputChangePassword()
    {
        $this->getToken('customer service');
        $input = [
            'sandiLama' => 'rahasia',
            'sandiBaru' => 'testingg'
        ];
        $this->validator->changePassword();
        $result = $this->validator->validate($input, 'changePassword');
        $this->assertEquals(true, $result);
    }
}