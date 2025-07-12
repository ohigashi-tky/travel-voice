<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * イベント一覧を取得
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Event::active();

            // 都道府県フィルター
            if ($request->filled('prefecture')) {
                $query->byPrefecture($request->prefecture);
            }

            // カテゴリフィルター
            if ($request->filled('category')) {
                $query->byCategory($request->category);
            }

            // 日付フィルター
            if ($request->filled('date')) {
                $date = Carbon::parse($request->date);
                $query->inDateRange($date, $date);
            }

            // 日付範囲フィルター
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $startDate = Carbon::parse($request->start_date);
                $endDate = Carbon::parse($request->end_date);
                $query->inDateRange($startDate, $endDate);
            }

            // キーワード検索
            if ($request->filled('keyword') || $request->filled('search')) {
                $keyword = $request->keyword ?? $request->search;
                $query->search($keyword);
            }

            // 今後のイベントのみ（デフォルト）
            if (!$request->filled('include_past')) {
                $query->upcoming();
            }

            // ソート
            $sortBy = $request->get('sort', 'display_order');
            $sortOrder = $request->get('order', 'asc');
            
            if ($sortBy === 'display_order') {
                $query->orderByDisplay();
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }

            // ページネーション
            $perPage = min($request->get('per_page', 100), 5000); // 最大5000件（全件取得対応）
            $events = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $events->items(),
                'message' => 'イベント情報を取得しました。'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'イベント情報の取得に失敗しました。',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * イベント詳細を取得
     */
    public function show(Event $event): JsonResponse
    {
        try {
            if (!$event->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'このイベントは現在利用できません。'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $event,
                'message' => 'イベント詳細を取得しました。'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'イベント詳細の取得に失敗しました。',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 都道府県別イベント数を取得
     */
    public function countByPrefecture(): JsonResponse
    {
        try {
            $counts = Event::active()
                ->upcoming()
                ->selectRaw('prefecture, COUNT(*) as count')
                ->groupBy('prefecture')
                ->orderBy('prefecture')
                ->pluck('count', 'prefecture');

            return response()->json([
                'success' => true,
                'data' => $counts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'データの取得に失敗しました。',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 人気のタグを取得
     */
    public function popularTags(Request $request): JsonResponse
    {
        try {
            $limit = min($request->get('limit', 20), 50);
            
            $events = Event::active()
                ->upcoming()
                ->whereNotNull('tags')
                ->get();
                
            $tagCounts = [];
            foreach ($events as $event) {
                if (is_array($event->tags)) {
                    foreach ($event->tags as $tag) {
                        $tag = trim($tag);
                        if (!empty($tag)) {
                            $tagCounts[$tag] = ($tagCounts[$tag] ?? 0) + 1;
                        }
                    }
                }
            }
            
            arsort($tagCounts);
            $popularTags = array_slice($tagCounts, 0, $limit, true);

            return response()->json([
                'success' => true,
                'data' => $popularTags
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'データの取得に失敗しました。',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * おすすめイベントを取得
     */
    public function featured(Request $request): JsonResponse
    {
        try {
            $limit = min($request->get('limit', 10), 50);
            
            $events = Event::active()
                ->upcoming()
                ->orderByDisplay()
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $events
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'データの取得に失敗しました。',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 現在開催中のイベントを取得
     */
    public function current(Request $request): JsonResponse
    {
        try {
            $limit = min($request->get('limit', 10), 50);
            $today = Carbon::today();
            
            $events = Event::active()
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->orderByDisplay()
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $events
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'データの取得に失敗しました。',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}