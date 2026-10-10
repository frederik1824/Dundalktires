<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'branch_id', 'category_id', 'name', 'description',
        'pricing_type', 'base_price', 'cost_price', 'estimated_minutes', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'base_price'  => 'decimal:2',
            'cost_price'  => 'decimal:2',
            'is_active'   => 'boolean',
        ];
    }

    public function branch()        { return $this->belongsTo(Branch::class); }
    public function category()      { return $this->belongsTo(ServiceCategory::class); }
    public function priceRules()    { return $this->hasMany(ServicePriceRule::class); }
    public function consumables()   { return $this->hasMany(ServiceConsumable::class); }

    /** Resolve price based on vehicle type */
    public function priceForVehicle(?string $vehicleType = null): float
    {
        if ($this->pricing_type === 'by_vehicle_type' && $vehicleType) {
            $rule = $this->priceRules->firstWhere('vehicle_type', $vehicleType);
            if ($rule) return (float) $rule->price;
        }
        return (float) $this->base_price;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (! $term) return $query;
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }
}
