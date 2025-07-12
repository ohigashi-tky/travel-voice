<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TravelSpot;
use App\Models\TravelSpotImage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchTravelSpotImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'travel-spots:fetch-images {--force : 既存の写真も再取得}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Google Places APIから観光地の写真を取得してテーブルに保存';

    private string $apiKey;

    public function __construct()
    {
        parent::__construct();
        $apiKey = config('services.google.places_api_key');
        if (!$apiKey) {
            throw new \Exception('Google Maps API key is not configured. Please set GOOGLE_MAPS_API_KEY in your .env file.');
        }
        $this->apiKey = $apiKey;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('観光地の写真取得を開始します...');
        
        // 対象のスポットを取得（place_idがnullでも対象に含める）
        $query = TravelSpot::query();
        
        if (!$this->option('force')) {
            $query->whereDoesntHave('spotImages');
        }
        
        $spots = $query->get();
        
        if ($spots->isEmpty()) {
            $this->info('対象のスポットがありません。');
            return;
        }
        
        $this->info("対象スポット数: {$spots->count()}件");
        
        $progressBar = $this->output->createProgressBar($spots->count());
        $progressBar->start();
        
        $totalImages = 0;
        
        foreach ($spots as $spot) {
            if ($this->option('force')) {
                $spot->spotImages()->delete();
            }
            
            $images = $this->fetchImagesForSpot($spot);
            
            if ($images) {
                foreach ($images as $index => $imageData) {
                    TravelSpotImage::create([
                        'travel_spot_id' => $spot->id,
                        'image_url' => $imageData['url'],
                        'thumbnail_url' => $imageData['thumbnail_url'] ?? null,
                        'width' => $imageData['width'] ?? null,
                        'height' => $imageData['height'] ?? null,
                        'order' => $index,
                        'attribution' => $imageData['attribution'] ?? null,
                        'source' => 'google_places',
                    ]);
                    $totalImages++;
                }
                $this->line("\n✓ {$spot->name}: " . count($images) . "枚の写真を保存");
            } else {
                $this->line("\n✗ {$spot->name}: 写真が見つかりません");
            }
            
            $progressBar->advance();
            
            // API rate limit対策
            sleep(1);
        }
        
        $progressBar->finish();
        
        $this->newLine(2);
        $this->info("完了: {$totalImages}枚の写真を保存しました。");
    }

    private function fetchImagesForSpot(TravelSpot $spot): ?array
    {
        try {
            $placeId = null;
            
            // place_idが設定されている場合はそれを使用、ない場合は検索
            if (!empty($spot->place_id)) {
                $placeId = $spot->place_id;
            } else {
                // 1. 場所の検索
                $searchResponse = Http::get('https://maps.googleapis.com/maps/api/place/findplacefromtext/json', [
                    'input' => $spot->name . ', ' . $spot->prefecture . ', Japan',
                    'inputtype' => 'textquery',
                    'fields' => 'place_id,name,photos',
                    'key' => $this->apiKey,
                ]);

                if (!$searchResponse->successful()) {
                    Log::error("Search failed for {$spot->name}: " . $searchResponse->body());
                    return null;
                }

                $searchData = $searchResponse->json();
                if (empty($searchData['candidates'])) {
                    Log::warning("No candidates found for {$spot->name}");
                    return null;
                }

                $candidate = $searchData['candidates'][0];
                $placeId = $candidate['place_id'];
                
                // place_idが見つかった場合はスポットのレコードを更新
                $spot->update(['place_id' => $placeId]);
            }
            
            // 2. 詳細情報の取得（写真情報含む）
            $detailsResponse = Http::get('https://maps.googleapis.com/maps/api/place/details/json', [
                'place_id' => $placeId,
                'fields' => 'photos',
                'key' => $this->apiKey,
            ]);

            if (!$detailsResponse->successful()) {
                Log::error("Details failed for {$spot->name}: " . $detailsResponse->body());
                return null;
            }

            $detailsData = $detailsResponse->json();
            if (empty($detailsData['result']['photos'])) {
                Log::warning("No photos found for {$spot->name}");
                return null;
            }

            // 3. 写真URLの生成
            $images = [];
            $photos = array_slice($detailsData['result']['photos'], 0, 5); // 最大5枚

            foreach ($photos as $photo) {
                $imageUrl = "https://maps.googleapis.com/maps/api/place/photo?" . http_build_query([
                    'maxwidth' => 800,
                    'photo_reference' => $photo['photo_reference'],
                    'key' => $this->apiKey,
                ]);

                $thumbnailUrl = "https://maps.googleapis.com/maps/api/place/photo?" . http_build_query([
                    'maxwidth' => 400,
                    'photo_reference' => $photo['photo_reference'],
                    'key' => $this->apiKey,
                ]);

                $images[] = [
                    'url' => $imageUrl,
                    'thumbnail_url' => $thumbnailUrl,
                    'width' => $photo['width'],
                    'height' => $photo['height'],
                    'attribution' => isset($photo['html_attributions']) ? implode(', ', $photo['html_attributions']) : null,
                ];
            }

            return $images;
        } catch (\Exception $e) {
            Log::error("Error fetching images for {$spot->name}: " . $e->getMessage());
            return null;
        }
    }
}
