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
        // Create demo user
        User::factory()->create([
            'name' => 'デモユーザー',
            'email' => 'demo@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Seed tourist spots and guides
        $this->call([
            TouristSpotSeeder::class,
            GuideSeeder::class,
        ]);
    }
}
