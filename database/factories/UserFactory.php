<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Curated identities for social feed testing.
     *
     * @var array<int, array{name: string, email: string}>
     */
    private array $socialFeedUsers = [
        ['name' => 'Aarav Mehta', 'email' => 'aarav.mehta@example.com'],
        ['name' => 'Diya Kapoor', 'email' => 'diya.kapoor@example.com'],
        ['name' => 'Rohan Sharma', 'email' => 'rohan.sharma@example.com'],
        ['name' => 'Ananya Verma', 'email' => 'ananya.verma@example.com'],
        ['name' => 'Kabir Nair', 'email' => 'kabir.nair@example.com'],
        ['name' => 'Isha Reddy', 'email' => 'isha.reddy@example.com'],
        ['name' => 'Arjun Patel', 'email' => 'arjun.patel@example.com'],
        ['name' => 'Meera Iyer', 'email' => 'meera.iyer@example.com'],
        ['name' => 'Vikram Joshi', 'email' => 'vikram.joshi@example.com'],
        ['name' => 'Naina Singh', 'email' => 'naina.singh@example.com'],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create deterministic test users for login and API testing.
     */
    public function socialFeedTestUsers(): static
    {
        return $this->sequence(...$this->socialFeedUsers)->state(fn (array $attributes) => [
            'password' => static::$password ??= Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}
