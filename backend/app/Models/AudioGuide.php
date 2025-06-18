<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AudioGuide extends Model
{
    use HasFactory;

    protected $fillable = [
        'guide_id',
        'audio_file_path',
        'audio_file_url',
        'duration_seconds',
        'format',
        'file_size',
        'voice_actor',
        'language',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function guide(): BelongsTo
    {
        return $this->belongsTo(Guide::class);
    }
}
