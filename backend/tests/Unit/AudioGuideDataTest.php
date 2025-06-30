<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Http\Controllers\Api\AudioGuideController;
use App\Services\PollyService;

/**
 * 音声ガイドデータ構造テストクラス
 * 
 * AudioGuideControllerのデータ構造と整合性をテストする
 */
class AudioGuideDataTest extends TestCase
{
    private AudioGuideController $controller;
    private \ReflectionClass $reflection;

    protected function setUp(): void
    {
        parent::setUp();
        
        // PollyServiceのモックを作成
        $pollyServiceMock = $this->createMock(PollyService::class);
        
        $this->controller = new AudioGuideController($pollyServiceMock);
        $this->reflection = new \ReflectionClass($this->controller);
    }

    /**
     * 全ての定義済み観光地が適切なデータ構造を持っているかテスト
     */
    public function test_all_tourist_spots_have_required_data_structure(): void
    {
        $method = $this->reflection->getMethod('getTouristSpotData');
        $method->setAccessible(true);

        $requiredFields = ['id', 'name', 'description', 'history', 'episodes', 'highlights', 'cultural_significance'];
        $spotIds = $this->getAllDefinedSpotIds();

        $missingFieldsReport = [];

        foreach ($spotIds as $spotId) {
            $spotData = $method->invoke($this->controller, $spotId);
            
            if (!$spotData) {
                $missingFieldsReport[$spotId] = ['データが存在しません'];
                continue;
            }

            $missingFields = [];
            foreach ($requiredFields as $field) {
                if (!isset($spotData[$field])) {
                    $missingFields[] = $field;
                }
            }

            if (!empty($missingFields)) {
                $missingFieldsReport[$spotId] = $missingFields;
            }

            // データ型の検証
            if (isset($spotData['episodes']) && !is_array($spotData['episodes'])) {
                $missingFieldsReport[$spotId][] = 'episodes は配列である必要があります';
            }

            if (isset($spotData['highlights']) && !is_array($spotData['highlights'])) {
                $missingFieldsReport[$spotId][] = 'highlights は配列である必要があります';
            }
        }

        $this->assertEmpty($missingFieldsReport, 
            "以下の観光地でデータ構造の問題があります: " . json_encode($missingFieldsReport, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 音声ガイドテキスト生成ロジックのテスト
     */
    public function test_guide_text_generation_logic(): void
    {
        $method = $this->reflection->getMethod('generateGuideText');
        $method->setAccessible(true);

        // テスト用のサンプルデータ
        $sampleSpotData = [
            'name' => 'テスト観光地',
            'description' => 'テスト用の観光地です。',
            'history' => 'テスト観光地は1000年前に建設されました。',
            'episodes' => [
                'エピソード1です。',
                'エピソード2です。',
                'エピソード3です。'
            ],
            'highlights' => ['見どころ1', '見どころ2', '見どころ3'],
            'cultural_significance' => 'テスト文化の重要な場所です。'
        ];

        // 日本語版テスト
        $jaText = $method->invoke($this->controller, $sampleSpotData, 'ja');
        
        $this->assertStringContainsString('ようこそ、テスト観光地へお越しくださいました', $jaText);
        $this->assertStringContainsString('テスト用の観光地です', $jaText);
        $this->assertStringContainsString('1000年前に建設されました', $jaText);
        $this->assertStringContainsString('1つ目として、エピソード1です', $jaText);
        $this->assertStringContainsString('2つ目として、エピソード2です', $jaText);
        $this->assertStringContainsString('3つ目として、エピソード3です', $jaText);
        $this->assertStringContainsString('見どころ1、見どころ2、見どころ3', $jaText);
        $this->assertStringContainsString('テスト文化の重要な場所です', $jaText);

        // 英語版テスト
        $enText = $method->invoke($this->controller, $sampleSpotData, 'en');
        
        $this->assertStringContainsString('Welcome to テスト観光地', $enText);
        $this->assertStringContainsString('テスト用の観光地です', $enText);
        $this->assertStringContainsString('見どころ1, 見どころ2, 見どころ3', $enText);
    }

    /**
     * エラーハンドリングのテスト
     */
    public function test_error_handling_for_invalid_data(): void
    {
        $method = $this->reflection->getMethod('generateGuideText');
        $method->setAccessible(true);

        // 不完全なデータでのテスト
        $incompleteData = [
            'name' => 'テスト観光地',
            'description' => 'テスト用の観光地です。',
            // history, episodes, highlights, cultural_significance が欠如
        ];

        // エラーが発生せずに適切にハンドリングされることを確認
        $result = $method->invoke($this->controller, $incompleteData, 'ja');
        
        $this->assertIsString($result);
        $this->assertStringContainsString('ようこそ、テスト観光地へお越しくださいました', $result);
        $this->assertStringContainsString('テスト用の観光地です', $result);
    }

    /**
     * データの一意性テスト
     */
    public function test_spot_data_uniqueness(): void
    {
        $method = $this->reflection->getMethod('getTouristSpotData');
        $method->setAccessible(true);

        $spotIds = $this->getAllDefinedSpotIds();
        $spotNames = [];
        $duplicateNames = [];

        foreach ($spotIds as $spotId) {
            $spotData = $method->invoke($this->controller, $spotId);
            
            if ($spotData && isset($spotData['name'])) {
                $name = $spotData['name'];
                if (in_array($name, $spotNames)) {
                    $duplicateNames[] = $name;
                } else {
                    $spotNames[] = $name;
                }
            }
        }

        $this->assertEmpty($duplicateNames, 
            "重複する観光地名が検出されました: " . implode(', ', $duplicateNames));
    }

    /**
     * 定義済みの全観光地IDを取得
     */
    private function getAllDefinedSpotIds(): array
    {
        return [
            // 東京都 (1-99)
            1, 2, 3, 4, 5, 6,
            
            // 大阪府 (100-199)  
            101, 102, 103, 104, 105, 106,
            
            // 京都府 (200-299)
            201, 202, 203,
            
            // 北海道 (300-399)
            301, 302, 303,
            
            // 愛知県 (400-499)
            401, 402, 403,
            
            // 福岡県 (500-599)
            501, 502, 503,
            
            // 広島県 (600-699)
            601, 602, 603,
            
            // 福島県 (700-799)
            701, 702,
            
            // 愛媛県 (800-899)
            801, 802,
            
            // 埼玉県 (900-999)
            901, 902, 903,
            
            // 新潟県 (1000-1099)
            1001, 1002, 1003,
            
            // 山口県 (1100-1199)
            1101, 1102, 1103,
            
            // 徳島県 (1200-1299)
            1201, 1202, 1203,
            
            // 鹿児島県 (1300-1399)
            1301, 1302, 1303,
        ];
    }

    /**
     * 文字エンコーディングテスト
     */
    public function test_text_encoding_compatibility(): void
    {
        $method = $this->reflection->getMethod('getTouristSpotData');
        $method->setAccessible(true);

        $generateMethod = $this->reflection->getMethod('generateGuideText');
        $generateMethod->setAccessible(true);

        $spotIds = [1, 901, 1001]; // サンプル

        foreach ($spotIds as $spotId) {
            $spotData = $method->invoke($this->controller, $spotId);
            
            if ($spotData) {
                $guideText = $generateMethod->invoke($this->controller, $spotData, 'ja');
                
                // UTF-8エンコーディングの確認
                $this->assertTrue(mb_check_encoding($guideText, 'UTF-8'), 
                    "観光地ID {$spotId} のガイドテキストがUTF-8エンコーディングではありません");
                
                // 制御文字が含まれていないことを確認
                $this->assertSame($guideText, preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $guideText),
                    "観光地ID {$spotId} のガイドテキストに制御文字が含まれています");
            }
        }
    }

    /**
     * テキスト長の妥当性テスト
     */
    public function test_text_length_validity(): void
    {
        $method = $this->reflection->getMethod('getTouristSpotData');
        $method->setAccessible(true);

        $generateMethod = $this->reflection->getMethod('generateGuideText');
        $generateMethod->setAccessible(true);

        $spotIds = $this->getAllDefinedSpotIds();
        $minLength = 100;  // 最小文字数
        $maxLength = 2000; // 最大文字数（Pollyの制限を考慮）

        $lengthIssues = [];

        foreach ($spotIds as $spotId) {
            $spotData = $method->invoke($this->controller, $spotId);
            
            if ($spotData) {
                $guideText = $generateMethod->invoke($this->controller, $spotData, 'ja');
                $length = mb_strlen($guideText, 'UTF-8');
                
                if ($length < $minLength) {
                    $lengthIssues[$spotId] = "短すぎます ({$length}文字)";
                } elseif ($length > $maxLength) {
                    $lengthIssues[$spotId] = "長すぎます ({$length}文字)";
                }
            }
        }

        $this->assertEmpty($lengthIssues, 
            "以下の観光地でテキスト長の問題があります: " . json_encode($lengthIssues, JSON_UNESCAPED_UNICODE));
    }
}