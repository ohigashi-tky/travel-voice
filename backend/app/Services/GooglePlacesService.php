<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GooglePlacesService
{
    private string $apiKey;
    private string $baseUrl = 'https://places.googleapis.com/v1/places:searchText';

    public function __construct()
    {
        $apiKey = config('services.google.places_api_key');
        if (!$apiKey) {
            throw new \Exception('Google Maps API key is not configured. Please set GOOGLE_MAPS_API_KEY in your .env file.');
        }
        $this->apiKey = $apiKey;
    }

    /**
     * 場所の名前と都道府県からplace_idを取得
     */
    public function getPlaceId(string $name, string $prefecture): ?string
    {
        try {
            $query = "{$name}, {$prefecture}, Japan";
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Goog-Api-Key' => $this->apiKey,
                'X-Goog-FieldMask' => 'places.id,places.displayName,places.formattedAddress'
            ])->post($this->baseUrl, [
                'textQuery' => $query
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['places']) && count($data['places']) > 0) {
                    $place = $data['places'][0];
                    Log::info("Found place_id for {$name}: {$place['id']}");
                    return $place['id'];
                }
            }

            Log::warning("Could not find place_id for: {$query}");
            return null;
        } catch (\Exception $e) {
            Log::error("Error fetching place_id for {$name}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * 複数の場所のplace_idを一括取得
     */
    public function getMultiplePlaceIds(array $spots): array
    {
        $results = [];
        
        foreach ($spots as $spot) {
            $placeId = $this->getPlaceId($spot['name'], $spot['prefecture']);
            $results[] = [
                'id' => $spot['id'],
                'name' => $spot['name'],
                'place_id' => $placeId
            ];
            
            // API rate limit対策で少し待機
            sleep(1);
        }
        
        return $results;
    }
}