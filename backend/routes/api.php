<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TravelSpotController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\PopularSpotsController;
use App\Http\Controllers\EventController;
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