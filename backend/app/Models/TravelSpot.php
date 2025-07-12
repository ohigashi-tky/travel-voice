<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelSpot extends Model
{
    use HasFactory;

    protected $table = 'travel_spots';

    protected $fillable = [
        'name',
        'description',
        'category',
        'prefecture',
        'prefecture_id',
        'place_id',
        'imageUrl',
        'overview',
        'highlights',
        'history',
        'images',
    ];

    protected $casts = [
        'highlights' => 'array',
        'images' => 'array',
    ];

    public function spotImages()
    {
        return $this->hasMany(TravelSpotImage::class)->where('is_active', true)->orderBy('order');
    }

    /**
     * 都道府県との関係
     */
    public function prefecture()
    {
        return $this->belongsTo(Prefecture::class);
    }

    /**
     * 音声ガイドとの関係
     */
    public function guides()
    {
        return $this->hasMany(Guide::class, 'tourist_spot_id');
    }
}