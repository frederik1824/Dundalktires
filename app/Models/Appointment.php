<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'branch_id', 'customer_id', 'vehicle_id', 'assigned_to', 'created_by',
        'work_order_id', 'number', 'status', 'service_type',
        'scheduled_at', 'duration_minutes', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at'     => 'datetime',
            'duration_minutes' => 'integer',
        ];
    }

    public function branch()    { return $this->belongsTo(Branch::class); }
    public function customer()  { return $this->belongsTo(Customer::class); }
    public function vehicle()   { return $this->belongsTo(Vehicle::class); }
    public function assignedTo(){ return $this->belongsTo(User::class, 'assigned_to'); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
    public function workOrder() { return $this->belongsTo(WorkOrder::class); }

    public static function nextNumber(): string
    {
        $last = static::withTrashed()->orderByDesc('id')->value('number');
        if (! $last) return 'APT-0001';
        $n = (int) substr($last, 4);
        return 'APT-' . str_pad($n + 1, 4, '0', STR_PAD_LEFT);
    }
}
