<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TravelSpotImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_spot_id',
        'image_url',
        'thumbnail_url',
        'width',
        'height',
        'order',
        'attribution',
        'source',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function travelSpot(): BelongsTo
    {
        return $this->belongsTo(TravelSpot::class);
    }
}