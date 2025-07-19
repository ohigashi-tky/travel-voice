<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prefecture extends Model
{
    protected $fillable = [
        'name',
        'name_kana',
        'region',
        'display_order',
        'is_available',
        'order_in_region',
        'featured_order',
        'region_order',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    /**
     * 都道府県に紐づく画像
     */
    public function images(): HasMany
    {
        return $this->hasMany(PrefectureImage::class);
    }

    /**
     * 都道府県に紐づく観光地
     */
    public function travelSpots(): HasMany
    {
        return $this->hasMany(TravelSpot::class);
    }

    /**
     * アイコン画像を取得
     */
    public function getIconImageAttribute()
    {
        return $this->images()->where('image_type', 'icon')->first();
    }

    /**
     * バナー画像を取得
     */
    public function getBannerImageAttribute()
    {
        return $this->images()->where('image_type', 'banner')->first();
    }

    /**
     * 表示順でソート
     */
    public function scopeOrderByDisplay($query)
    {
        return $query->orderBy('display_order');
    }

    /**
     * 利用可能な都道府県のみ
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * 地域別にグループ化
     */
    public function scopeByRegion($query, $region)
    {
        return $query->where('region', $region);
    }

    /**
     * 地域内の順番でソート
     */
    public function scopeOrderByRegionOrder($query)
    {
        return $query->orderBy('order_in_region');
    }

    /**
     * 主要都道府県のみ
     */
    public function scopeFeatured($query)
    {
        return $query->whereNotNull('featured_order')->orderBy('featured_order');
    }

    /**
     * 地域順でソート
     */
    public function scopeOrderByRegion($query)
    {
        return $query->orderBy('region_order')->orderBy('order_in_region');
    }
}
