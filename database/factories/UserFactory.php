<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'username' => fake()->userName,
            'password' => bcrypt('password'),
            'last_login' => fake()->dateTime(),
            'is_active' => true,
            'role' => fake()->randomElement(['manager', 'agent']),
        ];
    }
}
