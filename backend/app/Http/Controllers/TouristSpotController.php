<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\TouristSpot;

class TouristSpotController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = TouristSpot::with('activeGuides')
            ->where('is_active', true);

        if ($request->has('prefecture')) {
            $query->where('prefecture', $request->prefecture);
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $touristSpots = $query->get();

        return response()->json($touristSpots);
    }

    public function show(TouristSpot $touristSpot): JsonResponse
    {
        $touristSpot->load(['activeGuides.activeAudioGuides']);

        return response()->json($touristSpot);
    }

    public function byPrefecture(string $prefecture): JsonResponse
    {
        $touristSpots = TouristSpot::with('activeGuides')
            ->where('prefecture', $prefecture)
            ->where('is_active', true)
            ->get();

        return response()->json($touristSpots);
    }
}
