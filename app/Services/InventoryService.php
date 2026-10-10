<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductMovement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    public function adjust(
        Product $product,
        int $qtyChange,
        string $type,
        ?string $notes = null,
        ?Model $reference = null,
        ?float $unitCost = null,
    ): ProductMovement {
        return DB::transaction(function () use ($product, $qtyChange, $type, $notes, $reference, $unitCost) {
            // Lock the row to prevent race conditions
            $product = Product::lockForUpdate()->find($product->id);

            $qtyBefore = $product->stock_qty;
            $qtyAfter = $qtyBefore + $qtyChange;

            if ($qtyAfter < 0) {
                $name = $product->type === 'tire'
                    ? trim("{$product->tire_brand} {$product->tire_line} {$product->tire_size}")
                    : $product->name;
                $avail = max(0, $qtyBefore);
                $req = abs($qtyChange);
                throw ValidationException::withMessages([
                    'stock' => "Stock insuficiente para '{$name}'. Disponible: {$avail}, solicitado: {$req}.",
                ]);
            }

            $product->update(['stock_qty' => $qtyAfter]);
            Cache::forget('global_low_stock_count');

            return ProductMovement::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'branch_id' => $product->branch_id,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference?->id,
                'type' => $type,
                'qty_before' => $qtyBefore,
                'qty_change' => $qtyChange,
                'qty_after' => $qtyAfter,
                'unit_cost' => $unitCost,
                'notes' => $notes,
            ]);
        });
    }

    public function receive(Product $product, int $qty, ?float $unitCost = null, ?Model $reference = null, ?string $notes = null): ProductMovement
    {
        return $this->adjust($product, abs($qty), 'purchase', $notes, $reference, $unitCost);
    }

    public function deduct(Product $product, int $qty, string $type = 'sale', ?Model $reference = null, ?string $notes = null): ProductMovement
    {
        return $this->adjust($product, -abs($qty), $type, $notes, $reference);
    }

    public function correction(Product $product, int $newQty, ?string $notes = null): ProductMovement
    {
        $diff = $newQty - $product->stock_qty;

        return $this->adjust($product, $diff, 'correction', $notes);
    }
}
