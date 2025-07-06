<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EventGeneratorService
{
    private $openRouterApiKey;
    private $openRouterBaseUrl;
    private $openRouterModel;
    private $cacheKey = 'generated_events_cache';
    private $cacheDuration = 24 * 60 * 60; // 24 hours in seconds

    public function __construct()
    {
        $this->openRouterApiKey = config('services.openrouter.api_key');
        $this->openRouterBaseUrl = config('services.openrouter.base_url');
        $this->openRouterModel = config('services.openrouter.model');
    }

    /**
     * Get events without caching (always generate fresh data)
     */
    public function getEvents(): array
    {
        // Always generate new events (cache disabled)
        Log::info('EventGeneratorService: Generating fresh events (cache disabled)');
        $events = $this->generateEventsFromAI();
        
        Log::info('EventGeneratorService: Fresh events generated', ['count' => count($events)]);
        
        return $events;
    }

    /**
     * Force refresh events cache
     */
    public function refreshEvents(): array
    {
        Cache::forget($this->cacheKey);
        return $this->getEvents();
    }

    /**
     * Generate events using AI via OpenRouter
     */
    private function generateEventsFromAI(): array
    {
        if (!$this->openRouterApiKey) {
            Log::warning('EventGeneratorService: OpenRouter API key not configured');
            return [];
        }

        try {
            $prompt = $this->buildPrompt();
            Log::info('EventGeneratorService: Sending request to OpenRouter API', [
                'model' => $this->openRouterModel,
                'prompt_length' => strlen($prompt)
            ]);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->openRouterApiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->openRouterBaseUrl . '/chat/completions', [
                'model' => $this->openRouterModel,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'あなたは日本の観光とイベント情報に詳しい専門家です。リアルで魅力的なイベント情報を生成してください。'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 16000
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '';
                
                Log::info('EventGeneratorService: Received response from OpenRouter API', [
                    'response_length' => strlen($content),
                    'content_preview' => substr($content, 0, 200)
                ]);
                
                return $this->parseAIResponse($content);
            } else {
                Log::error('EventGeneratorService: OpenRouter API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('EventGeneratorService: Exception occurred', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return [];
    }

    /**
     * Build prompt for AI event generation
     */
    private function buildPrompt(): string
    {
        $currentDate = Carbon::now()->format('Y年m月d日');
        $endDate = Carbon::now()->addMonths(6)->format('Y年m月d日');

        return "日本全国のイベント情報を20個、以下のJSON形式で生成してください。

期間: {$currentDate} ～ {$endDate}
都道府県: 北海道、東京都、大阪府、京都府、愛知県、福岡県、広島県、愛媛県、新潟県、山口県、徳島県、鹿児島県、埼玉県、福島県（各都道府県から1-2個ずつ）

[
  {
    \"title\": \"桜まつり\",
    \"description\": \"美しい桜の花見を楽しめる春のイベント。多くの屋台も出店します。\",
    \"prefecture\": \"東京都\",
    \"location\": \"上野公園\",
    \"start_date\": \"2025-03-25\",
    \"end_date\": \"2025-04-10\",
    \"tags\": [\"桜\", \"花見\", \"春\"],
    \"overview\": \"約1000本の桜が咲き誇る都内有数の花見スポット。屋台多数出店、ライトアップあり。\",
    \"access\": \"JR上野駅公園口から徒歩2分、地下鉄銀座線・日比谷線上野駅から徒歩5分\"
  }
]

注意事項:
- locationには実在する具体的で簡潔な場所名を1つだけ記載してください（例：「上野公園」「大阪城公園」「札幌ドーム」）
- 複数の場所や詳細な住所は含めず、メインとなる会場名のみにしてください
- テスト用の文字列やダミーデータは一切使用しないでください
- 日本の実際の地名や施設名を使用してください
- overviewはイベントの概要を50-80文字程度で簡潔に記載してください
- accessには最寄り駅からの交通アクセス情報を具体的に記載してください（駅名、徒歩時間、路線名など）

上記の例を参考に、20個の異なるイベント情報をJSON配列形式で出力してください。多様なイベント（祭り、グルメ、文化、スポーツ、季節イベント等）を含めてください。説明文は不要で、JSONのみを出力してください。";
    }

    /**
     * Parse AI response and convert to event array
     */
    private function parseAIResponse(string $content): array
    {
        try {
            Log::info('EventGeneratorService: Parsing AI response', [
                'raw_content' => substr($content, 0, 500)
            ]);

            // Remove markdown code blocks if present
            $content = preg_replace('/```json\s*/', '', $content);
            $content = preg_replace('/```\s*$/', '', $content);
            $content = trim($content);

            // Try to find JSON array in the response
            if (preg_match('/\[.*\]/s', $content, $matches)) {
                $content = $matches[0];
            }

            $data = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('EventGeneratorService: JSON decode error', [
                    'error' => json_last_error_msg(),
                    'content' => $content
                ]);
                return [];
            }
            
            if (!is_array($data)) {
                Log::warning('EventGeneratorService: AI response is not an array', [
                    'type' => gettype($data),
                    'data' => $data
                ]);
                return [];
            }

            $events = [];
            foreach ($data as $index => $eventData) {
                if ($this->validateEventData($eventData)) {
                    $events[] = [
                        'id' => $index + 1,
                        'title' => $eventData['title'],
                        'description' => $eventData['description'],
                        'prefecture' => $eventData['prefecture'],
                        'location' => $eventData['location'],
                        'start_date' => $eventData['start_date'],
                        'end_date' => $eventData['end_date'],
                        'image_url' => null,
                        'overview' => $eventData['overview'] ?? null,
                        'access' => $eventData['access'] ?? null,
                        'tags' => $eventData['tags'] ?? [],
                        'is_active' => true,
                        'created_at' => Carbon::now()->toISOString(),
                        'updated_at' => Carbon::now()->toISOString()
                    ];
                } else {
                    Log::warning('EventGeneratorService: Invalid event data', [
                        'index' => $index,
                        'data' => $eventData
                    ]);
                }
            }

            if (empty($events)) {
                Log::warning('EventGeneratorService: No valid events found in AI response');
                return [];
            }

            Log::info('EventGeneratorService: Successfully parsed AI events', ['count' => count($events)]);
            return $events;

        } catch (\Exception $e) {
            Log::error('EventGeneratorService: Failed to parse AI response', [
                'error' => $e->getMessage(),
                'content' => substr($content, 0, 500) // Log first 500 chars for debugging
            ]);
            return [];
        }
    }

    /**
     * Validate event data structure
     */
    private function validateEventData(array $eventData): bool
    {
        $requiredFields = ['title', 'description', 'prefecture', 'location', 'start_date', 'end_date'];
        
        foreach ($requiredFields as $field) {
            if (!isset($eventData[$field]) || empty($eventData[$field])) {
                return false;
            }
        }

        // Validate date format
        try {
            Carbon::createFromFormat('Y-m-d', $eventData['start_date']);
            Carbon::createFromFormat('Y-m-d', $eventData['end_date']);
        } catch (\Exception $e) {
            return false;
        }

        // Validate URL format if provided
        if (isset($eventData['official_url']) && !empty($eventData['official_url']) && $eventData['official_url'] !== null) {
            if (!filter_var($eventData['official_url'], FILTER_VALIDATE_URL)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get fallback events when AI generation fails (reduced to 10 events)
     */
    private function getFallbackEvents(): array
    {
        $fallbackEvents = [];
        
        for ($i = 1; $i <= 10; $i++) {
            $fallbackEvents[] = [
                'id' => $i,
                'title' => "フォールバックイベント{$i}",
                'description' => 'AI生成に失敗した場合のフォールバックデータです。実際のサービスではAI生成されたリアルなイベント情報が表示されます。',
                'prefecture' => ['東京都', '大阪府', '京都府', '北海道', '福岡県'][($i - 1) % 5],
                'location' => "フォールバック会場{$i}",
                'start_date' => Carbon::now()->addDays($i * 7)->format('Y-m-d'),
                'end_date' => Carbon::now()->addDays($i * 7 + 2)->format('Y-m-d'),
                'image_url' => null,
                'overview' => 'テスト用のイベント概要です。実際の運用では詳細な情報が表示されます。',
                'access' => 'テスト駅から徒歩5分',
                'tags' => ['フォールバック', 'テスト'],
                'is_active' => true,
                'created_at' => Carbon::now()->toISOString(),
                'updated_at' => Carbon::now()->toISOString()
            ];
        }
        
        return $fallbackEvents;
    }

    /**
     * Clear events cache
     */
    public function clearCache(): bool
    {
        return Cache::forget($this->cacheKey);
    }

    /**
     * Check if cache exists
     */
    public function isCached(): bool
    {
        return Cache::has($this->cacheKey);
    }

    /**
     * Get cache expiration time
     */
    public function getCacheExpiration(): ?Carbon
    {
        if (!$this->isCached()) {
            return null;
        }

        // This is an approximation since we can't get exact expiration from Laravel cache
        return Carbon::now()->addSeconds($this->cacheDuration);
    }
}