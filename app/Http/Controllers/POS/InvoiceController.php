<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\CashClose;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Setting;
use App\Models\WorkOrder;
use App\Services\InventoryService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class InvoiceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_sales|manage_sales', only: ['index', 'show', 'pdf', 'receipt']),
            new Middleware('permission:manage_sales', only: ['create', 'store', 'addPayment', 'void']),
        ];
    }

    public function index(Request $request)
    {
        $invoices = Invoice::query()
            ->with(['customer', 'createdBy'])
            ->withCount('items')
            ->when($request->input('q'), fn ($q, $term) => $q->where(function ($q2) use ($term) {
                $q2->where('number', 'like', "%{$term}%")
                    ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$term}%"));
            })
            )
            ->when($request->input('status'), fn ($q, $v) => $q->where('status', $v))
            ->orderByDesc('created_at')
            ->paginate(20)->withQueryString();

        return Inertia::render('POS/List', [
            'invoices' => $invoices,
            'filters' => $request->only('q', 'status'),
        ]);
    }

    public function create(Request $request)
    {
        $workOrder = null;
        if ($request->input('work_order_id')) {
            $workOrder = WorkOrder::with('customer', 'vehicle', 'services', 'products')
                ->findOrFail($request->input('work_order_id'));
        }

        $branchId = auth()->user()->branch_id;
        $cashOpen = CashClose::where('branch_id', $branchId)
            ->where('date', now()->toDateString())
            ->where('status', 'open')
            ->exists();

        return Inertia::render('POS/Index', [
            'customers' => Customer::active()->orderBy('name')->get(['id', 'name', 'phone']),
            'paymentMethods' => Payment::methods(),
            'prefillOrder' => $workOrder,
            'cashOpen' => $cashOpen,
        ]);
    }

    private function assertCashOpen(): void
    {
        $branchId = auth()->user()->branch_id;
        $open = CashClose::where('branch_id', $branchId)
            ->where('date', now()->toDateString())
            ->where('status', 'open')
            ->exists();

        if (! $open) {
            abort(422, 'La caja no está abierta. Debe abrir la caja antes de registrar ventas o pagos.');
        }
    }

    public function store(Request $request)
    {
        $this->assertCashOpen();

        $data = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'work_order_id' => 'nullable|exists:work_orders,id',
            'notes' => 'nullable|string|max:1000',
            'tax' => 'numeric|min:0',
            'discount' => 'numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:service,product,other',
            'items.*.reference_id' => 'nullable|integer',
            'items.*.name' => 'required|string|max:200',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'numeric|min:0',
            'payments' => 'array',
            'payments.*.method' => 'required|in:cash,card,zelle,cashapp,transfer,other',
            'payments.*.amount' => 'required|numeric|min:0.01',
            'payments.*.reference' => 'nullable|string|max:100',
        ]);

        // Check linked work order and validate inventory before transaction
        $workOrder = ! empty($data['work_order_id'])
            ? WorkOrder::with('products')->find($data['work_order_id'])
            : null;

        $woProductQuantities = [];
        if ($workOrder) {
            foreach ($workOrder->products as $wp) {
                if ($wp->product_id) {
                    $woProductQuantities[$wp->product_id] = ($woProductQuantities[$wp->product_id] ?? 0) + $wp->qty;
                }
            }
        }

        // Calculate quantities that must actually be deducted from inventory
        $qtyToDeduct = [];
        $remainingWoQty = ($workOrder && $workOrder->inventory_deducted) ? $woProductQuantities : [];

        foreach ($data['items'] as $item) {
            if ($item['type'] === 'product' && ! empty($item['reference_id'])) {
                $pId = (int) $item['reference_id'];
                $qty = (int) $item['qty'];

                if (! empty($remainingWoQty[$pId])) {
                    $deductedByWo = min($qty, $remainingWoQty[$pId]);
                    $qty -= $deductedByWo;
                    $remainingWoQty[$pId] -= $deductedByWo;
                }

                if ($qty > 0) {
                    $qtyToDeduct[$pId] = ($qtyToDeduct[$pId] ?? 0) + $qty;
                }
            }
        }

        // Validate stock available for items that need deduction
        if (! empty($qtyToDeduct)) {
            $products = Product::whereIn('id', array_keys($qtyToDeduct))->get()->keyBy('id');
            foreach ($qtyToDeduct as $pId => $needed) {
                $prod = $products->get($pId);
                $stock = (int) ($prod?->stock_qty ?? 0);
                if (! $prod || $stock < $needed) {
                    $name = $prod ? ($prod->type === 'tire' ? trim("{$prod->tire_brand} {$prod->tire_line} {$prod->tire_size}") : $prod->name) : "ID #{$pId}";
                    $avail = max(0, $stock);
                    throw ValidationException::withMessages([
                        'items' => "No se puede vender '{$name}'. Stock disponible: {$avail}, solicitado para descontar: {$needed}.",
                    ]);
                }
            }
        }

        $invoice = DB::transaction(function () use ($data, $workOrder, $woProductQuantities) {
            $invoice = Invoice::create([
                'branch_id' => auth()->user()->branch_id,
                'customer_id' => $data['customer_id'] ?? null,
                'work_order_id' => $data['work_order_id'] ?? null,
                'created_by' => auth()->id(),
                'number' => Invoice::nextNumber(auth()->user()->branch_id),
                'status' => 'draft',
                'notes' => $data['notes'] ?? null,
                'tax' => $data['tax'] ?? 0,
                'discount' => $data['discount'] ?? 0,
            ]);

            $woDeductTracking = ($workOrder && $workOrder->inventory_deducted) ? $woProductQuantities : [];

            foreach ($data['items'] as $item) {
                $lineTotal = ($item['qty'] * $item['unit_price']) - ($item['discount'] ?? 0);
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'type' => $item['type'],
                    'reference_id' => $item['reference_id'] ?? null,
                    'name' => $item['name'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'] ?? 0,
                    'total' => max(0, $lineTotal),
                ]);

                // Deduct stock for product items (avoiding double deduction if WO already deducted)
                if ($item['type'] === 'product' && ! empty($item['reference_id'])) {
                    $pId = (int) $item['reference_id'];
                    $qty = (int) $item['qty'];

                    if (! empty($woDeductTracking[$pId])) {
                        $alreadyDeducted = min($qty, $woDeductTracking[$pId]);
                        $qty -= $alreadyDeducted;
                        $woDeductTracking[$pId] -= $alreadyDeducted;
                    }

                    if ($qty > 0) {
                        $product = Product::find($pId);
                        if ($product) {
                            app(InventoryService::class)->deduct(
                                $product, $qty, 'sale', $invoice
                            );
                        }
                    }
                }
            }

            $invoice->load('items');
            $invoice->recalculate();

            foreach ($data['payments'] ?? [] as $pay) {
                Payment::create([
                    'invoice_id' => $invoice->id,
                    'received_by' => auth()->id(),
                    'method' => $pay['method'],
                    'amount' => $pay['amount'],
                    'reference' => $pay['reference'] ?? null,
                    'paid_at' => now(),
                ]);
            }

            $invoice->refreshStatus();

            // Mark linked work order as invoiced and inventory_deducted
            if ($invoice->work_order_id) {
                WorkOrder::where('id', $invoice->work_order_id)->update([
                    'invoiced' => true,
                    'inventory_deducted' => true,
                ]);
            }

            return $invoice;
        });

        return redirect()->route('invoices.show', $invoice)
            ->with('success', "Invoice {$invoice->number} created.");
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'workOrder.vehicle', 'items', 'payments.receivedBy', 'createdBy', 'creditNotes.createdBy']);

        $branchId = auth()->user()->branch_id;
        $cashOpen = CashClose::where('branch_id', $branchId)
            ->where('date', now()->toDateString())
            ->where('status', 'open')
            ->exists();

        return Inertia::render('POS/Show', [
            'invoice' => $invoice,
            'paymentMethods' => Payment::methods(),
            'cashOpen' => $cashOpen,
        ]);
    }

    public function addPayment(Request $request, Invoice $invoice)
    {
        $this->assertCashOpen();

        $data = $request->validate([
            'method' => 'required|in:cash,card,zelle,cashapp,transfer,other',
            'amount' => 'required|numeric|min:0.01',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($data, $invoice) {
            Payment::create([
                'invoice_id' => $invoice->id,
                'received_by' => auth()->id(),
                'method' => $data['method'],
                'amount' => $data['amount'],
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'paid_at' => now(),
            ]);
            $invoice->refreshStatus();
        });

        return back()->with('success', 'Payment recorded.');
    }

    public function void(Invoice $invoice)
    {
        $invoice->update(['status' => 'void']);

        return back()->with('success', 'Invoice voided.');
    }

    public function receipt(Request $request, Invoice $invoice)
    {
        $invoice->load(['customer', 'workOrder.vehicle', 'items', 'payments.receivedBy', 'createdBy']);

        $branchId = $invoice->branch_id;
        $settings = [
            'business_name' => Setting::get('business_name', $branchId) ?? 'Dundalk Used & New Tires',
            'business_address' => Setting::get('business_address', $branchId) ?? '',
            'business_phone' => Setting::get('business_phone', $branchId) ?? '',
            'business_email' => Setting::get('business_email', $branchId) ?? '',
        ];
        $currency = Setting::get('currency_symbol', $branchId) ?? '$';
        $width = in_array((int) $request->input('width', 80), [58, 80]) ? (int) $request->input('width', 80) : 80;

        return view('receipt.invoice', compact('invoice', 'settings', 'currency', 'width'));
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load(['customer', 'workOrder.vehicle', 'items', 'payments', 'createdBy']);

        $branchId = $invoice->branch_id;
        $settings = [
            'business_name' => Setting::get('business_name', $branchId) ?? 'Dundalk Used & New Tires',
            'business_address' => Setting::get('business_address', $branchId) ?? '',
            'business_phone' => Setting::get('business_phone', $branchId) ?? '',
            'business_email' => Setting::get('business_email', $branchId) ?? '',
        ];
        $currency = Setting::get('currency_symbol', $branchId) ?? '$';

        $pdf = Pdf::loadView('pdf.invoice', compact('invoice', 'settings', 'currency'))
            ->setPaper('letter', 'portrait');

        return $pdf->download("invoice-{$invoice->number}.pdf");
    }
}
