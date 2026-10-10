<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\WorkOrder;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id', 'branch_id', 'make', 'model', 'year', 'color',
        'plate', 'vin', 'type', 'tire_size', 'mileage', 'notes', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function customer()   { return $this->belongsTo(Customer::class); }
    public function branch()     { return $this->belongsTo(Branch::class); }
    public function workOrders() { return $this->hasMany(WorkOrder::class); }

    public function getDisplayNameAttribute(): string
    {
        return "{$this->year} {$this->make} {$this->model}";
    }

    public static function vehicleTypes(): array
    {
        return [
            'sedan'  => 'Sedan',
            'suv'    => 'SUV',
            'pickup' => 'Pickup / Truck',
            'van'    => 'Van / Minivan',
            'truck'  => 'Commercial Truck',
            'other'  => 'Other',
        ];
    }
}
