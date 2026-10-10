<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrderService extends Model
{
    protected $fillable = ['work_order_id', 'service_id', 'name', 'qty', 'unit_price', 'cost_price', 'notes'];

    protected function casts(): array
    {
        return [
            'unit_price'  => 'decimal:2',
            'cost_price'  => 'decimal:2',
        ];
    }

    public function workOrder() { return $this->belongsTo(WorkOrder::class); }
    public function service()   { return $this->belongsTo(Service::class); }

    public function getLineTotalAttribute(): float
    {
        return $this->qty * (float) $this->unit_price;
    }
}
