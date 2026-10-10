<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'branch_id', 'created_by', 'category_id', 'description', 'amount',
        'payment_method', 'vendor', 'reference', 'evidence',
        'is_recurring', 'recurrence', 'paid_at', 'next_due_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount'       => 'decimal:2',
            'is_recurring' => 'boolean',
            'paid_at'      => 'date',
            'next_due_at'  => 'date',
        ];
    }

    public function branch()    { return $this->belongsTo(Branch::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
    public function category()  { return $this->belongsTo(ExpenseCategory::class, 'category_id'); }

    public function scopeSearch($query, ?string $term)
    {
        if (! $term) return $query;
        return $query->where(function ($q) use ($term) {
            $q->where('description', 'like', "%{$term}%")
              ->orWhere('vendor', 'like', "%{$term}%")
              ->orWhere('reference', 'like', "%{$term}%");
        });
    }
}
