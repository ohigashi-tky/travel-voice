<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TouristSpotController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\PopularSpotsController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});

Route::get('/tourist-spots', [TouristSpotController::class, 'index']);
Route::get('/tourist-spots/{touristSpot}', [TouristSpotController::class, 'show']);
Route::get('/tourist-spots/prefecture/{prefecture}', [TouristSpotController::class, 'byPrefecture']);

Route::get('/popular-spots', [PopularSpotsController::class, 'getPopularSpots']);

Route::get('/guides/{guide}', [GuideController::class, 'show']);