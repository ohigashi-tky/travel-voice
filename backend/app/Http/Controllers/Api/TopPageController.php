<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prefecture;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TopPageController extends Controller
{
    /**
     * トップページ用の最適化されたデータを一括取得
     */
    public function index(): JsonResponse
    {
        try {
            // 24時間キャッシュで高速化
            $data = Cache::remember('top_page_data', 60 * 60 * 24, function () {
                return [
                    'featured_prefectures' => $this->getFeaturedPrefectures(),
                    'regional_prefectures' => $this->getRegionalPrefectures(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            \Log::error('Top page data fetch error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'データの取得に失敗しました'
            ], 500);
        }
    }

    /**
     * 主要都道府県データ取得（最小限のカラムのみ）
     */
    private function getFeaturedPrefectures(): array
    {
        return Prefecture::whereNotNull('featured_order')
            ->select(['id', 'name', 'featured_order'])
            ->orderBy('featured_order')
            ->get()
            ->toArray();
    }

    /**
     * 地域別都道府県データ取得（最小限のカラムのみ）
     */
    private function getRegionalPrefectures(): array
    {
        $prefectures = Prefecture::select(['id', 'name', 'region', 'region_order', 'order_in_region'])
            ->orderBy('region_order')
            ->orderBy('order_in_region')
            ->get();

        // 地域でグループ化
        $grouped = $prefectures->groupBy('region');
        
        $result = [];
        foreach ($grouped as $region => $items) {
            $result[] = [
                'region' => $region,
                'prefectures' => $items->toArray()
            ];
        }

        return $result;
    }
}