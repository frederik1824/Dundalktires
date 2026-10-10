<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPromotion extends Model
{
    protected $fillable = [
        'title', 'description', 'badge_text', 'cta_text',
        'is_active', 'starts_at', 'ends_at', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'date',
        'ends_at'   => 'date',
    ];

    public function scopeActive($query)
    {
        $today = now()->toDateString();
        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $today))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $today));
    }
}
