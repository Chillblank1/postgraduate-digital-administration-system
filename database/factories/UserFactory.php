<?php

namespace Database\Factories;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'role' => UserRole::Student,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'phone_number' => null,
            'department' => fake()->optional()->company(),
            'faculty' => fake()->optional()->word(),
            'remember_token' => Str::random(10),
        ];
    }

    public function student(): static
    {
        return $this->state(fn () => [
            'role' => UserRole::Student,
        ]);
    }

    public function supervisor(): static
    {
        return $this->state(fn () => [
            'role' => UserRole::Supervisor,
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn () => [
            'email_verified_at' => null,
        ]);
    }
}
