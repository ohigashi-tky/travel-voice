<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\Api\AudioGuideController;
use Illuminate\Http\Request;

/**
 * 音声ガイド品質テストクラス
 * 
 * 全ての観光地の音声ガイドが汎用テンプレートではなく、
 * 詳細で質の高いコンテンツを提供しているかを検証する
 */
class AudioGuideQualityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * テスト対象の観光地ID一覧
     * AudioGuideControllerで定義されている全ての観光地ID
     */
    private array $touristSpotIds = [
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

    /**
     * 汎用テンプレートの特徴となるフレーズ
     * これらが含まれている場合は汎用テンプレートと判定
     */
    private array $genericTemplatePatterns = [
        '多くの興味深いエピソードがあります',
        '訪れる人々に感動と発見を提供し続けています',
        '歴史的価値、文化的意義、美しい景観',
        '地域の文化と歴史を代表する重要な観光地として、多くの人々に愛されています',
        '観光地#',
        '日本を代表する素晴らしい観光地です。歴史と文化が息づく魅力的な場所で、多くの訪問者に愛され続けています',
        '長い歴史を持つ.*は、その地域の文化と伝統を象徴する重要な場所として、代々受け継がれてきました',
    ];

    /**
     * 質の高い音声ガイドの最小要件
     */
    private array $qualityRequirements = [
        'min_length' => 200,        // 最小文字数
        'min_episodes' => 2,        // 最小エピソード数（〜つ目として）
        'requires_specific_history' => true,  // 具体的な歴史情報
        'requires_highlights' => true,        // 具体的な見どころ
    ];

    /**
     * メインテスト: 全ての観光地が汎用テンプレートを使用していないことを確認
     */
    public function test_all_tourist_spots_have_non_generic_audio_guides(): void
    {
        $failedSpots = [];
        $detailedResults = [];

        foreach ($this->touristSpotIds as $spotId) {
            $result = $this->analyzeAudioGuideQuality($spotId);
            $detailedResults[$spotId] = $result;

            if (!$result['is_quality']) {
                $failedSpots[] = [
                    'spot_id' => $spotId,
                    'spot_name' => $result['spot_name'],
                    'issues' => $result['issues'],
                ];
            }
        }

        // 詳細な結果をファイルに出力（デバッグ用）
        $this->outputDetailedReport($detailedResults);

        // テスト結果を検証
        if (!empty($failedSpots)) {
            $failureMessage = "以下の観光地で汎用テンプレートまたは品質の問題が検出されました:\n";
            foreach ($failedSpots as $failed) {
                $failureMessage .= "- ID {$failed['spot_id']} ({$failed['spot_name']}): " . 
                                   implode(', ', $failed['issues']) . "\n";
            }
            
            $this->fail($failureMessage);
        }

        // 全ての観光地が品質基準を満たしていることを確認
        $this->assertEmpty($failedSpots, "全ての観光地が詳細な音声ガイドを持つ必要があります");
        
        // 成功メッセージ
        $totalSpots = count($this->touristSpotIds);
        $this->addToAssertionCount(1);
        echo "\n✅ テスト成功: {$totalSpots}箇所の観光地全てで詳細な音声ガイドが確認されました\n";
    }

    /**
     * 個別テスト: 特定の観光地の音声ガイド品質を詳細分析
     */
    public function test_specific_tourist_spot_quality(): void
    {
        // 川越（ID: 901）の詳細テスト
        $kawagoeResult = $this->analyzeAudioGuideQuality(901);
        
        $this->assertTrue($kawagoeResult['is_quality'], 
            "川越の音声ガイドは品質基準を満たす必要があります: " . implode(', ', $kawagoeResult['issues']));
        
        // 具体的な内容が含まれているかチェック
        $guideText = $kawagoeResult['guide_text'];
        $this->assertStringContainsString('太田道灌', $guideText, '川越の音声ガイドには太田道灌の歴史が含まれる必要があります');
        $this->assertStringContainsString('小江戸', $guideText, '川越の音声ガイドには小江戸の説明が含まれる必要があります');
        $this->assertStringContainsString('蔵造り', $guideText, '川越の音声ガイドには蔵造りの説明が含まれる必要があります');
    }

    /**
     * 既存の詳細観光地が保護されていることを確認するテスト
     */
    public function test_existing_detailed_spots_are_preserved(): void
    {
        // 東京スカイツリー（ID: 1）の詳細テスト
        $skytreeResult = $this->analyzeAudioGuideQuality(1);
        
        $this->assertTrue($skytreeResult['is_quality'], 
            "東京スカイツリーの音声ガイドは詳細である必要があります");
        
        // 具体的な内容が含まれているかチェック
        $guideText = $skytreeResult['guide_text'];
        $this->assertStringContainsString('心柱制振', $guideText, '東京スカイツリーの技術的説明が含まれる必要があります');
        $this->assertStringContainsString('新東京タワー', $guideText, '建設時の名前の変遷が含まれる必要があります');
        $this->assertStringContainsString('634', $guideText, '具体的な高さが含まれる必要があります');
    }

    /**
     * 音声ガイドの品質を分析する
     */
    private function analyzeAudioGuideQuality(int $spotId): array
    {
        // AudioGuideControllerを直接呼び出してデータを取得
        $controller = new AudioGuideController(app(\App\Services\PollyService::class));
        
        // getTouristSpotDataメソッドを呼び出すためのリフレクションを使用
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('getTouristSpotData');
        $method->setAccessible(true);
        
        $spotData = $method->invoke($controller, $spotId);
        
        if (!$spotData) {
            return [
                'is_quality' => false,
                'spot_name' => "Unknown Spot #$spotId",
                'guide_text' => '',
                'issues' => ['観光地データが見つかりません'],
                'analysis' => [],
            ];
        }

        // 音声ガイドテキストを生成
        $generateMethod = $reflection->getMethod('generateGuideText');
        $generateMethod->setAccessible(true);
        
        $guideText = $generateMethod->invoke($controller, $spotData, 'ja');
        
        // 品質分析
        $analysis = $this->performQualityAnalysis($guideText, $spotData);
        
        return [
            'is_quality' => $analysis['is_quality'],
            'spot_name' => $spotData['name'],
            'guide_text' => $guideText,
            'issues' => $analysis['issues'],
            'analysis' => $analysis,
        ];
    }

    /**
     * 音声ガイドテキストの品質を分析
     */
    private function performQualityAnalysis(string $guideText, array $spotData): array
    {
        $issues = [];
        $metrics = [];

        // 1. 汎用テンプレートパターンの検出
        foreach ($this->genericTemplatePatterns as $pattern) {
            if (preg_match("/$pattern/u", $guideText)) {
                $issues[] = "汎用テンプレートパターンを検出: $pattern";
            }
        }

        // 2. 文字数チェック
        $textLength = mb_strlen($guideText, 'UTF-8');
        $metrics['text_length'] = $textLength;
        if ($textLength < $this->qualityRequirements['min_length']) {
            $issues[] = "文字数が不足: {$textLength}文字（最小{$this->qualityRequirements['min_length']}文字必要）";
        }

        // 3. エピソード数のチェック
        $episodeCount = preg_match_all('/(\d+)つ目として/', $guideText);
        $metrics['episode_count'] = $episodeCount;
        if ($episodeCount < $this->qualityRequirements['min_episodes']) {
            $issues[] = "エピソード数が不足: {$episodeCount}個（最小{$this->qualityRequirements['min_episodes']}個必要）";
        }

        // 4. 具体性のチェック
        $hasSpecificHistory = $this->hasSpecificHistoricalContent($guideText);
        $metrics['has_specific_history'] = $hasSpecificHistory;
        if (!$hasSpecificHistory) {
            $issues[] = "具体的な歴史情報が不足";
        }

        // 5. 見どころの具体性チェック
        $hasSpecificHighlights = $this->hasSpecificHighlights($guideText, $spotData);
        $metrics['has_specific_highlights'] = $hasSpecificHighlights;
        if (!$hasSpecificHighlights) {
            $issues[] = "具体的な見どころ情報が不足";
        }

        // 6. 数値・固有名詞の有無チェック
        $hasNumbers = preg_match('/\d+/', $guideText);
        $metrics['has_numbers'] = $hasNumbers;
        if (!$hasNumbers) {
            $issues[] = "具体的な数値情報が不足";
        }

        return [
            'is_quality' => empty($issues),
            'issues' => $issues,
            'metrics' => $metrics,
        ];
    }

    /**
     * 具体的な歴史情報が含まれているかチェック
     */
    private function hasSpecificHistoricalContent(string $text): bool
    {
        $historicalPatterns = [
            '/\d{4}年/',           // 具体的な年号
            '/\d+世紀/',           // 世紀の記述
            '/[明治|大正|昭和|平成|令和]/',  // 年号
            '/[築城|建設|創建|開業|完成]/',  // 建設関連の動詞
        ];

        foreach ($historicalPatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 具体的な見どころが含まれているかチェック
     */
    private function hasSpecificHighlights(string $text, array $spotData): bool
    {
        // 汎用的な見どころキーワードを除外
        $genericKeywords = ['歴史的価値', '文化的意義', '美しい景観'];
        
        // 見どころの部分を抽出
        if (preg_match('/主な見どころは、(.+?)となって/', $text, $matches)) {
            $highlightsText = $matches[1];
            
            // 汎用キーワードのみで構成されているかチェック
            foreach ($genericKeywords as $keyword) {
                $highlightsText = str_replace($keyword, '', $highlightsText);
            }
            
            // 残った文字数で判定
            return mb_strlen(trim($highlightsText, '、'), 'UTF-8') > 10;
        }

        return false;
    }

    /**
     * 詳細なテスト結果をファイルに出力
     */
    private function outputDetailedReport(array $results): void
    {
        $reportPath = storage_path('logs/audio_guide_quality_report.json');
        
        $report = [
            'test_date' => now()->toISOString(),
            'total_spots' => count($results),
            'passed_spots' => count(array_filter($results, fn($r) => $r['is_quality'])),
            'failed_spots' => count(array_filter($results, fn($r) => !$r['is_quality'])),
            'results' => $results,
        ];

        file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        echo "\n📊 詳細レポートを出力しました: $reportPath\n";
    }

    /**
     * パフォーマンステスト: 音声ガイド生成速度
     */
    public function test_audio_guide_generation_performance(): void
    {
        $sampleSpotIds = [1, 901, 1001]; // 既存、新規、追加スポット
        $maxExecutionTime = 5.0; // 秒

        foreach ($sampleSpotIds as $spotId) {
            $startTime = microtime(true);
            
            $result = $this->analyzeAudioGuideQuality($spotId);
            
            $executionTime = microtime(true) - $startTime;
            
            $this->assertLessThan($maxExecutionTime, $executionTime, 
                "観光地ID {$spotId} の音声ガイド生成が{$maxExecutionTime}秒を超えています: {$executionTime}秒");
            
            $this->assertTrue($result['is_quality'], 
                "観光地ID {$spotId} の音声ガイド品質テストが失敗しました");
        }
    }
}