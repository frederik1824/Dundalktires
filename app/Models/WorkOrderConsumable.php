<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderConsumable extends Model
{
    protected $fillable = [
        'work_order_id', 'product_id', 'name', 'unit',
        'qty_used', 'unit_cost', 'total_cost', 'recorded_by', 'notes',
    ];

    protected $casts = [
        'qty_used'   => 'decimal:3',
        'unit_cost'  => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function workOrder(): BelongsTo  { return $this->belongsTo(WorkOrder::class); }
    public function product(): BelongsTo    { return $this->belongsTo(Product::class); }
    public function recordedBy(): BelongsTo { return $this->belongsTo(User::class, 'recorded_by'); }
}
