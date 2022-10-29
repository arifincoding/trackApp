<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ServiceFactory extends Factory
{

    protected $model = Service::class;

    public function definition()
    {
        return [
            'kode' => $this->faker->randomNumber(9, true),
            'keluhan' => $this->faker->paragraph(3, false),
            'uangMuka' => $this->faker->numberBetween(25000, 500000),
            'status' => 'antri',
            'estimasiBiaya' => $this->faker->numberBetween(25000, 1000000),
            'idCustomer' => $this->faker->randomNumber(2, false),
            'idProduct' => $this->faker->randomNumber(2, false),
            'butuhPersetujuan' => true,
            'disetujui' => null,
            'konfirmasiBiaya' => false,
            'diambil' => false,
            'waktuMasuk' => Carbon::now('GMT+7'),
            'waktuAmbil' => null,
            'usernameCS' => $this->faker->randomNumber(7, true),
            'usernameTeknisi' => $this->faker->randomNumber(7, true),
            'totalBiaya' => null,
            'garansi' => null
        ];
    }
}