<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TouristSpot extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'prefecture',
        'city',
        'address',
        'latitude',
        'longitude',
        'category',
        'images',
        'access_info',
        'website',
        'phone',
        'opening_hours',
        'admission_fee',
        'is_active'
    ];

    protected $casts = [
        'images' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean'
    ];

    public function guides(): HasMany
    {
        return $this->hasMany(Guide::class);
    }

    public function activeGuides(): HasMany
    {
        return $this->hasMany(Guide::class)->where('is_active', true)->orderBy('order');
    }
}
