<?php

namespace Tests\Feature;

use App\Models\CashClose;
use App\Models\Product;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderProduct;
use App\Services\InventoryService;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class InventoryStockGuardTest extends TestCase
{
    public function test_inventory_service_throws_exception_when_deduction_exceeds_stock(): void
    {
        $product = Product::first();
        $initialStock = $product->stock_qty;

        $service = app(InventoryService::class);

        $this->expectException(ValidationException::class);
        $service->deduct($product, $initialStock + 10);
    }

    public function test_invoice_creation_fails_when_product_has_zero_or_insufficient_stock(): void
    {
        $user = User::first();
        $this->actingAs($user);

        // Ensure cash register is open
        CashClose::firstOrCreate(
            ['branch_id' => $user->branch_id, 'date' => now()->toDateString()],
            ['status' => 'open', 'opened_by' => $user->id, 'opening_cash' => 100]
        );

        $product = Product::where('stock_qty', 0)->first();
        if (! $product) {
            $product = Product::first();
            $product->update(['stock_qty' => 0]);
        }

        $payload = [
            'tax' => 0,
            'discount' => 0,
            'items' => [
                [
                    'type' => 'product',
                    'reference_id' => $product->id,
                    'name' => $product->name,
                    'qty' => 1,
                    'unit_price' => 100,
                    'discount' => 0,
                ],
            ],
            'payments' => [
                [
                    'method' => 'cash',
                    'amount' => 100,
                ],
            ],
        ];

        $response = $this->post('/invoices', $payload);
        $response->assertSessionHasErrors('items');

        $product->refresh();
        $this->assertEquals(0, $product->stock_qty);
    }

    public function test_work_order_completion_fails_if_product_has_insufficient_stock(): void
    {
        $user = User::first();
        $this->actingAs($user);

        $product = Product::where('stock_qty', 0)->first();
        if (! $product) {
            $product = Product::first();
            $product->update(['stock_qty' => 0]);
        }

        $order = WorkOrder::create([
            'number' => WorkOrder::nextNumber($user->branch_id),
            'branch_id' => $user->branch_id,
            'created_by' => $user->id,
            'status' => 'pending',
            'inventory_deducted' => false,
        ]);

        WorkOrderProduct::create([
            'work_order_id' => $order->id,
            'product_id' => $product->id,
            'name' => $product->name,
            'qty' => 2,
            'unit_price' => 50,
            'cost_price' => 20,
        ]);

        $response = $this->patch("/work-orders/{$order->id}/status", [
            'status' => 'completed',
        ]);

        $response->assertSessionHasErrors('status');

        $order->refresh();
        $this->assertEquals('pending', $order->status);
        $this->assertFalse($order->inventory_deducted);

        $product->refresh();
        $this->assertEquals(0, $product->stock_qty);
    }
}
