<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PurchaseOrderController extends Controller implements HasMiddleware
{
    public function __construct(private InventoryService $inventory) {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_purchases|manage_purchases', only: ['index', 'show']),
            new Middleware('permission:manage_purchases', only: ['create', 'store', 'receive', 'uploadAttachment', 'updateStatus', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $orders = PurchaseOrder::with(['supplier:id,name', 'createdBy:id,name', 'items'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->supplier_id, fn ($q, $id) => $q->where('supplier_id', $id))
            ->when($request->search, fn ($q, $s) => $q->where('number', 'like', "%{$s}%")->orWhere('notes', 'like', "%{$s}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Purchases/Index', [
            'orders'    => $orders,
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'filters'   => $request->only('status', 'supplier_id', 'search'),
        ]);
    }

    public function create(Request $request)
    {
        $type = $request->get('type', 'regular'); // regular | suggested

        $suggestedItems = [];
        if ($type === 'suggested') {
            $suggestedItems = Product::lowStock()
                ->where('is_active', true)
                ->with(['supplier:id,name', 'category:id,name'])
                ->get()
                ->map(fn ($p) => [
                    'product_id'    => $p->id,
                    'product'       => [
                        'id'        => $p->id,
                        'name'      => $p->type === 'tire'
                            ? "{$p->tire_brand} {$p->tire_line} {$p->tire_size}"
                            : $p->name,
                        'type'      => $p->type,
                        'tire_size' => $p->tire_size,
                        'stock_qty' => $p->stock_qty,
                        'stock_min' => $p->stock_min,
                    ],
                    'qty_ordered'   => max(1, $p->stock_min * 2 - $p->stock_qty), // suggest enough to reach 2x min
                    'unit_cost'     => $p->cost_price,
                    'notes'         => '',
                ]);
        }

        return Inertia::render('Purchases/Form', [
            'orderType'      => $type,
            'suggestedItems' => $suggestedItems,
            'suppliers'      => Supplier::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'nextNumber'     => PurchaseOrder::nextNumber(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'type'        => 'required|in:regular,suggested',
            'notes'       => 'nullable|string',
            'ordered_at'  => 'nullable|date',
            'items'       => 'required|array|min:1',
            'items.*.product_id'  => 'required|exists:products,id',
            'items.*.qty_ordered' => 'required|integer|min:1',
            'items.*.unit_cost'   => 'required|numeric|min:0',
            'items.*.notes'       => 'nullable|string',
        ]);

        $order = DB::transaction(function () use ($data) {
            $order = PurchaseOrder::create([
                'supplier_id' => $data['supplier_id'] ?? null,
                'created_by'  => auth()->id(),
                'number'      => PurchaseOrder::nextNumber(),
                'status'      => 'draft',
                'type'        => $data['type'],
                'notes'       => $data['notes'] ?? null,
                'ordered_at'  => $data['ordered_at'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $order->id,
                    'product_id'        => $item['product_id'],
                    'qty_ordered'       => $item['qty_ordered'],
                    'qty_received'      => 0,
                    'unit_cost'         => $item['unit_cost'],
                    'notes'             => $item['notes'] ?? null,
                ]);
            }

            return $order;
        });

        return redirect()->route('purchases.show', $order)
            ->with('success', "Purchase order {$order->number} created.");
    }

    public function show(PurchaseOrder $purchase)
    {
        $purchase->load([
            'supplier:id,name,phone,email',
            'createdBy:id,name',
            'receivedBy:id,name',
            'items.product:id,name,type,tire_brand,tire_line,tire_width,tire_ratio,tire_diameter,stock_qty',
        ]);

        return Inertia::render('Purchases/Show', [
            'order' => $purchase,
        ]);
    }

    public function receive(Request $request, PurchaseOrder $purchase)
    {
        if (! in_array($purchase->status, ['draft', 'sent', 'partial'])) {
            return back()->with('error', 'This order cannot be received.');
        }

        $data = $request->validate([
            'items'              => 'required|array',
            'items.*.id'         => 'required|exists:purchase_order_items,id',
            'items.*.qty_received' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($data, $purchase) {
            foreach ($data['items'] as $row) {
                $item = PurchaseOrderItem::find($row['id']);
                if ($item->purchase_order_id !== $purchase->id) continue;

                $newQty = (int) $row['qty_received'];
                $delta  = $newQty - $item->qty_received;

                if ($delta > 0) {
                    $this->inventory->receive(
                        $item->product,
                        $delta,
                        (float) $item->unit_cost,
                        $purchase,
                        "Received on {$purchase->number}"
                    );

                    // Update the product's cost price if it changed
                    if ($item->unit_cost > 0) {
                        $item->product->update(['cost_price' => $item->unit_cost]);
                    }
                }

                $item->update(['qty_received' => $newQty]);
            }

            // Re-check status
            $purchase->refresh()->load('items');
            $status = 'partial';
            if ($purchase->isFullyReceived()) {
                $status = 'received';
            }

            $purchase->update([
                'status'      => $status,
                'received_by' => auth()->id(),
                'received_at' => now(),
            ]);
        });

        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Stock updated from purchase order.');
    }

    public function uploadAttachment(Request $request, PurchaseOrder $purchase)
    {
        $request->validate([
            'attachment' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
        ]);

        // Delete old attachment
        if ($purchase->attachment) {
            Storage::disk('public')->delete($purchase->attachment);
        }

        $path = $request->file('attachment')->store('purchases', 'public');
        $purchase->update(['attachment' => $path]);

        return back()->with('success', 'Document attached.');
    }

    public function updateStatus(Request $request, PurchaseOrder $purchase)
    {
        $data = $request->validate(['status' => 'required|in:draft,sent,cancelled']);
        $purchase->update(['status' => $data['status']]);
        return back()->with('success', 'Status updated.');
    }

    public function destroy(PurchaseOrder $purchase)
    {
        if ($purchase->status !== 'draft') {
            return back()->with('error', 'Only draft orders can be deleted.');
        }
        $purchase->delete();
        return redirect()->route('purchases.index')->with('success', 'Order deleted.');
    }
}
