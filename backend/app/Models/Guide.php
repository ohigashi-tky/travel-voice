<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guide extends Model
{
    use HasFactory;

    protected $fillable = [
        'tourist_spot_id',
        'title',
        'content',
        'type',
        'duration_minutes',
        'order',
        'highlights',
        'is_active'
    ];

    protected $casts = [
        'highlights' => 'array',
        'is_active' => 'boolean'
    ];

    public function touristSpot(): BelongsTo
    {
        return $this->belongsTo(TouristSpot::class);
    }

    public function audioGuides(): HasMany
    {
        return $this->hasMany(AudioGuide::class);
    }

    public function activeAudioGuides(): HasMany
    {
        return $this->hasMany(AudioGuide::class)->where('is_active', true);
    }
}
