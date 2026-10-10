<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'branch_id', 'supplier_id', 'category_id', 'type',
        'sku', 'barcode', 'name', 'description', 'unit',
        'tire_brand', 'tire_line', 'tire_width', 'tire_ratio', 'tire_diameter',
        'tire_type', 'tire_size_normalized',
        'cost_price', 'sale_price',
        'stock_qty', 'stock_min', 'location',
        'notes', 'is_active', 'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function movements()
    {
        return $this->hasMany(ProductMovement::class);
    }

    public function serviceConsumables()
    {
        return $this->hasMany(ServiceConsumable::class);
    }

    public function scopeConsumable($query)
    {
        return $query->where('type', 'consumable');
    }

    // Human-readable tire size: "215/55R17"
    public function getTireSizeAttribute(): ?string
    {
        if ($this->type !== 'tire' || ! $this->tire_width) {
            return null;
        }

        return "{$this->tire_width}/{$this->tire_ratio}R{$this->tire_diameter}";
    }

    // Is stock at or below minimum?
    public function getLowStockAttribute(): bool
    {
        return $this->stock_qty <= $this->stock_min;
    }

    // Normalize any tire size input to "21555R17" for indexed searching
    public static function normalizeTireSize(string $input): string
    {
        // Strip everything except digits and R/r
        $clean = preg_replace('/[^0-9Rr]/', '', $input);

        // Uppercase R
        return strtoupper($clean);
    }

    // Parse "215/55R17" or "2155517" or "215 55 17" → [width, ratio, diameter]
    public static function parseTireSize(string $input): ?array
    {
        // Try standard format: 215/55R17
        if (preg_match('/^(\d{3})[\/\s]?(\d{2})[Rr\s]?(\d{2})$/', trim($input), $m)) {
            return ['width' => $m[1], 'ratio' => $m[2], 'diameter' => $m[3]];
        }
        // Try compact: 2155517
        if (preg_match('/^(\d{3})(\d{2})R?(\d{2})$/i', trim($input), $m)) {
            return ['width' => $m[1], 'ratio' => $m[2], 'diameter' => $m[3]];
        }

        return null;
    }

    public function scopeTire($query)
    {
        return $query->where('type', 'tire');
    }

    public function scopeMaterial($query)
    {
        return $query->whereIn('type', ['material', 'consumable', 'part']);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_qty', '<=', 'stock_min');
    }

    public function scopeSearch($query, ?string $term)
    {
        if (! $term) {
            return $query;
        }
        $normalized = static::normalizeTireSize($term);

        return $query->where(function ($q) use ($term, $normalized) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('tire_brand', 'like', "%{$term}%")
                ->orWhere('tire_line', 'like', "%{$term}%")
                ->orWhere('sku', 'like', "%{$term}%")
                ->orWhere('tire_size_normalized', 'like', "%{$normalized}%");
        });
    }
}
