<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\EventGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EventController extends Controller
{
    protected $eventGeneratorService;

    public function __construct(EventGeneratorService $eventGeneratorService)
    {
        $this->eventGeneratorService = $eventGeneratorService;
    }

    /**
     * Display a listing of events
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Get events from AI generator service with caching
            $events = collect($this->eventGeneratorService->getEvents());

            // Apply filters
            if ($request->has('prefecture') && $request->prefecture) {
                $events = $events->filter(function ($event) use ($request) {
                    return $event['prefecture'] === $request->prefecture;
                });
            }

            // Date range filter
            if ($request->has('date_range') && $request->date_range) {
                $dateRange = $request->date_range;
                $now = Carbon::now();
                
                switch ($dateRange) {
                    case 'thisMonth':
                        $startDate = $now->copy()->startOfMonth();
                        $endDate = $now->copy()->endOfMonth();
                        break;
                    case 'nextMonth':
                        $startDate = $now->copy()->addMonth()->startOfMonth();
                        $endDate = $now->copy()->addMonth()->endOfMonth();
                        break;
                    case 'next3Months':
                        $startDate = $now->copy()->startOfMonth();
                        $endDate = $now->copy()->addMonths(3)->endOfMonth();
                        break;
                    default:
                        $startDate = null;
                        $endDate = null;
                }
                
                if ($startDate && $endDate) {
                    $events = $events->filter(function ($event) use ($startDate, $endDate) {
                        $eventStart = Carbon::parse($event['start_date']);
                        $eventEnd = Carbon::parse($event['end_date']);
                        
                        return ($eventStart >= $startDate && $eventStart <= $endDate) ||
                               ($eventEnd >= $startDate && $eventEnd <= $endDate) ||
                               ($eventStart <= $startDate && $eventEnd >= $endDate);
                    });
                }
            }

            // Search filter
            if ($request->has('search') && $request->search) {
                $searchTerm = strtolower($request->search);
                $events = $events->filter(function ($event) use ($searchTerm) {
                    return str_contains(strtolower($event['title']), $searchTerm) ||
                           str_contains(strtolower($event['description']), $searchTerm) ||
                           str_contains(strtolower($event['location']), $searchTerm);
                });
            }

            // Tags filter
            if ($request->has('tags') && $request->tags) {
                $tags = is_array($request->tags) ? $request->tags : explode(',', $request->tags);
                $events = $events->filter(function ($event) use ($tags) {
                    $eventTags = $event['tags'] ?? [];
                    return !empty(array_intersect($tags, $eventTags));
                });
            }

            // Sort by start date
            $events = $events->sortBy('start_date')->values();

            return response()->json([
                'success' => true,
                'data' => $events->all(),
                'message' => 'イベント情報を取得しました。',
                'cached' => false // Cache disabled
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
     * Display the specified event
     */
    public function show(int $id): JsonResponse
    {
        try {
            $events = collect($this->eventGeneratorService->getEvents());
            $event = $events->firstWhere('id', $id);

            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'イベントが見つかりません。'
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
     * Get events by prefecture
     */
    public function byPrefecture(string $prefecture): JsonResponse
    {
        try {
            $events = collect($this->eventGeneratorService->getEvents())
                        ->filter(function ($event) use ($prefecture) {
                            return $event['prefecture'] === $prefecture;
                        })
                        ->sortBy('start_date')
                        ->values();

            return response()->json([
                'success' => true,
                'data' => $events->all(),
                'message' => $prefecture . 'のイベント情報を取得しました。'
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
     * Get available prefectures with events
     */
    public function prefectures(): JsonResponse
    {
        try {
            $events = collect($this->eventGeneratorService->getEvents());
            $prefectures = $events->pluck('prefecture')->unique()->sort()->values();

            return response()->json([
                'success' => true,
                'data' => $prefectures->all(),
                'message' => 'イベント開催都道府県を取得しました。'
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
     * Get available tags
     */
    public function tags(): JsonResponse
    {
        try {
            $events = collect($this->eventGeneratorService->getEvents());
            $allTags = collect();
            
            foreach ($events as $event) {
                if (isset($event['tags']) && is_array($event['tags'])) {
                    $allTags = $allTags->merge($event['tags']);
                }
            }
            
            $uniqueTags = $allTags->unique()->sort()->values();

            return response()->json([
                'success' => true,
                'data' => $uniqueTags->all(),
                'message' => 'イベントタグを取得しました。'
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
     * Refresh events cache
     */
    public function refreshCache(): JsonResponse
    {
        try {
            $events = $this->eventGeneratorService->refreshEvents();

            return response()->json([
                'success' => true,
                'data' => $events,
                'message' => 'イベント情報のキャッシュを更新しました。'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'キャッシュの更新に失敗しました。',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear events cache
     */
    public function clearCache(): JsonResponse
    {
        try {
            $cleared = $this->eventGeneratorService->clearCache();

            return response()->json([
                'success' => $cleared,
                'message' => $cleared ? 'キャッシュを削除しました。' : 'キャッシュが見つかりませんでした。'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'キャッシュの削除に失敗しました。',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}