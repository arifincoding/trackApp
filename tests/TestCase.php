<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    public function getToken(string $role)
    {
        User::factory()->create(['username' => '2211001', 'password' => Hash::make('rahasia'), 'role' => $role]);
        return Auth::attempt(['username' => '2211001', 'password' => 'rahasia']);
    }
}
