<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\AudioGuideController;
use App\Services\PollyService;

/**
 * 音声ガイド品質チェックコマンド
 * 
 * 全ての観光地の音声ガイドが汎用テンプレートではなく、
 * 詳細で質の高いコンテンツを提供しているかをコマンドラインから検証する
 */
class AudioGuideQualityCheck extends Command
{
    /**
     * コマンドの署名
     */
    protected $signature = 'audio-guide:quality-check 
                            {--spot-id= : 特定の観光地IDのみをチェック}
                            {--detailed : 詳細な出力を表示}
                            {--export-report : レポートファイルを出力}';

    /**
     * コマンドの説明
     */
    protected $description = '全ての観光地の音声ガイド品質をチェックし、汎用テンプレートの使用を検出します';

    /**
     * 全観光地ID一覧
     */
    private array $allSpotIds = [
        1, 2, 3, 4, 5, 6,                          // 東京都
        101, 102, 103, 104, 105, 106,              // 大阪府
        201, 202, 203,                              // 京都府
        301, 302, 303,                              // 北海道
        401, 402, 403,                              // 愛知県
        501, 502, 503,                              // 福岡県
        601, 602, 603,                              // 広島県
        701, 702,                                   // 福島県
        801, 802,                                   // 愛媛県
        901, 902, 903,                              // 埼玉県
        1001, 1002, 1003,                          // 新潟県
        1101, 1102, 1103,                          // 山口県
        1201, 1202, 1203,                          // 徳島県
        1301, 1302, 1303,                          // 鹿児島県
    ];

    /**
     * 汎用テンプレート検出パターン
     */
    private array $genericPatterns = [
        '多くの興味深いエピソードがあります',
        '訪れる人々に感動と発見を提供し続けています',
        '歴史的価値、文化的意義、美しい景観',
        '観光地#',
        '日本を代表する素晴らしい観光地です。歴史と文化が息づく魅力的な場所で、多くの訪問者に愛され続けています',
    ];

    /**
     * コマンド実行
     */
    public function handle()
    {
        $this->info('🔍 音声ガイド品質チェックを開始します...');
        $this->newLine();

        // チェック対象のスポットIDを決定
        $targetSpotIds = $this->option('spot-id') 
            ? [(int)$this->option('spot-id')] 
            : $this->allSpotIds;

        $controller = app(AudioGuideController::class);
        $results = [];
        $issueCount = 0;

        // プログレスバーを初期化
        $progressBar = $this->output->createProgressBar(count($targetSpotIds));
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');

        foreach ($targetSpotIds as $spotId) {
            $result = $this->checkSpotQuality($controller, $spotId);
            $results[$spotId] = $result;

            if (!$result['is_quality']) {
                $issueCount++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // 結果の表示
        $this->displayResults($results, $issueCount);

        // レポート出力
        if ($this->option('export-report')) {
            $this->exportReport($results);
        }

        // 終了コード
        return $issueCount === 0 ? Command::SUCCESS : Command::FAILURE;
    }

    /**
     * 特定の観光地の品質をチェック
     */
    private function checkSpotQuality(AudioGuideController $controller, int $spotId): array
    {
        try {
            // リフレクションを使用してprivateメソッドにアクセス
            $reflection = new \ReflectionClass($controller);
            
            $getDataMethod = $reflection->getMethod('getTouristSpotData');
            $getDataMethod->setAccessible(true);
            
            $generateMethod = $reflection->getMethod('generateGuideText');
            $generateMethod->setAccessible(true);

            // データ取得
            $spotData = $getDataMethod->invoke($controller, $spotId);
            
            if (!$spotData) {
                return [
                    'is_quality' => false,
                    'spot_name' => "Unknown Spot #$spotId",
                    'issues' => ['観光地データが見つかりません'],
                    'guide_text' => '',
                ];
            }

            // ガイドテキスト生成
            $guideText = $generateMethod->invoke($controller, $spotData, 'ja');

            // 品質分析
            $analysis = $this->analyzeQuality($guideText);

            return [
                'is_quality' => empty($analysis['issues']),
                'spot_name' => $spotData['name'],
                'issues' => $analysis['issues'],
                'guide_text' => $guideText,
                'metrics' => $analysis['metrics'],
            ];

        } catch (\Exception $e) {
            return [
                'is_quality' => false,
                'spot_name' => "Error Spot #$spotId",
                'issues' => ["エラーが発生しました: " . $e->getMessage()],
                'guide_text' => '',
            ];
        }
    }

    /**
     * ガイドテキストの品質を分析
     */
    private function analyzeQuality(string $guideText): array
    {
        $issues = [];
        $metrics = [];

        // 1. 汎用テンプレート検出
        foreach ($this->genericPatterns as $pattern) {
            if (strpos($guideText, $pattern) !== false) {
                $issues[] = "汎用テンプレート検出: {$pattern}";
            }
        }

        // 2. 文字数チェック
        $textLength = mb_strlen($guideText, 'UTF-8');
        $metrics['text_length'] = $textLength;
        if ($textLength < 200) {
            $issues[] = "文字数不足: {$textLength}文字";
        }

        // 3. エピソード数チェック
        $episodeCount = preg_match_all('/\d+つ目として/', $guideText);
        $metrics['episode_count'] = $episodeCount;
        if ($episodeCount < 2) {
            $issues[] = "エピソード不足: {$episodeCount}個";
        }

        // 4. 具体的な情報の確認
        $hasNumbers = preg_match('/\d+/', $guideText);
        $metrics['has_numbers'] = $hasNumbers;
        if (!$hasNumbers) {
            $issues[] = "具体的な数値情報が不足";
        }

        // 5. 年代情報の確認
        $hasYearInfo = preg_match('/\d{4}年|\d+世紀|[明治|大正|昭和|平成|令和]/', $guideText);
        $metrics['has_year_info'] = $hasYearInfo;
        if (!$hasYearInfo) {
            $issues[] = "年代情報が不足";
        }

        return [
            'issues' => $issues,
            'metrics' => $metrics,
        ];
    }

    /**
     * 結果を表示
     */
    private function displayResults(array $results, int $issueCount): void
    {
        $totalSpots = count($results);
        $qualitySpots = $totalSpots - $issueCount;

        // サマリー表示
        $this->info("📊 チェック結果サマリー");
        $this->table(
            ['項目', '値'],
            [
                ['総観光地数', $totalSpots],
                ['品質OK', $qualitySpots],
                ['要改善', $issueCount],
                ['品質スコア', sprintf('%.1f%%', ($qualitySpots / $totalSpots) * 100)],
            ]
        );

        // 問題のある観光地の詳細表示
        if ($issueCount > 0) {
            $this->newLine();
            $this->error("❌ 要改善の観光地一覧:");
            
            $problemSpots = [];
            foreach ($results as $spotId => $result) {
                if (!$result['is_quality']) {
                    $problemSpots[] = [
                        'ID' => $spotId,
                        '観光地名' => $result['spot_name'],
                        '問題' => implode(', ', $result['issues']),
                    ];
                }
            }

            $this->table(['ID', '観光地名', '問題'], $problemSpots);
        } else {
            $this->newLine();
            $this->info("✅ 全ての観光地が品質基準を満たしています！");
        }

        // 詳細表示オプション
        if ($this->option('detailed')) {
            $this->displayDetailedResults($results);
        }
    }

    /**
     * 詳細結果を表示
     */
    private function displayDetailedResults(array $results): void
    {
        $this->newLine();
        $this->info("📋 詳細分析結果:");

        foreach ($results as $spotId => $result) {
            $this->newLine();
            $status = $result['is_quality'] ? '✅' : '❌';
            $this->line("$status ID {$spotId}: {$result['spot_name']}");
            
            if (isset($result['metrics'])) {
                $metrics = $result['metrics'];
                $this->line("  文字数: {$metrics['text_length']}");
                $this->line("  エピソード数: {$metrics['episode_count']}");
                $this->line("  数値情報: " . ($metrics['has_numbers'] ? 'あり' : 'なし'));
                $this->line("  年代情報: " . ($metrics['has_year_info'] ? 'あり' : 'なし'));
            }

            if (!empty($result['issues'])) {
                $this->line("  問題: " . implode(', ', $result['issues']));
            }
        }
    }

    /**
     * レポートをファイルに出力
     */
    private function exportReport(array $results): void
    {
        $reportData = [
            'check_date' => now()->toISOString(),
            'total_spots' => count($results),
            'quality_spots' => count(array_filter($results, fn($r) => $r['is_quality'])),
            'issue_spots' => count(array_filter($results, fn($r) => !$r['is_quality'])),
            'results' => $results,
        ];

        $reportPath = storage_path('logs/audio_guide_quality_report_' . date('Y-m-d_H-i-s') . '.json');
        file_put_contents($reportPath, json_encode($reportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("📄 詳細レポートを出力しました: $reportPath");
    }
}