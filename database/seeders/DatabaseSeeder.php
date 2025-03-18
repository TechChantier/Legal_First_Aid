<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
    // Create 15 lawyers
        User::factory(15)->create([
            'role' => 'lawyer',
        ]);

        // Create 10 normal users
        User::factory(10)->create([
            'role' => 'normal_user',
        ]);

        // Create a test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'normal_user',
        ]);
    }
}
