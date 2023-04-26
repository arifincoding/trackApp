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
            'password' => Hash::make('rahasia'),
            'firstname' => $this->faker->firstNameMale(),
            'lastname' => $this->faker->lastName(),
            'role' => 'pemilik',
            'gender' => 'pria',
            'email' => $this->faker->unique()->safeEmail,
            'telp' => $this->faker->numerify('628##########'),
            'address' => $this->faker->address()
        ];
    }

    public function tecnician()
    {
        return $this->state(function (array $attributs) {
            return [
                'role' => 'teknisi'
            ];
        });
    }

    public function cs()
    {
        return $this->state(function (array $attributs) {
            return [
                'role' => 'customer service'
            ];
        });
    }
}
