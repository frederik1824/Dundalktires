<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TireDisposalPickup extends Model
{
    protected $fillable = [
        'branch_id', 'created_by', 'expense_id',
        'vendor', 'pickup_date', 'qty_collected',
        'cost_per_unit', 'total_cost', 'payment_method',
        'evidence', 'notes',
    ];

    protected $casts = [
        'pickup_date'   => 'date',
        'qty_collected' => 'integer',
        'cost_per_unit' => 'decimal:2',
        'total_cost'    => 'decimal:2',
    ];

    public function branch(): BelongsTo    { return $this->belongsTo(Branch::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function expense(): BelongsTo   { return $this->belongsTo(Expense::class); }
    public function items(): HasMany       { return $this->hasMany(TireDisposalPickupItem::class, 'pickup_id'); }
}
