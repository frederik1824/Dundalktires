<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id', 'product_id',
        'qty_ordered', 'qty_received', 'unit_cost', 'notes',
    ];

    protected function casts(): array
    {
        return ['unit_cost' => 'decimal:2'];
    }

    public function order()   { return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id'); }
    public function product() { return $this->belongsTo(Product::class); }

    public function getLineTotalAttribute(): float
    {
        return $this->qty_ordered * (float) $this->unit_cost;
    }

    public function getRemainingAttribute(): int
    {
        return max(0, $this->qty_ordered - $this->qty_received);
    }
}
