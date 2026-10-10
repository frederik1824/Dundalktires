<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class WebLead extends Model
{
    protected $fillable = [
        'name', 'phone', 'service_type', 'vehicle', 'tire_size', 'notes',
        'status', 'contacted_at', 'staff_notes',
    ];

    protected $casts = [
        'contacted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('global_new_leads_count'));
        static::deleted(fn () => Cache::forget('global_new_leads_count'));
    }
}
