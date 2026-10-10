<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CreditNote;
use App\Models\Setting;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'branch_id', 'customer_id', 'work_order_id', 'created_by',
        'number', 'status', 'notes',
        'subtotal', 'tax', 'discount', 'total', 'amount_paid', 'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'    => 'decimal:2',
            'tax'         => 'decimal:2',
            'discount'    => 'decimal:2',
            'total'       => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'paid_at'     => 'datetime',
        ];
    }

    public function branch()      { return $this->belongsTo(Branch::class); }
    public function customer()    { return $this->belongsTo(Customer::class); }
    public function workOrder()   { return $this->belongsTo(WorkOrder::class); }
    public function createdBy()   { return $this->belongsTo(User::class, 'created_by'); }
    public function items()       { return $this->hasMany(InvoiceItem::class); }
    public function payments()    { return $this->hasMany(Payment::class); }
    public function creditNotes() { return $this->hasMany(CreditNote::class); }

    public static function nextNumber(?int $branchId = null): string
    {
        $prefix = Setting::get('invoice_prefix', $branchId) ?? 'INV';
        $last   = static::withTrashed()
                    ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                    ->orderByDesc('id')->value('number');

        $seq = 1;
        if ($last) {
            preg_match('/(\d+)$/', $last, $m);
            $seq = isset($m[1]) ? (int) $m[1] + 1 : 1;
        }
        return $prefix . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function getBalanceAttribute(): float
    {
        return (float) $this->total - (float) $this->amount_paid;
    }

    public function recalculate(): void
    {
        $subtotal = $this->items->sum(fn ($i) => $i->total);
        $total    = $subtotal + $this->tax - $this->discount;
        $this->update(['subtotal' => $subtotal, 'total' => $total]);
    }

    public function refreshStatus(): void
    {
        $paid = $this->payments()->sum('amount');
        $this->update(['amount_paid' => $paid]);

        if ($paid <= 0) {
            $this->update(['status' => 'draft']);
        } elseif ($paid >= $this->total) {
            $this->update(['status' => 'paid', 'paid_at' => now()]);
        } else {
            $this->update(['status' => 'partial']);
        }
    }
}
