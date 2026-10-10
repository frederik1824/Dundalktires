<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceConsumable extends Model
{
    protected $fillable = ['service_id', 'product_id', 'qty_default', 'notes'];

    protected $casts = ['qty_default' => 'decimal:3'];

    public function service(): BelongsTo { return $this->belongsTo(Service::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
