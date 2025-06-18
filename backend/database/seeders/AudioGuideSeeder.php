<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AudioGuideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // サンプル音声ガイドファイル
        // 実際の運用では音声ファイルをアップロードして使用
        $audioGuides = [
            [
                'guide_id' => 1, // 東京スカイツリー完全ガイド
                'audio_file_path' => '/audio/skytree_complete_guide.mp3',
                'audio_file_url' => 'https://www.soundjay.com/misc/sounds/bell-ringing-05.mp3', // サンプル音声URL
                'duration_seconds' => 480, // 8分
                'format' => 'mp3',
                'file_size' => 7680000, // 約7.6MB
                'voice_actor' => '田中美咲',
                'language' => 'ja',
                'is_active' => true,
            ],
            [
                'guide_id' => 2, // スカイツリーの建設秘話
                'audio_file_path' => '/audio/skytree_construction.mp3',
                'audio_file_url' => 'https://www.soundjay.com/misc/sounds/clock-ticking-3.mp3', // サンプル音声URL
                'duration_seconds' => 300, // 5分
                'format' => 'mp3',
                'file_size' => 4800000, // 約4.8MB
                'voice_actor' => '佐藤健太',
                'language' => 'ja',
                'is_active' => true,
            ],
            [
                'guide_id' => 3, // 浅草寺の歴史と文化
                'audio_file_path' => '/audio/asakusa_history.mp3',
                'audio_file_url' => 'https://www.soundjay.com/nature/sounds/rain-01.mp3', // サンプル音声URL
                'duration_seconds' => 600, // 10分
                'format' => 'mp3',
                'file_size' => 9600000, // 約9.6MB
                'voice_actor' => '山田花子',
                'language' => 'ja',
                'is_active' => true,
            ],
            [
                'guide_id' => 4, // 仲見世通りの楽しみ方
                'audio_file_path' => '/audio/nakamise_guide.mp3',
                'audio_file_url' => 'https://www.soundjay.com/misc/sounds/footsteps-3.mp3', // サンプル音声URL
                'duration_seconds' => 360, // 6分
                'format' => 'mp3',
                'file_size' => 5760000, // 約5.7MB
                'voice_actor' => '鈴木一郎',
                'language' => 'ja',
                'is_active' => true,
            ],
            [
                'guide_id' => 5, // 明治神宮の由来と意義
                'audio_file_path' => '/audio/meiji_shrine.mp3',
                'audio_file_url' => 'https://www.soundjay.com/nature/sounds/wind-1.mp3', // サンプル音声URL
                'duration_seconds' => 540, // 9分
                'format' => 'mp3',
                'file_size' => 8640000, // 約8.6MB
                'voice_actor' => '高橋真理',
                'language' => 'ja',
                'is_active' => true,
            ],
            [
                'guide_id' => 6, // 江戸城跡としての皇居東御苑
                'audio_file_path' => '/audio/imperial_palace.mp3',
                'audio_file_url' => 'https://www.soundjay.com/nature/sounds/birds-1.mp3', // サンプル音声URL
                'duration_seconds' => 420, // 7分
                'format' => 'mp3',
                'file_size' => 6720000, // 約6.7MB
                'voice_actor' => '伊藤雅人',
                'language' => 'ja',
                'is_active' => true,
            ],
            [
                'guide_id' => 7, // 上野動物園の歴史と見どころ
                'audio_file_path' => '/audio/ueno_zoo.mp3',
                'audio_file_url' => 'https://www.soundjay.com/animals/sounds/elephant-1.mp3', // サンプル音声URL
                'duration_seconds' => 480, // 8分
                'format' => 'mp3',
                'file_size' => 7680000, // 約7.6MB
                'voice_actor' => '加藤春香',
                'language' => 'ja',
                'is_active' => true,
            ]
        ];

        foreach ($audioGuides as $audioGuide) {
            \App\Models\AudioGuide::create($audioGuide);
        }
    }
}
