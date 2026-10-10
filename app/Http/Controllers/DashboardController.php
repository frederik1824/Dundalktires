<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('permission:view_dashboard')];
    }

    public function index(Request $request)
    {
        $period = in_array($request->input('period'), ['day', 'week', 'month'])
            ? $request->input('period')
            : 'month';

        $branchId = auth()->user()->branch_id;

        $periodStart = match ($period) {
            'day' => now()->startOfDay(),
            'week' => now()->subDays(6)->startOfDay(),
            default => now()->startOfMonth(),
        };

        $invoiceQ = Invoice::query()
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereIn('status', ['paid', 'partial']);

        $salesPeriod = (clone $invoiceQ)->where('paid_at', '>=', $periodStart)->sum('amount_paid');
        $invoicesPeriod = (clone $invoiceQ)->where('created_at', '>=', $periodStart)->count();

        $expensesPeriod = Expense::query()
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->where('paid_at', '>=', $periodStart)->sum('amount');

        $openOrders = WorkOrder::where('status', 'pending')
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))->count();
        $inProgressOrders = WorkOrder::where('status', 'in_progress')
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))->count();

        $lowStockItems = Product::where('is_active', true)
            ->whereColumn('stock_qty', '<=', 'stock_min')
            ->orderBy('stock_qty')
            ->get(['id', 'name', 'sku', 'type', 'stock_qty', 'stock_min']);

        $pendingPurchases = PurchaseOrder::whereIn('status', ['draft', 'sent'])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))->count();

        return Inertia::render('Dashboard/Index', [
            'period' => $period,
            'stats' => [
                'sales_period' => number_format((float) $salesPeriod, 2),
                'expenses_period' => number_format((float) $expensesPeriod, 2),
                'profit_period' => number_format((float) ($salesPeriod - $expensesPeriod), 2),
                'invoices_period' => $invoicesPeriod,
                'open_orders' => $openOrders,
                'in_progress_orders' => $inProgressOrders,
                'low_stock' => $lowStockItems->count(),
                'pending_purchases' => $pendingPurchases,
            ],
            'chart_revenue' => Inertia::defer(function () use ($period, $branchId) {
                if ($period === 'day') {
                    return Invoice::select(
                        DB::raw('HOUR(paid_at) as period_key'),
                        DB::raw('SUM(amount_paid) as total')
                    )
                        ->whereIn('status', ['paid', 'partial'])
                        ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                        ->whereDate('paid_at', now()->toDateString())
                        ->groupBy('period_key')
                        ->orderBy('period_key')
                        ->get();
                }

                $days = $period === 'week' ? 6 : 29;

                return Invoice::select(
                    DB::raw('DATE(paid_at) as period_key'),
                    DB::raw('SUM(amount_paid) as total')
                )
                    ->whereIn('status', ['paid', 'partial'])
                    ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                    ->where('paid_at', '>=', now()->subDays($days)->startOfDay())
                    ->groupBy('period_key')
                    ->orderBy('period_key')
                    ->get();
            }),
            'recent_orders' => Inertia::defer(function () use ($branchId) {
                return WorkOrder::with(['customer:id,name', 'vehicle:id,make,model,year,plate'])
                    ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                    ->whereNotIn('status', ['cancelled'])
                    ->orderByDesc('created_at')
                    ->limit(5)
                    ->get(['id', 'number', 'customer_id', 'vehicle_id', 'status', 'total', 'created_at']);
            }),
            'low_stock_items' => $lowStockItems,
        ]);
    }
}
