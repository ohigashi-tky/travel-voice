<?php

namespace App\Http\Controllers;

use App\Models\TravelSpot;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TravelSpotController extends Controller
{
    /**
     * Display a listing of the travel spots.
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
     * Display the specified travel spot.
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

}