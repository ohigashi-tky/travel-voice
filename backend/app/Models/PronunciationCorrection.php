<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class PronunciationCorrection extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_text',
        'pronunciation',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * 有効な読み方修正データのみを取得
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * 優先度順（高い順）、文字数順（長い順）でソート
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('priority', 'desc')
                     ->orderByRaw('CHAR_LENGTH(original_text) desc');
    }

    /**
     * SSML形式の置換文字列を生成
     */
    public function getSSMLSubstitution(): string
    {
        return sprintf('<sub alias="%s">%s</sub>', $this->pronunciation, $this->original_text);
    }
}
