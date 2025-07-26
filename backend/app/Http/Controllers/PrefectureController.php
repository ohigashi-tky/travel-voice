<?php

namespace App\Http\Controllers;

use App\Models\Prefecture;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PrefectureController extends Controller
{
    /**
     * 全都道府県一覧を取得
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Prefecture::with('images')
                ->orderByDisplay();

            // 地域でフィルタ
            if ($request->has('region')) {
                $query->byRegion($request->region);
            }

            // 利用可能なもののみ
            if ($request->boolean('available_only')) {
                $query->available();
            }

            $prefectures = $query->get();

            return response()->json([
                'success' => true,
                'data' => $prefectures
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch prefectures',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 利用可能な都道府県のみ取得
     */
    public function available(): JsonResponse
    {
        try {
            $prefectures = Prefecture::with('images')
                ->available()
                ->orderByDisplay()
                ->get();

            return response()->json([
                'success' => true,
                'data' => $prefectures
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch available prefectures',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 主要都道府県のみ取得
     */
    public function featured(): JsonResponse
    {
        try {
            // キャッシュキー
            $cacheKey = 'prefectures.featured';
            
            // キャッシュから取得、なければDBから取得して24時間キャッシュ
            $prefectures = Cache::remember($cacheKey, 60 * 60 * 24, function () {
                return Prefecture::featured()
                    ->select('id', 'name', 'is_available', 'featured_order')
                    ->get();
            });

            return response()->json([
                'success' => true,
                'data' => $prefectures
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch featured prefectures',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 地域別都道府県取得
     */
    public function byRegion(): JsonResponse
    {
        try {
            // キャッシュキー
            $cacheKey = 'prefectures.by_region';
            
            // キャッシュから取得、なければDBから取得して24時間キャッシュ
            $prefectures = Cache::remember($cacheKey, 60 * 60 * 24, function () {
                return Prefecture::orderByRegion()
                    ->select('id', 'name', 'region', 'is_available', 'region_order', 'order_in_region')
                    ->get()
                    ->groupBy('region');
            });

            return response()->json([
                'success' => true,
                'data' => $prefectures
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch prefectures by region',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * トップページ用データ一括取得（新規追加）
     * 主要都道府県と地域別都道府県を一度に取得
     */
    public function topPageData(): JsonResponse
    {
        try {
            // キャッシュキー
            $cacheKey = 'prefectures.top_page';
            
            // キャッシュから取得、なければDBから取得して24時間キャッシュ
            $data = Cache::remember($cacheKey, 60 * 60 * 24, function () {
                // 必要最小限のカラムのみ選択
                $columns = ['id', 'name', 'region', 'is_available', 'featured_order', 'region_order', 'order_in_region'];
                
                // 主要都道府県
                $featured = Prefecture::featured()
                    ->select($columns)
                    ->get();
                
                // 地域別都道府県
                $byRegion = Prefecture::orderByRegion()
                    ->select($columns)
                    ->get()
                    ->groupBy('region');
                
                return [
                    'featured' => $featured,
                    'by_region' => $byRegion
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch prefecture data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 特定の都道府県詳細を取得
     */
    public function show($id): JsonResponse
    {
        try {
            $prefecture = Prefecture::with(['images', 'travelSpots.spotImages'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $prefecture
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Prefecture not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * 都道府県の観光地一覧を取得
     */
    public function spots($id): JsonResponse
    {
        try {
            $prefecture = Prefecture::findOrFail($id);
            $spots = $prefecture->travelSpots()
                ->with('spotImages')
                ->orderBy('id')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'prefecture' => $prefecture,
                    'spots' => $spots
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch prefecture spots',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * 都道府県名で観光地検索
     */
    public function spotsByName($name): JsonResponse
    {
        try {
            $prefecture = Prefecture::where('name', $name)->firstOrFail();
            $spots = $prefecture->travelSpots()
                ->with('spotImages')
                ->orderBy('id')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'prefecture' => $prefecture,
                    'spots' => $spots
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Prefecture not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * キャッシュクリア（管理者用）
     */
    public function clearCache(): JsonResponse
    {
        try {
            Cache::forget('prefectures.featured');
            Cache::forget('prefectures.by_region');
            Cache::forget('prefectures.top_page');

            return response()->json([
                'success' => true,
                'message' => 'Prefecture cache cleared successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}