<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePriceRule extends Model
{
    protected $fillable = ['service_id', 'vehicle_type', 'price'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2'];
    }

    public function service() { return $this->belongsTo(Service::class); }
}
