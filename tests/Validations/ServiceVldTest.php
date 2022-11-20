<?php

use App\Models\Category;
use App\Validations\ServiceValidation;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ServiceVldTest extends TestCase
{
    use DatabaseMigrations;
    private ServiceValidation $validator;

    public function setUp(): void
    {
        parent::setUp();
        $this->validator = new ServiceValidation();
    }

    public function testShouldSuccessValidateInput()
    {

        Category::factory()->create(['nama' => 'test']);
        $input = [
            'namaCustomer' => 'test',
            'noHp' => 6285667889876,
            'bisaWA' => true,
            'namaProduk' => 'testing',
            'kategori' => 'test',
            'keluhan' => 'testing coba',
            'butuhPersetujuan' => true,
            'estimasiBiaya' => 2000,
            'uangMuka' => 1000
        ];
        $result = $this->validator->validate($input, 'create');
        $this->assertEquals(true, $result);
    }

    public function testShouldSuccessValidateInputStatusService()
    {
        $input = ['status' => 'mulai diagnosa'];
        $this->validator->statusService();
        $result = $this->validator->validate($input, 'updateStatus');
        $this->assertEquals(true, $result);
    }

    public function testShouldSuccessValidateInputServiceWarranty()
    {
        $input = ['garansi' => '1 bulan'];
        $this->validator->serviceWarranty();
        $result = $this->validator->validate($input, 'updateWarranty');
        $this->assertEquals(true, $result);
    }

    public function testShouldSuccessValidateInputConfirmation()
    {
        $input = ['disetujui' => true];
        $this->validator->confirmation();
        $result = $this->validator->validate($input, 'updateConfirmation');
        $this->assertEquals(true, $result);
    }
}