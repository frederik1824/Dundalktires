<?php

namespace App\Http\Controllers\WorkOrders;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderProduct;
use App\Models\WorkOrderService;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class WorkOrderController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_work_orders|manage_work_orders', only: ['index', 'show']),
            new Middleware('permission:manage_work_orders', only: ['create', 'store', 'edit', 'update', 'updateStatus', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $status = $request->input('status');

        $query = WorkOrder::query()
            ->with([
                'customer:id,name,phone',
                'vehicle:id,make,model,year,plate',
                'assignedTo:id,name',
            ])
            ->withCount(['services', 'products'])
            ->when($status && $status !== 'all', fn ($q) => $q->where('status', $status))
            ->orderByDesc('created_at');

        $orders = $query->get();

        return Inertia::render('WorkOrders/Index', [
            'orders' => $orders,
            'filters' => $request->only('status', 'view'),
            'statuses' => WorkOrder::statuses(),
            'technicians' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create()
    {
        return Inertia::render('WorkOrders/Form', [
            'customers' => Customer::active()->orderBy('name')->get(['id', 'name', 'phone']),
            'technicians' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:2000',
            'internal_notes' => 'nullable|string|max:2000',
            'promised_at' => 'nullable|date',
            'services' => 'array',
            'services.*.service_id' => 'nullable|exists:services,id',
            'services.*.name' => 'required|string|max:200',
            'services.*.qty' => 'required|integer|min:1',
            'services.*.unit_price' => 'required|numeric|min:0',
            'services.*.cost_price' => 'nullable|numeric|min:0',
            'services.*.notes' => 'nullable|string|max:500',
            'products' => 'array',
            'products.*.product_id' => 'nullable|exists:products,id',
            'products.*.name' => 'required|string|max:200',
            'products.*.qty' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'products.*.cost_price' => 'nullable|numeric|min:0',
            'products.*.notes' => 'nullable|string|max:500',
        ]);

        $wo = DB::transaction(function () use ($data) {
            $wo = WorkOrder::create([
                'branch_id' => auth()->user()->branch_id,
                'customer_id' => $data['customer_id'] ?? null,
                'vehicle_id' => $data['vehicle_id'] ?? null,
                'assigned_to' => $data['assigned_to'] ?? null,
                'created_by' => auth()->id(),
                'number' => WorkOrder::nextNumber(auth()->user()->branch_id),
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
                'internal_notes' => $data['internal_notes'] ?? null,
                'promised_at' => $data['promised_at'] ?? null,
            ]);

            foreach ($data['services'] ?? [] as $s) {
                if (empty($s['cost_price']) && ! empty($s['service_id'])) {
                    $s['cost_price'] = Service::find($s['service_id'])?->cost_price ?? 0;
                }
                WorkOrderService::create(array_merge($s, ['work_order_id' => $wo->id]));
            }
            foreach ($data['products'] ?? [] as $p) {
                if (empty($p['cost_price']) && ! empty($p['product_id'])) {
                    $p['cost_price'] = Product::find($p['product_id'])?->cost_price ?? 0;
                }
                WorkOrderProduct::create(array_merge($p, ['work_order_id' => $wo->id]));
            }

            $wo->load('services', 'products');
            $wo->recalculate();

            return $wo;
        });

        return redirect()->route('work-orders.show', $wo)
            ->with('success', "Work order {$wo->number} created.");
    }

    public function show(WorkOrder $workOrder)
    {
        $workOrder->load([
            'customer', 'vehicle', 'assignedTo', 'createdBy',
            'services', 'products', 'invoices.payments',
            'consumables.product', 'consumables.recordedBy',
            'tireDisposals.createdBy',
        ]);

        $branchId = $workOrder->branch_id ?? auth()->user()->branch_id;

        $availableConsumables = Product::whereIn('type', ['consumable', 'material', 'part'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'unit', 'cost_price', 'sale_price', 'stock_qty', 'sku']);

        return Inertia::render('WorkOrders/Show', [
            'order' => $workOrder,
            'statuses' => WorkOrder::statuses(),
            'technicians' => User::orderBy('name')->get(['id', 'name']),
            'disposalCostUnit' => (float) (Setting::get('tire_disposal_cost', null, $branchId) ?? 0),
            'availableConsumables' => $availableConsumables,
        ]);
    }

    public function edit(WorkOrder $workOrder)
    {
        $workOrder->load('services', 'products', 'customer.vehicles');

        $selectedCustomer = null;
        if ($workOrder->customer) {
            $selectedCustomer = [
                'id' => $workOrder->customer->id,
                'name' => $workOrder->customer->name,
                'phone' => $workOrder->customer->phone ?? null,
                'vehicles' => $workOrder->customer->vehicles,
            ];
        }

        return Inertia::render('WorkOrders/Form', [
            'order' => $workOrder,
            'selected_customer' => $selectedCustomer,
            'technicians' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, WorkOrder $workOrder)
    {
        $data = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:2000',
            'internal_notes' => 'nullable|string|max:2000',
            'promised_at' => 'nullable|date',
            'services' => 'array',
            'services.*.service_id' => 'nullable|exists:services,id',
            'services.*.name' => 'required|string|max:200',
            'services.*.qty' => 'required|integer|min:1',
            'services.*.unit_price' => 'required|numeric|min:0',
            'services.*.cost_price' => 'nullable|numeric|min:0',
            'products' => 'array',
            'products.*.product_id' => 'nullable|exists:products,id',
            'products.*.name' => 'required|string|max:200',
            'products.*.qty' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'products.*.cost_price' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($data, $workOrder) {
            $workOrder->update($data);
            $workOrder->services()->delete();
            $workOrder->products()->delete();

            foreach ($data['services'] ?? [] as $s) {
                if (empty($s['cost_price']) && ! empty($s['service_id'])) {
                    $s['cost_price'] = Service::find($s['service_id'])?->cost_price ?? 0;
                }
                WorkOrderService::create(array_merge($s, ['work_order_id' => $workOrder->id]));
            }
            foreach ($data['products'] ?? [] as $p) {
                if (empty($p['cost_price']) && ! empty($p['product_id'])) {
                    $p['cost_price'] = Product::find($p['product_id'])?->cost_price ?? 0;
                }
                WorkOrderProduct::create(array_merge($p, ['work_order_id' => $workOrder->id]));
            }

            $workOrder->load('services', 'products');
            $workOrder->recalculate();
        });

        return redirect()->route('work-orders.show', $workOrder)
            ->with('success', 'Work order updated.');
    }

    public function updateStatus(Request $request, WorkOrder $workOrder)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        // An invoiced order cannot be rolled back to pending or cancelled
        if ($workOrder->invoiced && in_array($data['status'], ['pending', 'cancelled'])) {
            return back()->withErrors(['status' => 'Esta orden ya tiene una factura emitida y no puede revertirse.']);
        }

        // If completing and inventory has not been deducted yet, validate available stock
        if ($data['status'] === 'completed' && ! $workOrder->inventory_deducted) {
            $workOrder->loadMissing('products');
            $needed = [];
            foreach ($workOrder->products as $item) {
                if ($item->product_id) {
                    $needed[$item->product_id] = ($needed[$item->product_id] ?? 0) + $item->qty;
                }
            }
            if (! empty($needed)) {
                $products = Product::whereIn('id', array_keys($needed))->get()->keyBy('id');
                foreach ($needed as $pId => $qty) {
                    $product = $products->get($pId);
                    $stock = (int) ($product?->stock_qty ?? 0);
                    if (! $product || $stock < $qty) {
                        $name = $product ? ($product->type === 'tire' ? trim("{$product->tire_brand} {$product->tire_line} {$product->tire_size}") : $product->name) : "ID #{$pId}";
                        $avail = max(0, $stock);

                        return back()->withErrors([
                            'status' => "No se puede completar la orden. Stock insuficiente para '{$name}'. Disponible: {$avail}, requerido: {$qty}.",
                        ]);
                    }
                }
            }
        }

        $updates = ['status' => $data['status']];

        if ($data['status'] === 'in_progress' && ! $workOrder->started_at) {
            $updates['started_at'] = now();
        }
        if ($data['status'] === 'completed' && ! $workOrder->completed_at) {
            $updates['completed_at'] = now();
        }

        DB::transaction(function () use ($workOrder, $data, &$updates) {
            if ($data['status'] === 'completed' && ! $workOrder->inventory_deducted) {
                $workOrder->loadMissing('products');
                $inv = app(InventoryService::class);
                foreach ($workOrder->products as $item) {
                    if ($item->product_id) {
                        $product = Product::find($item->product_id);
                        if ($product) {
                            $inv->deduct($product, $item->qty, 'sale', $workOrder);
                        }
                    }
                }
                $updates['inventory_deducted'] = true;
            } elseif ($workOrder->inventory_deducted && in_array($data['status'], ['pending', 'in_progress', 'cancelled'])) {
                // If reverting from completed, restore deducted inventory
                $workOrder->loadMissing('products');
                $inv = app(InventoryService::class);
                foreach ($workOrder->products as $item) {
                    if ($item->product_id) {
                        $product = Product::find($item->product_id);
                        if ($product) {
                            $inv->receive($product, $item->qty, null, $workOrder, "Reversa de orden {$workOrder->number}");
                        }
                    }
                }
                $updates['inventory_deducted'] = false;
            }

            $workOrder->update($updates);
        });

        return back()->with('success', 'Status updated.');
    }

    public function destroy(WorkOrder $workOrder)
    {
        $workOrder->delete();

        return redirect()->route('work-orders.index')
            ->with('success', 'Work order removed.');
    }
}
