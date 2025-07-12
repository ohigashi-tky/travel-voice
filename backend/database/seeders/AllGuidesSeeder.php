<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class AllGuidesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('全観光地の音声ガイドを生成中...');
        
        // GenerateAllGuidesコマンドを実行
        Artisan::call('guides:generate-all', ['--force' => true]);
        
        $this->command->info('全観光地の音声ガイド生成が完了しました');
    }
}