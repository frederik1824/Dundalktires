<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TireDisposal extends Model
{
    protected $fillable = [
        'branch_id', 'work_order_id', 'created_by',
        'qty_total', 'qty_client_kept', 'qty_for_disposal',
        'cost_per_unit', 'status', 'notes',
    ];

    protected $casts = [
        'qty_total'       => 'integer',
        'qty_client_kept' => 'integer',
        'qty_for_disposal'=> 'integer',
        'cost_per_unit'   => 'decimal:2',
    ];

    public function branch(): BelongsTo    { return $this->belongsTo(Branch::class); }
    public function workOrder(): BelongsTo { return $this->belongsTo(WorkOrder::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function pickupItems(): HasMany  { return $this->hasMany(TireDisposalPickupItem::class, 'tire_disposal_id'); }

    /** How many have already been collected across all pickups */
    public function qtyCollected(): int
    {
        return (int) $this->pickupItems()->sum('qty_collected');
    }

    /** Remaining tires still pending pickup */
    public function qtyPending(): int
    {
        return max(0, $this->qty_for_disposal - $this->qtyCollected());
    }

    /** Total estimated disposal cost */
    public function totalCost(): float
    {
        return round($this->qty_for_disposal * $this->cost_per_unit, 2);
    }

    /** Recalculate status based on collected qty */
    public function refreshStatus(): void
    {
        $collected = $this->qtyCollected();
        $status = match(true) {
            $collected <= 0                           => 'pending',
            $collected >= $this->qty_for_disposal     => 'collected',
            default                                   => 'partial',
        };
        $this->update(['status' => $status]);
    }
}
