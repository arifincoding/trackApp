<?php

use App\Models\Category;
use App\Validations\ServiceValidation;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ServiceVldTest extends TestCase
{
    use DatabaseTransactions;
    private ServiceValidation $validator;

    public function setUp(): void
    {
        parent::setUp();
        $this->validator = new ServiceValidation();
    }

    public function testShouldSuccessValidateInput()
    {

        $category = Category::factory()->create();
        $input = [
            'customer' => [
                'name' => 'test',
                'telp' => 6285667889876,
                'is_whatsapp' => true,
            ],
            'product' => [
                'name' => 'testing',
                'category_id' => $category->id,
                'completeness' => 'beterai, charger',
                'product_defects' => 'layar garis'
            ],
            'complaint' => 'testing coba',
            'need_approval' => true,
            'estimated_cost' => 2000,
            'down_payment' => 1000,
            'note' => 'ini adalah note'
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
        $input = ['warranty' => '1 bulan'];
        $this->validator->serviceWarranty();
        $result = $this->validator->validate($input, 'updateWarranty');
        $this->assertEquals(true, $result);
    }

    public function testShouldSuccessValidateInputConfirmation()
    {
        $input = ['is_approved' => true];
        $this->validator->confirmation();
        $result = $this->validator->validate($input, 'updateConfirmation');
        $this->assertEquals(true, $result);
    }
}
