<?php

namespace App\Http\Controllers;

use App\Models\TravelSpot;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TravelSpotController extends Controller
{
    /**
     * 観光地一覧の取得
     * 
     * すべての観光地情報を取得します。各観光地には関連する画像情報も含まれます。
     * 
     * @group 観光地 / Travel Spots
     * 
     * @response 200 scenario="成功時" {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "東京タワー",
     *       "description": "東京のシンボルとして知られる電波塔",
     *       "latitude": 35.6586,
     *       "longitude": 139.7454,
     *       "address": "東京都港区芝公園4丁目2-8",
     *       "place_id": "ChIJCewJkL2LGGAR3Qmk0vCTGkg",
     *       "category": "観光地",
     *       "spot_images": []
     *     }
     *   ]
     * }
     */
    public function index(): JsonResponse
    {
        $spots = TravelSpot::with('spotImages')->orderBy('id')->get();
        
        return response()->json([
            'success' => true,
            'data' => $spots
        ]);
    }

    /**
     * 観光地詳細の取得
     * 
     * 指定したIDの観光地の詳細情報を取得します。
     * 
     * @group 観光地 / Travel Spots
     * 
     * @urlParam id integer required 観光地のID Example: 1
     * 
     * @response 200 scenario="成功時" {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "東京タワー",
     *     "description": "東京のシンボルとして知られる電波塔",
     *     "latitude": 35.6586,
     *     "longitude": 139.7454,
     *     "address": "東京都港区芝公園4丁目2-8",
     *     "place_id": "ChIJCewJkL2LGGAR3Qmk0vCTGkg",
     *     "category": "観光地",
     *     "spot_images": []
     *   }
     * }
     * 
     * @response 404 scenario="観光地が見つからない場合" {
     *   "success": false,
     *   "message": "Travel spot not found"
     * }
     */
    public function show(int $id): JsonResponse
    {
        $spot = TravelSpot::with('spotImages')->find($id);
        
        if (!$spot) {
            return response()->json([
                'success' => false,
                'message' => 'Travel spot not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $spot
        ]);
    }

    /**
     * Get travel spots by prefecture ID.
     */
    public function getByPrefecture(int $prefectureId): JsonResponse
    {
        $spots = TravelSpot::with('spotImages')
            ->where('prefecture_id', $prefectureId)
            ->orderBy('rating', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $spots
        ]);
    }

}