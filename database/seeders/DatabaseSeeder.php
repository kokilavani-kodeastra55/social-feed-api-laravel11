<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->delete();

        // 1. Create the specific static test user for Postman login
        User::create([
            'name' => 'Aarav Mehta',
            'email' => 'aarav.mehta@example.com',
            'password' => Hash::make('password'),
        ]);

        // 2. Create 4 more users dynamically with random fake values
        User::factory()
            ->count(4)
            ->create();
    }
}
