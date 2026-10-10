<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductMovement extends Model
{
    public $timestamps = true;
    const UPDATED_AT = null; // movements are immutable

    protected $fillable = [
        'product_id', 'user_id', 'branch_id',
        'reference_type', 'reference_id',
        'type', 'qty_before', 'qty_change', 'qty_after',
        'unit_cost', 'notes',
    ];

    protected function casts(): array
    {
        return ['unit_cost' => 'decimal:2'];
    }

    public function product()   { return $this->belongsTo(Product::class); }
    public function user()      { return $this->belongsTo(User::class); }
    public function reference() { return $this->morphTo(); }

    public static function types(): array
    {
        return ['purchase', 'sale', 'adjustment', 'return', 'correction', 'transfer'];
    }
}
