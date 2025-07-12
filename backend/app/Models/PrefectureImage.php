<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrefectureImage extends Model
{
    protected $fillable = [
        'prefecture_id',
        'image_url',
        'image_type',
        'display_order',
    ];

    /**
     * 都道府県との関係
     */
    public function prefecture(): BelongsTo
    {
        return $this->belongsTo(Prefecture::class);
    }

    /**
     * 表示順でソート
     */
    public function scopeOrderByDisplay($query)
    {
        return $query->orderBy('display_order');
    }

    /**
     * 画像タイプでフィルタ
     */
    public function scopeByType($query, $type)
    {
        return $query->where('image_type', $type);
    }
}
