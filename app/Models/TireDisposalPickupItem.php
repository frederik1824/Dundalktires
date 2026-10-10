<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TireDisposalPickupItem extends Model
{
    protected $fillable = ['pickup_id', 'tire_disposal_id', 'qty_collected'];

    protected $casts = ['qty_collected' => 'integer'];

    public function pickup(): BelongsTo      { return $this->belongsTo(TireDisposalPickup::class, 'pickup_id'); }
    public function disposal(): BelongsTo    { return $this->belongsTo(TireDisposal::class, 'tire_disposal_id'); }
}
