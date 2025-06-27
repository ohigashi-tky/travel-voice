<?php

namespace App\Http\Controllers;

use App\Models\TouristSpot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PopularSpotsController extends Controller
{
    /**
     * Get popular spots analyzed by AI
     */
    public function getPopularSpots(Request $request): JsonResponse
    {
        try {
            // Check cache first (cache for 1 day)
            $cacheKey = 'ai_popular_spots';
            $popularSpots = Cache::get($cacheKey);

            if (!$popularSpots) {
                // Get all tourist spots
                $allSpots = TouristSpot::where('is_active', true)->get();

                if ($allSpots->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No tourist spots found'
                    ], 404);
                }

                // Analyze spots with AI
                $popularSpots = $this->analyzePopularityWithAI($allSpots);

                // Cache the results for 1 day (24 hours)
                Cache::put($cacheKey, $popularSpots, 86400);
            }

            return response()->json([
                'success' => true,
                'data' => $popularSpots
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting popular spots: ' . $e->getMessage());
            
            // Fallback to random selection
            $fallbackSpots = TouristSpot::where('is_active', true)
                ->inRandomOrder()
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $fallbackSpots,
                'fallback' => true,
                'message' => 'Using random selection due to AI analysis error'
            ]);
        }
    }

    /**
     * Analyze tourist spots popularity using AI
     */
    private function analyzePopularityWithAI($spots)
    {
        try {
            // Prepare data for AI analysis
            $spotsData = $spots->map(function ($spot) {
                return [
                    'id' => $spot->id,
                    'name' => $spot->name,
                    'description' => $spot->description,
                    'prefecture' => $spot->prefecture,
                    'city' => $spot->city,
                    'category' => $spot->category,
                    'tags' => $spot->tags,
                    'overview' => $spot->overview,
                    'highlights' => $spot->highlights,
                ];
            });

            // AI prompt to analyze popularity
            $prompt = $this->buildPopularityAnalysisPrompt($spotsData->toArray());

            // Make API call to OpenRouter
            $openrouterApiKey = env('OPENROUTER_API_KEY');
            $openrouterApiUrl = env('OPENROUTER_API_URL', 'https://openrouter.ai/api/v1/chat/completions');
            $model = env('OPENROUTER_MODEL', 'google/gemini-2.5-flash-lite-preview-06-17');

            if (!$openrouterApiKey) {
                throw new \Exception('OpenRouter API key not configured');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $openrouterApiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => 'https://travel-voice.app',
                'X-Title' => 'Travel Voice App'
            ])->post($openrouterApiUrl, [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'あなたは日本の観光地分析の専門家です。提供された観光スポットを一般的な観光客の好み、アクセシビリティ、文化的意義、総合的な魅力に基づいて人気度でランキングしてください。'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => 2000,
                'temperature' => 0.3,
                'top_p' => 1,
                'frequency_penalty' => 0,
                'presence_penalty' => 0
            ]);

            if ($response->successful()) {
                $aiResponse = $response->json();
                
                if (!isset($aiResponse['choices']) || !isset($aiResponse['choices'][0]) || !isset($aiResponse['choices'][0]['message'])) {
                    Log::error('Invalid OpenRouter response format', ['response' => $aiResponse]);
                    throw new \Exception('Invalid AI response format');
                }
                
                $analysis = $aiResponse['choices'][0]['message']['content'];
                
                // Parse AI response and extract popular spot IDs
                $popularSpotIds = $this->parseAIResponse($analysis);
                
                // Get the popular spots in order
                $popularSpots = collect($popularSpotIds)->map(function ($id) use ($spots) {
                    return $spots->firstWhere('id', $id);
                })->filter()->values();

                return $popularSpots->take(5);
            }

            $errorResponse = $response->body();
            Log::error('OpenRouter API call failed', [
                'status' => $response->status(),
                'response' => $errorResponse
            ]);
            throw new \Exception('OpenRouter API call failed: ' . $response->status() . ' - ' . $errorResponse);

        } catch (\Exception $e) {
            Log::error('AI analysis error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Build prompt for AI analysis
     */
    private function buildPopularityAnalysisPrompt(array $spots): string
    {
        $spotsJson = json_encode($spots, JSON_UNESCAPED_UNICODE);
        
        return "以下の日本の観光スポットを分析し、人気度の高い上位5つをランキングしてください。

評価基準：
1. 文化的・歴史的意義
2. 観光客にとってのアクセシビリティと利便性
3. ユニークな体験や魅力
4. 国内外観光客への総合的な訴求力
5. 季節を通じた魅力度

観光スポットデータ：
{$spotsJson}

回答は人気度順の観光スポットIDのJSON配列のみで返してください：
[123, 456, 789, 101, 202]

説明や追加のテキストは含めず、JSON配列のみを返してください。";
    }

    /**
     * Parse AI response to extract spot IDs
     */
    private function parseAIResponse(string $response): array
    {
        try {
            // Remove any markdown formatting
            $response = preg_replace('/```json\s*|\s*```/', '', $response);
            $response = trim($response);
            
            // Decode JSON
            $spotIds = json_decode($response, true);
            
            if (is_array($spotIds)) {
                return array_map('intval', $spotIds);
            }
            
            throw new \Exception('Invalid AI response format');
            
        } catch (\Exception $e) {
            Log::error('Error parsing AI response: ' . $e->getMessage());
            throw $e;
        }
    }

}