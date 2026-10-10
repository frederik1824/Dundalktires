<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id', 'received_by', 'method', 'amount', 'reference', 'notes', 'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'  => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function invoice()    { return $this->belongsTo(Invoice::class); }
    public function receivedBy() { return $this->belongsTo(User::class, 'received_by'); }

    public static function methods(): array
    {
        return [
            'cash'     => 'Cash',
            'card'     => 'Card',
            'zelle'    => 'Zelle',
            'cashapp'  => 'Cash App',
            'transfer' => 'Bank Transfer',
            'other'    => 'Other',
        ];
    }
}
