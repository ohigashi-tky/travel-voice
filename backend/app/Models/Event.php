<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'overview',
        'prefecture',
        'location',
        'start_date',
        'end_date',
        'category',
        'tags',
        'access',
        'url',
        'organizer',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'tags' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Scope to get only active events
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get events by prefecture
     */
    public function scopeByPrefecture(Builder $query, string $prefecture): Builder
    {
        return $query->where('prefecture', $prefecture);
    }

    /**
     * Scope to get current and future events
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('end_date', '>=', Carbon::today());
    }

    /**
     * Scope to get events in date range
     */
    public function scopeInDateRange(Builder $query, Carbon $startDate, Carbon $endDate): Builder
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function ($q2) use ($startDate, $endDate) {
                  $q2->where('start_date', '<=', $startDate)
                     ->where('end_date', '>=', $endDate);
              });
        });
    }

    /**
     * Scope to search events by keyword
     */
    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('title', 'like', '%' . $keyword . '%')
              ->orWhere('description', 'like', '%' . $keyword . '%')
              ->orWhere('overview', 'like', '%' . $keyword . '%')
              ->orWhere('location', 'like', '%' . $keyword . '%');
        });
    }

    /**
     * Scope to filter by category
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to order by display order
     */
    public function scopeOrderByDisplay(Builder $query): Builder
    {
        return $query->orderBy('display_order', 'asc')
                     ->orderBy('start_date', 'asc');
    }

    /**
     * Check if event is currently active (within date range)
     */
    public function isCurrentlyActive(): bool
    {
        $today = Carbon::today();
        return $this->start_date <= $today && $this->end_date >= $today;
    }

    /**
     * Check if event is upcoming
     */
    public function isUpcoming(): bool
    {
        return $this->start_date > Carbon::today();
    }

    /**
     * Check if event has ended
     */
    public function hasEnded(): bool
    {
        return $this->end_date < Carbon::today();
    }

    /**
     * Get formatted duration
     */
    public function getDurationAttribute(): string
    {
        if ($this->start_date->equalTo($this->end_date)) {
            return $this->start_date->format('Y年m月d日');
        }
        
        return $this->start_date->format('Y年m月d日') . ' - ' . $this->end_date->format('Y年m月d日');
    }
}