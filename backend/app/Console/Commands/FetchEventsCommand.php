<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FetchEventsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'events:fetch 
                            {--prefecture= : 特定の都道府県のみ取得}
                            {--force : 既存データを削除して再取得}';

    /**
     * The console command description.
     */
    protected $description = 'AIを使って全国の観光イベント情報を取得してデータベースに保存';

    /**
     * 対象都道府県リスト
     */
    private array $prefectures = [
        '北海道', '東京都', '大阪府', '京都府', '愛知県', '福岡県', 
        '広島県', '愛媛県', '福島県', '埼玉県', '新潟県', '山口県', 
        '徳島県', '鹿児島県', '青森県', '岩手県', '宮城県', '秋田県',
        '山形県', '茨城県', '栃木県', '群馬県', '千葉県', '神奈川県',
        '富山県', '石川県', '福井県', '山梨県', '長野県', '岐阜県',
        '静岡県', '三重県', '滋賀県', '奈良県', '和歌山県', '兵庫県',
        '鳥取県', '島根県', '岡山県', '香川県', '高知県', '佐賀県',
        '長崎県', '熊本県', '大分県', '宮崎県', '沖縄県'
    ];

    /**
     * イベントカテゴリ
     */
    private array $eventCategories = [
        '祭り', 'フェスティバル', 'スポーツ', 'ライブ・コンサート', 
        '展示・展覧会', 'グルメ', '花火大会', '桜祭り', '紅葉祭り',
        '伝統行事', '文化イベント', '季節イベント', 'アニメ・サブカル'
    ];

    public function handle()
    {
        $this->info('=== イベント情報取得バッチ開始 ===');
        
        $targetPrefecture = $this->option('prefecture');
        $forceUpdate = $this->option('force');
        
        // 既存データの削除（--forceオプション時）
        if ($forceUpdate) {
            $this->info('既存のイベントデータを削除中...');
            Event::truncate();
        }
        
        // 対象都道府県の決定
        $prefecturesToProcess = $targetPrefecture ? [$targetPrefecture] : $this->prefectures;
        
        $totalEvents = 0;
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($prefecturesToProcess as $prefecture) {
            $this->info("📍 {$prefecture} のイベント情報を取得中...");
            
            try {
                $events = $this->fetchEventsForPrefecture($prefecture);
                $saved = $this->saveEvents($events, $prefecture);
                
                $successCount += $saved;
                $totalEvents += count($events);
                
                $this->info("✅ {$prefecture}: {$saved}件のイベントを保存");
                
                // API制限を考慮して少し待機
                sleep(2);
                
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("❌ {$prefecture}: エラー - " . $e->getMessage());
                Log::error("イベント取得エラー [{$prefecture}]", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        $this->info('=== 取得完了 ===');
        $this->table(
            ['項目', '件数'],
            [
                ['対象都道府県数', count($prefecturesToProcess)],
                ['取得イベント総数', $totalEvents],
                ['保存成功数', $successCount],
                ['エラー数', $errorCount],
            ]
        );
        
        return Command::SUCCESS;
    }

    /**
     * 特定都道府県のイベント情報をAI経由で取得
     */
    private function fetchEventsForPrefecture(string $prefecture): array
    {
        $prompt = $this->buildEventPrompt($prefecture);
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openrouter.api_key'),
            'Content-Type' => 'application/json',
        ])->timeout(120)->post(config('services.openrouter.base_url') . '/chat/completions', [
            'model' => config('services.openrouter.model'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'あなたは日本の観光イベント情報の専門家です。正確で最新の情報を提供し、JSON形式で回答してください。'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.7,
            'max_tokens' => 8000,
        ]);

        if (!$response->successful()) {
            throw new \Exception("OpenRouter API エラー: " . $response->body());
        }

        $data = $response->json();
        $content = $data['choices'][0]['message']['content'] ?? '';
        
        // EventGeneratorServiceと同じJSON解析処理
        // Remove markdown code blocks if present
        $content = preg_replace('/```json\s*/', '', $content);
        $content = preg_replace('/```\s*$/', '', $content);
        $content = trim($content);

        // Try to find JSON array in the response
        if (preg_match('/\[.*\]/s', $content, $matches)) {
            $content = $matches[0];
        }

        $events = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("JSON解析エラー: " . json_last_error_msg() . " - Content: " . substr($content, 0, 200));
        }
        
        if (!is_array($events)) {
            throw new \Exception("AI応答が配列ではありません: " . gettype($events));
        }
        
        return $events;
    }

    /**
     * イベント取得用のプロンプトを生成
     */
    private function buildEventPrompt(string $prefecture): string
    {
        $categories = implode('、', $this->eventCategories);
        $currentYear = date('Y');
        $nextYear = $currentYear + 1;
        
        return "{$prefecture}のイベント情報を20個、以下のJSON形式で生成してください。

期間: {$currentYear}年から{$nextYear}年
カテゴリ: {$categories}

[
  {
    \"title\": \"桜まつり\",
    \"description\": \"美しい桜の花見を楽しめる春のイベント。多くの屋台も出店します。\",
    \"prefecture\": \"{$prefecture}\",
    \"location\": \"上野公園\",
    \"start_date\": \"2025-03-25\",
    \"end_date\": \"2025-04-10\",
    \"tags\": [\"桜\", \"花見\", \"春\"],
    \"overview\": \"約1000本の桜が咲き誇る都内有数の花見スポット。屋台多数出店、ライトアップあり。\",
    \"access\": \"JR上野駅公園口から徒歩2分、地下鉄銀座線・日比谷線上野駅から徒歩5分\"
  }
]

注意事項:
- locationには実在する具体的で簡潔な場所名を1つだけ記載してください
- テスト用の文字列やダミーデータは一切使用しないでください
- 日本の実際の地名や施設名を使用してください
- overviewはイベントの概要を50-80文字程度で簡潔に記載してください
- accessには最寄り駅からの交通アクセス情報を具体的に記載してください

上記の例を参考に、20個の異なるイベント情報をJSON配列形式で出力してください。説明文は不要で、JSONのみを出力してください。";
    }

    /**
     * 取得したイベントをデータベースに保存
     */
    private function saveEvents(array $events, string $prefecture): int
    {
        $savedCount = 0;
        
        foreach ($events as $index => $eventData) {
            try {
                // 必須フィールドの検証
                if (empty($eventData['title']) || empty($eventData['start_date'])) {
                    $this->warn("スキップ: 必須フィールドが不足 - " . ($eventData['title'] ?? 'タイトル未設定'));
                    continue;
                }
                
                // 重複チェック（タイトルと開始日で判定）
                $existing = Event::where('title', $eventData['title'])
                    ->where('prefecture', $prefecture)
                    ->where('start_date', $eventData['start_date'])
                    ->first();
                    
                if ($existing) {
                    continue; // 既存データはスキップ
                }
                
                // 日付の検証と変換
                $startDate = $this->parseDate($eventData['start_date']);
                $endDate = isset($eventData['end_date']) 
                    ? $this->parseDate($eventData['end_date']) 
                    : $startDate;
                
                Event::create([
                    'title' => $eventData['title'],
                    'description' => $eventData['description'] ?? '',
                    'overview' => $eventData['overview'] ?? '',
                    'prefecture' => $prefecture,
                    'location' => $eventData['location'] ?? '',
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'category' => $eventData['category'] ?? 'その他',
                    'tags' => $eventData['tags'] ?? [],
                    'access' => $eventData['access'] ?? '',
                    'url' => $eventData['url'] ?? '',
                    'organizer' => $eventData['organizer'] ?? '',
                    'display_order' => $index,
                    'is_active' => true,
                ]);
                
                $savedCount++;
                
            } catch (\Exception $e) {
                $this->warn("イベント保存エラー: " . ($eventData['title'] ?? '不明') . " - " . $e->getMessage());
            }
        }
        
        return $savedCount;
    }

    /**
     * 日付文字列を解析してCarbonインスタンスを返す
     */
    private function parseDate(string $dateString): Carbon
    {
        try {
            return Carbon::parse($dateString);
        } catch (\Exception $e) {
            // パースに失敗した場合は今日の日付を返す
            $this->warn("日付解析エラー: {$dateString} - 今日の日付を使用");
            return Carbon::today();
        }
    }
}