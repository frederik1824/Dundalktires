<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'branch_id', 'supplier_id', 'created_by', 'received_by',
        'number', 'status', 'type', 'notes', 'attachment',
        'ordered_at', 'received_at',
    ];

    protected function casts(): array
    {
        return [
            'ordered_at'  => 'datetime',
            'received_at' => 'datetime',
        ];
    }

    public function supplier()   { return $this->belongsTo(Supplier::class); }
    public function createdBy()  { return $this->belongsTo(User::class, 'created_by'); }
    public function receivedBy() { return $this->belongsTo(User::class, 'received_by'); }
    public function items()      { return $this->hasMany(PurchaseOrderItem::class); }

    // Total cost across all items
    public function getTotalAttribute(): float
    {
        return $this->items->sum(fn ($i) => $i->qty_ordered * $i->unit_cost);
    }

    // Whether all items have been fully received
    public function isFullyReceived(): bool
    {
        return $this->items->every(fn ($i) => $i->qty_received >= $i->qty_ordered);
    }

    public static function nextNumber(): string
    {
        $prefix = \App\Models\Setting::get('purchase_prefix', 'PO');
        $last = static::withTrashed()->orderByDesc('id')->value('number');
        if (! $last) return "{$prefix}-001";
        $n = (int) substr($last, strlen($prefix) + 1);
        return sprintf("%s-%03d", $prefix, $n + 1);
    }

    public static function statuses(): array
    {
        return ['draft', 'sent', 'partial', 'received', 'cancelled'];
    }
}
