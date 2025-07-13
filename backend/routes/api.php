<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TravelSpotController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\PopularSpotsController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PrefectureController;
use App\Http\Controllers\Api\AudioGuideController;
use App\Http\Controllers\Api\TestPollyController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});


Route::get('/travel-spots', [TravelSpotController::class, 'index']);
Route::get('/travel-spots/{id}', [TravelSpotController::class, 'show']);

Route::get('/popular-spots', [PopularSpotsController::class, 'getPopularSpots']);
Route::delete('/popular-spots/cache', [PopularSpotsController::class, 'clearCache']);

// Prefecture API Routes
Route::get('/prefectures', [PrefectureController::class, 'index']);
Route::get('/prefectures/available', [PrefectureController::class, 'available']);
Route::get('/prefectures/by-region', [PrefectureController::class, 'byRegion']);
Route::get('/prefectures/{id}', [PrefectureController::class, 'show']);
Route::get('/prefectures/{id}/spots', [PrefectureController::class, 'spots']);
Route::get('/prefectures/name/{name}/spots', [PrefectureController::class, 'spotsByName']);

Route::get('/guides/{guide}', [GuideController::class, 'show']);

// Events API Routes
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{id}', [EventController::class, 'show']);
Route::get('/events/prefecture/{prefecture}', [EventController::class, 'byPrefecture']);
Route::get('/events/meta/prefectures', [EventController::class, 'prefectures']);
Route::get('/events/meta/tags', [EventController::class, 'tags']);
Route::post('/events/refresh-cache', [EventController::class, 'refreshCache']);
Route::delete('/events/cache', [EventController::class, 'clearCache']);

// Test Routes
Route::get('/test-polly', [TestPollyController::class, 'test']);

// Health check and debug routes
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'database' => 'connected'
    ]);
});

// Simple test endpoint
Route::get('/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'API is working',
        'cors' => 'enabled',
        'timestamp' => now()
    ]);
});

Route::get('/debug/prefectures', function () {
    try {
        $count = \App\Models\Prefecture::count();
        return response()->json([
            'status' => 'ok',
            'prefecture_count' => $count,
            'sample' => \App\Models\Prefecture::take(3)->get()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});

Route::get('/debug/events', function () {
    try {
        $count = \App\Models\Event::count();
        return response()->json([
            'status' => 'ok',
            'event_count' => $count,
            'sample' => \App\Models\Event::take(3)->get()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});
Route::get('/test-events', function () {
    $service = new \App\Services\EventGeneratorService();
    $events = $service->getEvents();
    return response()->json([
        'success' => true,
        'count' => count($events),
        'data' => $events
    ]);
});

// Audio Guide API Routes
Route::prefix('audio-guide')->group(function () {
    Route::post('/synthesize', [AudioGuideController::class, 'synthesize']);
    Route::get('/voices', [AudioGuideController::class, 'voices']);
    Route::post('/tourist-spot', [AudioGuideController::class, 'generateTouristSpotAudio']);
    
    // Admin routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::delete('/cache', [AudioGuideController::class, 'clearCache']);
    });
});