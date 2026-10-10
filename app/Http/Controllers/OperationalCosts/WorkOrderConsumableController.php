<?php

namespace App\Http\Controllers\OperationalCosts;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\WorkOrder;
use App\Models\WorkOrderConsumable;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkOrderConsumableController extends Controller
{
    public function store(Request $request, WorkOrder $workOrder)
    {
        $data = $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'name' => 'required|string|max:200',
            'unit' => 'nullable|string|max:20',
            'qty_used' => 'required|numeric|min:0.001',
            'unit_cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'save_to_catalog' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($data, $request, $workOrder) {
            $productId = $data['product_id'] ?? null;

            if ($request->boolean('save_to_catalog') && empty($productId)) {
                $newProduct = Product::create([
                    'branch_id' => $workOrder->branch_id,
                    'type' => 'consumable',
                    'name' => $data['name'],
                    'unit' => $data['unit'] ?? 'unit',
                    'cost_price' => $data['unit_cost'],
                    'sale_price' => $data['unit_cost'],
                    'stock_qty' => 0,
                    'is_active' => true,
                ]);
                $productId = $newProduct->id;
            }

            $product = $productId ? Product::find($productId) : null;
            $unitCost = (float) $data['unit_cost'];
            $qtyUsed = (float) $data['qty_used'];
            $totalCost = round($qtyUsed * $unitCost, 2);

            WorkOrderConsumable::create([
                'work_order_id' => $workOrder->id,
                'product_id' => $productId,
                'name' => $data['name'],
                'unit' => $product?->unit ?? ($data['unit'] ?? 'unit'),
                'qty_used' => $qtyUsed,
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
                'recorded_by' => auth()->id(),
                'notes' => $data['notes'] ?? null,
            ]);

            // Deduct from stock if linked to a product and sufficient stock exists
            $deductQty = (int) ceil($qtyUsed);
            if ($product && $product->stock_qty >= $deductQty && $deductQty > 0) {
                app(InventoryService::class)->deduct(
                    $product, $deductQty, 'consumption', $workOrder
                );
            }

            $workOrder->load('services', 'products', 'consumables', 'tireDisposals');
            $workOrder->refreshCosts();
        });

        return back()->with('success', 'Consumable recorded.');
    }

    public function destroy(WorkOrder $workOrder, WorkOrderConsumable $consumable)
    {
        $consumable->delete();

        $workOrder->load('services', 'products', 'consumables', 'tireDisposals');
        $workOrder->refreshCosts();

        return back()->with('success', 'Consumable removed.');
    }
}
