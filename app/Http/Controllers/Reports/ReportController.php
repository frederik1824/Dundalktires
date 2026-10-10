<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\CashClose;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Setting;
use App\Models\WorkOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReportController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('permission:view_reports')];
    }

    public function index(Request $request)
    {
        $from     = $request->input('from', now()->startOfMonth()->toDateString());
        $to       = $request->input('to', now()->toDateString());
        $branchId = auth()->user()->branch_id;

        // --- Sales summary ---
        $salesSummary = Invoice::query()
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereIn('status', ['paid', 'partial'])
            ->whereBetween(DB::raw('DATE(paid_at)'), [$from, $to])
            ->selectRaw('COUNT(*) as count, SUM(amount_paid) as revenue, SUM(total) as billed')
            ->first();

        // --- Daily revenue (for the range) ---
        $dailyRevenue = Invoice::query()
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereIn('status', ['paid', 'partial'])
            ->whereBetween(DB::raw('DATE(paid_at)'), [$from, $to])
            ->selectRaw('DATE(paid_at) as date, SUM(amount_paid) as total, COUNT(*) as invoices')
            ->groupBy('date')->orderBy('date')->get();

        // --- Revenue by payment method ---
        $byMethod = Payment::query()
            ->join('invoices', 'invoices.id', '=', 'payments.invoice_id')
            ->when($branchId, fn ($q) => $q->where('invoices.branch_id', $branchId))
            ->whereBetween(DB::raw('DATE(payments.paid_at)'), [$from, $to])
            ->selectRaw('payments.method, SUM(payments.amount) as total, COUNT(*) as count')
            ->groupBy('payments.method')->orderByDesc('total')->get();

        // --- Top services ---
        $topServices = InvoiceItem::query()
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->when($branchId, fn ($q) => $q->where('invoices.branch_id', $branchId))
            ->whereIn('invoices.status', ['paid', 'partial'])
            ->whereBetween(DB::raw('DATE(invoices.paid_at)'), [$from, $to])
            ->where('invoice_items.type', 'service')
            ->selectRaw('invoice_items.name, SUM(invoice_items.total) as revenue, SUM(invoice_items.qty) as qty')
            ->groupBy('invoice_items.name')->orderByDesc('revenue')->limit(10)->get();

        // --- Top products ---
        $topProducts = InvoiceItem::query()
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->when($branchId, fn ($q) => $q->where('invoices.branch_id', $branchId))
            ->whereIn('invoices.status', ['paid', 'partial'])
            ->whereBetween(DB::raw('DATE(invoices.paid_at)'), [$from, $to])
            ->where('invoice_items.type', 'product')
            ->selectRaw('invoice_items.name, SUM(invoice_items.total) as revenue, SUM(invoice_items.qty) as qty')
            ->groupBy('invoice_items.name')->orderByDesc('revenue')->limit(10)->get();

        // --- Expenses summary ---
        $expenseSummary = Expense::query()
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereBetween('paid_at', [$from, $to])
            ->selectRaw('SUM(amount) as total, COUNT(*) as count')
            ->first();

        $expensesByCategory = Expense::query()
            ->with('category')
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereBetween('paid_at', [$from, $to])
            ->selectRaw('category_id, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('category_id')->orderByDesc('total')
            ->get()->map(fn ($e) => [
                'category' => $e->category?->name ?? 'Uncategorized',
                'color'    => $e->category?->color ?? '#94a3b8',
                'total'    => $e->total,
                'count'    => $e->count,
            ]);

        // --- Work orders ---
        $workOrderStats = WorkOrder::query()
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereBetween(DB::raw('DATE(created_at)'), [$from, $to])
            ->selectRaw('status, COUNT(*) as count, SUM(total) as value')
            ->groupBy('status')->get();

        // --- Inventory valuation ---
        $inventoryValue = Product::where('is_active', true)
            ->selectRaw('SUM(stock_qty * cost_price) as cost_value, SUM(stock_qty * sale_price) as sale_value, COUNT(*) as items')
            ->first();

        $revenue  = (float) ($salesSummary->revenue ?? 0);
        $expenses = (float) ($expenseSummary->total ?? 0);

        return Inertia::render('Reports/Index', [
            'filters'          => compact('from', 'to'),
            'salesSummary'     => $salesSummary,
            'dailyRevenue'     => $dailyRevenue,
            'byMethod'         => $byMethod,
            'topServices'      => $topServices,
            'topProducts'      => $topProducts,
            'expenseSummary'   => $expenseSummary,
            'expensesByCategory' => $expensesByCategory,
            'workOrderStats'   => $workOrderStats,
            'inventoryValue'   => $inventoryValue,
            'profit'           => round($revenue - $expenses, 2),
            'profitMargin'     => $revenue > 0 ? round(($revenue - $expenses) / $revenue * 100, 1) : 0,
        ]);
    }

    public function exportCsv(Request $request)
    {
        $from     = $request->input('from', now()->startOfMonth()->toDateString());
        $to       = $request->input('to', now()->toDateString());
        $type     = $request->input('type', 'sales');
        $branchId = auth()->user()->branch_id;

        $rows = match ($type) {
            'sales' => $this->salesRows($from, $to, $branchId),
            'expenses' => $this->expenseRows($from, $to, $branchId),
            default => [],
        };

        $filename = "report-{$type}-{$from}-{$to}.csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($rows) {
            $fh = fopen('php://output', 'w');
            foreach ($rows as $i => $row) {
                if ($i === 0) fputcsv($fh, array_keys($row));
                fputcsv($fh, array_values($row));
            }
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $from     = $request->input('from', now()->startOfMonth()->toDateString());
        $to       = $request->input('to', now()->toDateString());
        $branchId = auth()->user()->branch_id;

        $salesRows    = $this->salesRows($from, $to, $branchId);
        $expenseRows  = $this->expenseRows($from, $to, $branchId);

        $salesTotal   = array_sum(array_column($salesRows, 'total'));
        $expenseTotal = array_sum(array_column($expenseRows, 'amount'));

        $settings = [
            'business_name' => Setting::get('business_name', $branchId) ?? 'Dundalk Used & New Tires',
            'currency'      => Setting::get('currency_symbol', $branchId) ?? '$',
        ];

        $pdf = Pdf::loadView('pdf.report', compact('from', 'to', 'salesRows', 'expenseRows', 'salesTotal', 'expenseTotal', 'settings'))
            ->setPaper('letter', 'portrait');

        return $pdf->download("report-{$from}-{$to}.pdf");
    }

    public function arAging(Request $request)
    {
        $branchId = auth()->user()->branch_id;
        $today    = now()->toDateString();

        $invoices = Invoice::query()
            ->with('customer:id,name,phone,email')
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereIn('status', ['draft', 'partial'])
            ->whereRaw('total > amount_paid')
            ->orderBy('created_at')
            ->get(['id', 'number', 'customer_id', 'status', 'total', 'amount_paid', 'created_at']);

        $rows = $invoices->map(function ($inv) use ($today) {
            $days    = (int) now()->diffInDays($inv->created_at, false) * -1;
            $balance = round((float) $inv->total - (float) $inv->amount_paid, 2);

            return [
                'id'       => $inv->id,
                'number'   => $inv->number,
                'customer' => $inv->customer?->name ?? 'Walk-in',
                'phone'    => $inv->customer?->phone ?? '',
                'status'   => $inv->status,
                'total'    => (float) $inv->total,
                'paid'     => (float) $inv->amount_paid,
                'balance'  => $balance,
                'days'     => $days,
                'bucket'   => $days <= 0 ? 'current'
                    : ($days <= 30 ? '1-30'
                    : ($days <= 60 ? '31-60' : '60+')),
                'date'     => $inv->created_at->toDateString(),
            ];
        });

        $summary = [
            'current' => $rows->where('bucket', 'current')->sum('balance'),
            '1-30'    => $rows->where('bucket', '1-30')->sum('balance'),
            '31-60'   => $rows->where('bucket', '31-60')->sum('balance'),
            '60+'     => $rows->where('bucket', '60+')->sum('balance'),
            'total'   => $rows->sum('balance'),
        ];

        return Inertia::render('Reports/ARaging', [
            'rows'    => $rows->values(),
            'summary' => $summary,
        ]);
    }

    public function cashClose(Request $request)
    {
        $date     = $request->input('date', now()->toDateString());
        $branchId = auth()->user()->branch_id;

        $payments = Payment::query()
            ->join('invoices', 'invoices.id', '=', 'payments.invoice_id')
            ->when($branchId, fn ($q) => $q->where('invoices.branch_id', $branchId))
            ->whereDate('payments.paid_at', $date)
            ->select('payments.*', 'invoices.number as invoice_number', 'invoices.customer_id')
            ->with('invoice.customer:id,name')
            ->orderBy('payments.paid_at')
            ->get();

        $byMethod = $payments
            ->groupBy('method')
            ->map(fn ($group, $method) => [
                'method' => $method,
                'count'  => $group->count(),
                'total'  => round($group->sum('amount'), 2),
            ])
            ->values();

        $invoiceIds = $payments->pluck('invoice_id')->unique();
        $invoices = Invoice::whereIn('id', $invoiceIds)
            ->with('customer:id,name')
            ->get(['id', 'number', 'status', 'total', 'amount_paid', 'customer_id']);

        $cashClose = CashClose::where('branch_id', $branchId)
            ->where('date', $date)
            ->with('openedBy:id,name', 'closedBy:id,name')
            ->first();

        return Inertia::render('Reports/CashClose', [
            'date'      => $date,
            'payments'  => $payments->map(fn ($p) => [
                'id'             => $p->id,
                'invoice_number' => $p->invoice_number,
                'customer'       => $p->invoice?->customer?->name ?? 'Walk-in',
                'method'         => $p->method,
                'amount'         => (float) $p->amount,
                'reference'      => $p->reference,
                'paid_at'        => $p->paid_at,
            ])->values(),
            'byMethod'  => $byMethod,
            'total'     => round($payments->sum('amount'), 2),
            'count'     => $invoiceIds->count(),
            'cashClose' => $cashClose,
        ]);
    }

    private function salesRows(string $from, string $to, ?int $branchId): array
    {
        return Invoice::query()
            ->with(['customer', 'createdBy'])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereIn('status', ['paid', 'partial'])
            ->whereBetween(DB::raw('DATE(paid_at)'), [$from, $to])
            ->orderBy('paid_at')
            ->get()
            ->map(fn ($inv) => [
                'invoice'  => $inv->number,
                'date'     => $inv->paid_at?->toDateString() ?? $inv->created_at->toDateString(),
                'customer' => $inv->customer?->name ?? 'Walk-in',
                'status'   => $inv->status,
                'subtotal' => number_format((float) $inv->subtotal, 2, '.', ''),
                'tax'      => number_format((float) $inv->tax, 2, '.', ''),
                'discount' => number_format((float) $inv->discount, 2, '.', ''),
                'total'    => number_format((float) $inv->total, 2, '.', ''),
                'paid'     => number_format((float) $inv->amount_paid, 2, '.', ''),
                'issued_by'=> $inv->createdBy?->name ?? '',
            ])
            ->toArray();
    }

    private function expenseRows(string $from, string $to, ?int $branchId): array
    {
        return Expense::query()
            ->with(['category', 'createdBy'])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereBetween('paid_at', [$from, $to])
            ->orderBy('paid_at')
            ->get()
            ->map(fn ($e) => [
                'date'        => $e->paid_at?->toDateString() ?? '',
                'description' => $e->description,
                'category'    => $e->category?->name ?? 'Uncategorized',
                'vendor'      => $e->vendor ?? '',
                'method'      => $e->payment_method,
                'amount'      => number_format((float) $e->amount, 2, '.', ''),
                'created_by'  => $e->createdBy?->name ?? '',
            ])
            ->toArray();
    }
}
