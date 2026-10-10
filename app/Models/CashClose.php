<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashClose extends Model
{
    protected $fillable = [
        'branch_id', 'date',
        'opened_by', 'opened_at', 'opening_fund',
        'closed_by', 'closed_at', 'status',
        'system_total', 'system_amounts',
        'physical_total', 'physical_amounts', 'difference',
        'notes',
    ];

    protected $casts = [
        'date'             => 'date',
        'opened_at'        => 'datetime',
        'closed_at'        => 'datetime',
        'opening_fund'     => 'decimal:2',
        'system_total'     => 'decimal:2',
        'physical_total'   => 'decimal:2',
        'difference'       => 'decimal:2',
        'system_amounts'   => 'array',
        'physical_amounts' => 'array',
    ];

    public function branch()   { return $this->belongsTo(Branch::class); }
    public function openedBy() { return $this->belongsTo(User::class, 'opened_by'); }
    public function closedBy() { return $this->belongsTo(User::class, 'closed_by'); }
}
