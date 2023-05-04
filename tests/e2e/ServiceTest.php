<?php

use App\Models\Service;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Broken;
use App\Models\Category;
use App\Models\History;
use App\Models\Responbility;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ServiceTest extends TestCase
{

    use DatabaseTransactions;

    // create
    public function testShouldCreateService()
    {
        $category = Category::factory()->create();
        $parameters = [
            'customer' => [
                'name' => 'saitama',
                'telp' => 6285235690084,
                'is_whatsapp' => false,
            ],
            'product' => [
                'name' => 'laptop testing',
                'category_id' => $category->id,
                'completeness' => 'baterai',
                'product_defects' => 'baret'
            ],
            'complaint' => 'lagi ditesting',
            'need_approval' => false,
            'note' => 'password e ora',
            'down_payment' => 1000,
            'estimated_cost' => 2000
        ];
        $this->post('/services', $parameters, ['Authorization' => 'Bearer ' . $this->getToken('customer service')]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'service_id'
            ]
        ]);
    }

    // update
    public function testShouldUpdateService()
    {
        $category = Category::factory()->create();
        $service = Service::factory()->create();
        $parameters = [
            'customer' => [
                'name' => 'goku',
                'telp' => 6285235690023,
                'is_whatsapp' => true,
            ],
            'product' => [
                'name' => 'hp testing',
                'category_id' => $category->id,
                'completeness' => 'tas',
                'product_defects' => 'mulus'
            ],
            'complaint' => 'lagi ditest',
            'need_approval' => true,
            'note' => 'passwordnya 123',
            'down_payment' => 4000,
            'estimated_cost' => 7000,
        ];
        $this->put("/services/$service->id", $parameters, ['Authorization' => 'Bearer ' . $this->getToken('customer service')]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'service_id'
            ]
        ]);
    }

    // get all
    public function testShouldReturnAllService()
    {
        Service::factory()->count(3)->create();
        $this->get('/services', ['Authorization' => 'Bearer ' . $this->getToken('customer service')]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => ['*' => [
                'id',
                'code',
                'complaint',
                'status',
                'total_cost',
                'is_take',
                'is_approved',
                'product' => [
                    'name',
                    'customer' => [
                        'name',
                        'telp'
                    ],
                    'category' => [
                        'name'
                    ]
                ]
            ]]
        ]);
    }

    // get service queue
    public function testShouldReturnAllServiceQueue()
    {
        $token = $this->getToken('teknisi');
        $tecnicianUsername = Auth::payload()->get('username');
        $resp = Responbility::factory()->count(3)->create([
            'username' => $tecnicianUsername
        ]);
        $product = Product::factory()->count(3)->sequence(
            fn ($sequence) => [
                'category_id' => $resp[$sequence->index]->category_id
            ]
        )->create();
        Service::factory()->count(3)->sequence(
            fn ($sequence) => [
                'product_id' => $product[$sequence->index]->id
            ]
        )->create([
            'status' => 'antri',
            'tecnician_username' => null
        ]);
        $header = ['Authorization' => 'Bearer ' . $token];
        $this->get("/services/$tecnicianUsername/queue", $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => ['*' => [
                'id',
                'code',
                'complaint',
                'status',
                'is_approved',
                'product' => [
                    'name',
                    'category' => [
                        'name'
                    ]
                ]
            ]]
        ]);
    }

    // get service progress
    public function testShouldReturnAllServiceProgress()
    {
        $token = $this->getToken('teknisi');
        $tecnicianUsername = Auth::payload()->get('username');
        Service::factory()->count(3)->create(['tecnician_username' => $tecnicianUsername]);
        $header = ['Authorization' => 'Bearer ' . $token];
        $this->get("/services/$tecnicianUsername/progress", $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => ['*' => [
                'id',
                'code',
                'complaint',
                'status',
                'is_approved',
                'product' => [
                    'name',
                    'category' => [
                        'name'
                    ]
                ]
            ]]
        ]);
    }

    // update service status
    public function testShouldUpdateServiceStatus()
    {
        $service = Service::factory()->create(['status' => 'antri']);
        $parameters = ['status' => 'mulai diagnosa'];
        $header = ['Authorization' => 'Bearer ' . $this->getToken('teknisi')];
        $this->put("/services/$service->id/status", $parameters, $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'service_id'
            ]
        ]);
    }

    // set confirmation cost
    public function testShouldSetConfirmCost()
    {
        $service = Service::factory()->create();
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];
        $this->put("/services/$service->id/confirm-cost", [], $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'service_id'
            ]
        ]);
    }

    // update warranty
    public function testShouldUpdateServiceWarranty()
    {
        $service = Service::factory()->create();
        $parameters = ['warranty' => '1 bulan'];
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];
        $this->put("/services/$service->id/warranty", $parameters, $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'service_id'
            ]
        ]);
    }

    // set service confirmation
    public function testShouldSetServiceConfirmation()
    {
        $service = Service::factory()->create();
        $parameters = ['is_approved' => true];
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];
        $this->put("/services/$service->id/confirmation", $parameters, $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'service_id'
            ]
        ]);
    }

    // set service taking
    public function testShouldSetServiceTake()
    {
        $service = Service::factory()->create(['warranty' => '1 bulan']);
        $header = ['Authorization' => 'Bearer ' . $this->getToken('customer service')];
        $this->put("/services/$service->id/take", [], $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'service_id'
            ]
        ]);
    }

    // delete service
    public function testShouldDeleteService()
    {
        $service = Service::factory()->create();
        Broken::factory()->count(2)->create(['service_id' => $service->id]);
        History::factory()->count(2)->create(['service_id' => $service->id]);
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];
        $this->delete("/services/$service->id", $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
    }

    // get service by id with brokens,customer and product
    public function testShouldreturnServiceWithAllRelation()
    {
        $service = Service::factory()->create();
        Broken::factory()->count(3)->create(['service_id' => $service->id]);
        $header = ['Authorization' => 'Bearer ' . $this->getToken('customer service')];
        $this->get("/services/$service->id/detail?include=product,broken", $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'code',
                'complaint',
                'status',
                'total_cost' => [
                    'int',
                    'string'
                ],
                'is_take',
                'is_approved',
                'estimated_cost' => [
                    'int',
                    'string'
                ],
                'down_payment' => [
                    'int',
                    'string'
                ],
                'to_be_paid',
                'entry' => [
                    'date',
                    'time'
                ],
                'taked' => [
                    'date',
                    'time'
                ],
                'warranty',
                'username' => [
                    'cs',
                    'tecnician'
                ],
                'need_approval',
                'is_cost_confirmation',
                'note',
                'product' => [
                    'name',
                    'product_defects',
                    'completeness',
                    'category' => [
                        'name'
                    ],
                    'client' => [
                        'name',
                        'telp',
                        'is_whatsapp'
                    ],
                ], 'broken' => ['*' => [
                    'id',
                    'title',
                    'cost',
                    'is_approved'
                ]]
            ]
        ]);
    }

    public function testShouldreturnServiceWithKlien()
    {
        $this->markTestIncomplete();
        Service::factory()->for(Customer::factory(), 'klien')->for(Product::factory(), 'produk')->has(Broken::factory()->count(3), 'kerusakan')->create();
        $header = ['Authorization' => 'Bearer ' . $this->getToken('customer service')];
        $this->get('/services/1/detail?include=klien', $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'id', 'kode', 'keluhan', 'status', 'totalBiaya', 'totalBiayaString', 'diambil', 'disetujui', 'estimasiBiaya', 'estimasiBiayaString', 'uangMuka', 'uangMukaString', 'yangHarusDibayar', 'tanggalMasuk', 'jamMasuk', 'tanggalAmbil', 'jamAmbil', 'garansi', 'usernameCS', 'usernameTeknisi', 'butuhPersetujuan', 'sudahKonfirmasiBiaya', 'klien' => [
                    'nama',
                    'noHp',
                    'bisaWA'
                ]
            ]
        ]);
    }

    public function testShouldreturnServiceWithProduk()
    {
        $service = Service::factory()->create();
        Broken::factory()->count(3)->create(['service_id' => $service->id]);
        $header = ['Authorization' => 'Bearer ' . $this->getToken('customer service')];
        $this->get("/services/$service->id/detail?include=product", $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'code',
                'complaint',
                'status',
                'total_cost' => [
                    'int',
                    'string'
                ],
                'is_take',
                'is_approved',
                'estimated_cost' => [
                    'int',
                    'string'
                ],
                'down_payment' => [
                    'int',
                    'string'
                ],
                'to_be_paid',
                'entry' => [
                    'date',
                    'time'
                ],
                'taked' => [
                    'date',
                    'time'
                ],
                'warranty',
                'username' => [
                    'cs',
                    'tecnician'
                ],
                'need_approval',
                'is_cost_confirmation',
                'note',
                'product' => [
                    'name',
                    'product_defects',
                    'completeness',
                    'category' => [
                        'name'
                    ],
                    'client' => [
                        'name',
                        'telp',
                        'is_whatsapp'
                    ],
                ]
            ]
        ]);
    }

    public function testShouldreturnServiceWithKerusakan()
    {
        $service = Service::factory()->create();
        Broken::factory()->count(3)->create(['service_id' => $service->id]);
        $header = ['Authorization' => 'Bearer ' . $this->getToken('customer service')];
        $this->get("/services/$service->id/detail?include=broken", $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'code',
                'complaint',
                'status',
                'total_cost' => [
                    'int',
                    'string'
                ],
                'is_take',
                'is_approved',
                'estimated_cost' => [
                    'int',
                    'string'
                ],
                'down_payment' => [
                    'int',
                    'string'
                ],
                'to_be_paid',
                'entry' => [
                    'date',
                    'time'
                ],
                'taked' => [
                    'date',
                    'time'
                ],
                'warranty',
                'username' => [
                    'cs',
                    'tecnician'
                ],
                'need_approval',
                'is_cost_confirmation',
                'note',
                'broken' => ['*' => [
                    'id',
                    'title',
                    'cost',
                    'is_approved'
                ]]
            ]
        ]);
    }

    // get service by id
    public function testShouldReturnService()
    {
        $service = Service::factory()->create();
        Broken::factory()->count(3)->create(['service_id' => $service->id]);
        $header = ['Authorization' => 'Bearer ' . $this->getToken('customer service')];
        $this->get("/services/$service->id/detail", $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'code',
                'complaint',
                'status',
                'total_cost' => [
                    'int',
                    'string'
                ],
                'is_take',
                'is_approved',
                'estimated_cost' => [
                    'int',
                    'string'
                ],
                'down_payment' => [
                    'int',
                    'string'
                ],
                'to_be_paid',
                'entry' => [
                    'date',
                    'time'
                ],
                'taked' => [
                    'date',
                    'time'
                ],
                'warranty',
                'username' => [
                    'cs',
                    'tecnician'
                ],
                'need_approval',
                'is_cost_confirmation',
                'note'
            ]
        ]);
    }

    // track
    public function testShouldReturnTrackingInfo()
    {
        $service = Service::factory()->create(['status' => 'proses']);
        Broken::factory()->count(3)->create(['service_id' => $service->id]);
        History::factory()->count(3)->sequence(
            ['status' => 'antri'],
            ['status' => 'mulai diagnosa'],
            ['status' => 'selesai diagnosa']
        )->create(['service_id' => $service->id]);
        $this->get("/services/$service->code/track");
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'code',
                'status',
                'is_approved',
                'total_cost',
                'product' => [
                    'name',
                    'category' => ['name']
                ],
                'broken' => ['*' => [
                    'title',
                    'description',
                    'cost',
                    'is_approved'
                ]],
                'history' => ['*' => [
                    'status',
                    'message',
                    'created' => [
                        'date',
                        'time'
                    ]
                ]]
            ]
        ]);
    }
}
