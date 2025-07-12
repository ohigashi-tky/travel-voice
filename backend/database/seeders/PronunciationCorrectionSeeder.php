<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PronunciationCorrection;

class PronunciationCorrectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $corrections = [
            // 清水関連（優先度高）
            ['original_text' => '清水寺', 'pronunciation' => 'きよみずでら', 'priority' => 100],
            ['original_text' => '清水', 'pronunciation' => 'きよみず', 'priority' => 50],
            
            // 寺院
            ['original_text' => '金閣寺', 'pronunciation' => 'きんかくじ', 'priority' => 90],
            ['original_text' => '銀閣寺', 'pronunciation' => 'ぎんかくじ', 'priority' => 90],
            ['original_text' => '東大寺', 'pronunciation' => 'とうだいじ', 'priority' => 90],
            ['original_text' => '法隆寺', 'pronunciation' => 'ほうりゅうじ', 'priority' => 90],
            ['original_text' => '浅草寺', 'pronunciation' => 'せんそうじ', 'priority' => 90],
            ['original_text' => '善光寺', 'pronunciation' => 'ぜんこうじ', 'priority' => 90],
            
            // 神社・宮
            ['original_text' => '平等院', 'pronunciation' => 'びょうどういん', 'priority' => 90],
            ['original_text' => '厳島神社', 'pronunciation' => 'いつくしまじんじゃ', 'priority' => 90],
            ['original_text' => '出雲大社', 'pronunciation' => 'いずもたいしゃ', 'priority' => 90],
            ['original_text' => '伊勢神宮', 'pronunciation' => 'いせじんぐう', 'priority' => 90],
            ['original_text' => '日光東照宮', 'pronunciation' => 'にっこうとうしょうぐう', 'priority' => 90],
            
            // 自然・山
            ['original_text' => '富士山', 'pronunciation' => 'ふじさん', 'priority' => 80],
            ['original_text' => '高野山', 'pronunciation' => 'こうやさん', 'priority' => 80],
            ['original_text' => '比叡山', 'pronunciation' => 'ひえいざん', 'priority' => 80],
            
            // 庭園・その他
            ['original_text' => '兼六園', 'pronunciation' => 'けんろくえん', 'priority' => 70],
            ['original_text' => '偕楽園', 'pronunciation' => 'かいらくえん', 'priority' => 70],
            ['original_text' => '後楽園', 'pronunciation' => 'こうらくえん', 'priority' => 70],
            ['original_text' => '白川郷', 'pronunciation' => 'しらかわごう', 'priority' => 70],
            ['original_text' => '五箇山', 'pronunciation' => 'ごかやま', 'priority' => 70],
        ];

        foreach ($corrections as $correction) {
            PronunciationCorrection::firstOrCreate(
                ['original_text' => $correction['original_text']],
                $correction
            );
        }
    }
}
