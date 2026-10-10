<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'author_name', 'rating', 'body', 'reviewed_at', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'reviewed_at' => 'date',
        'rating'      => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->author_name));
        $initials = strtoupper($words[0][0] ?? '?');
        if (isset($words[1])) {
            $initials .= strtoupper($words[1][0]);
        }
        return $initials;
    }
}
