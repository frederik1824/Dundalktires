<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    protected $fillable = [
        'invoice_id', 'branch_id', 'created_by',
        'number', 'reason', 'notes', 'amount', 'refund_method',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2'];
    }

    public function invoice()   { return $this->belongsTo(Invoice::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }

    public static function nextNumber(?int $branchId = null): string
    {
        $last = static::whereHas('invoice', fn ($q) => $q->where('branch_id', $branchId))
            ->orderByDesc('id')->value('number');
        $seq = 1;
        if ($last) {
            preg_match('/(\d+)$/', $last, $m);
            $seq = isset($m[1]) ? (int) $m[1] + 1 : 1;
        }
        return 'CN-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
