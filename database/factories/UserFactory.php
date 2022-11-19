<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'username' => $this->faker->randomNumber(7, true),
            'password' => Hash::make('test'),
            'namaDepan' => $this->faker->firstNameMale(),
            'namaBelakang' => $this->faker->lastName(),
            'peran' => 'pemilik',
            'jenisKelamin' => 'pria',
            'email' => $this->faker->unique()->safeEmail,
            'noHp' => $this->faker->numerify('628##########'),
            'alamat' => $this->faker->address()
        ];
    }

    public function tecnician()
    {
        return $this->state(function (array $attributs) {
            return [
                'peran' => 'teknisi'
            ];
        });
    }

    public function cs()
    {
        return $this->state(function (array $attributs) {
            return [
                'peran' => 'customer service'
            ];
        });
    }
}