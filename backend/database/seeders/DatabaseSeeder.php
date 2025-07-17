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
        // Create demo user if not exists
        User::firstOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'デモユーザー',
                'email' => 'demo@example.com',
                'password' => bcrypt('password123'),
            ]
        );

        // Seed prefectures, travel spots, guides and basic events
        $this->call([
            PrefectureSeeder::class,
            TravelSpotSeeder::class,
            AllGuidesSeeder::class, // 全観光地のガイドを自動生成
            EventSeeder::class,
            PronunciationCorrectionSeeder::class,
        ]);
    }
}
