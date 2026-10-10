<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'branch_id', 'customer_id', 'vehicle_id', 'assigned_to', 'created_by',
        'number', 'status', 'notes', 'internal_notes',
        'subtotal', 'tax', 'total', 'invoiced', 'inventory_deducted',
        'cost_products', 'cost_services', 'cost_consumables', 'cost_disposal',
        'margin_amount', 'margin_pct',
        'promised_at', 'started_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'invoiced' => 'boolean',
            'inventory_deducted' => 'boolean',
            'cost_products' => 'decimal:2',
            'cost_services' => 'decimal:2',
            'cost_consumables' => 'decimal:2',
            'cost_disposal' => 'decimal:2',
            'margin_amount' => 'decimal:2',
            'margin_pct' => 'decimal:2',
            'promised_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function services()
    {
        return $this->hasMany(WorkOrderService::class);
    }

    public function products()
    {
        return $this->hasMany(WorkOrderProduct::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function consumables()
    {
        return $this->hasMany(WorkOrderConsumable::class);
    }

    public function tireDisposals()
    {
        return $this->hasMany(TireDisposal::class);
    }

    public static function nextNumber(?int $branchId = null): string
    {
        $prefix = Setting::get('work_order_prefix', $branchId) ?? 'WO';
        $last = static::withTrashed()
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->orderByDesc('id')->value('number');

        $seq = 1;
        if ($last) {
            preg_match('/(\d+)$/', $last, $m);
            $seq = isset($m[1]) ? (int) $m[1] + 1 : 1;
        }

        return $prefix.'-'.str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public function recalculate(): void
    {
        $serviceTotal = $this->services->sum(fn ($s) => $s->qty * $s->unit_price);
        $productTotal = $this->products->sum(fn ($p) => $p->qty * $p->unit_price);
        $subtotal = $serviceTotal + $productTotal;
        $this->update([
            'subtotal' => $subtotal,
            'total' => $subtotal + $this->tax,
        ]);
    }

    /**
     * Recalculate direct costs and gross margin.
     * Call after adding/removing consumables, tire disposals, or updating cost_price on items.
     */
    public function refreshCosts(): void
    {
        $this->loadMissing(['services', 'products', 'consumables', 'tireDisposals']);

        $costServices = $this->services->sum(fn ($s) => $s->qty * $s->cost_price);
        $costProducts = $this->products->sum(fn ($p) => $p->qty * $p->cost_price);
        $costConsumables = $this->consumables->sum(fn ($c) => (float) $c->total_cost);
        $costDisposal = $this->tireDisposals->sum(fn ($d) => $d->totalCost());

        $totalCost = $costServices + $costProducts + $costConsumables + $costDisposal;
        $revenue = (float) $this->total;
        $marginAmount = $revenue - $totalCost;
        $marginPct = $revenue > 0 ? round(($marginAmount / $revenue) * 100, 2) : null;

        $this->update([
            'cost_services' => $costServices,
            'cost_products' => $costProducts,
            'cost_consumables' => $costConsumables,
            'cost_disposal' => $costDisposal,
            'margin_amount' => $marginAmount,
            'margin_pct' => $marginPct,
        ]);
    }

    public static function statuses(): array
    {
        return ['pending', 'in_progress', 'completed', 'cancelled'];
    }
}
