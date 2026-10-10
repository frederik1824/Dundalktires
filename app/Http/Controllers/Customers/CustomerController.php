<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;

class CustomerController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_customers|manage_customers', only: ['index', 'show', 'search']),
            new Middleware('permission:manage_customers', only: ['create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $customers = Customer::query()
            ->withCount('vehicles')
            ->search($request->input('q'))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Customers/Index', [
            'customers' => $customers,
            'filters'   => $request->only('q'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Customers/Form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:100',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:100',
            'address' => 'nullable|string|max:200',
            'notes'   => 'nullable|string|max:1000',
        ]);

        $customer = Customer::create($data);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['vehicles' => function ($q) {
            $q->orderByDesc('year');
        }]);

        $workOrders = WorkOrder::where('customer_id', $customer->id)
            ->with('vehicle')
            ->orderByDesc('created_at')
            ->get(['id', 'number', 'status', 'total', 'created_at', 'vehicle_id']);

        $invoices = Invoice::where('customer_id', $customer->id)
            ->orderByDesc('created_at')
            ->get(['id', 'number', 'status', 'total', 'amount_paid', 'created_at']);

        return Inertia::render('Customers/Show', [
            'customer'   => $customer,
            'workOrders' => $workOrders,
            'invoices'   => $invoices,
        ]);
    }

    public function edit(Customer $customer)
    {
        return Inertia::render('Customers/Form', [
            'customer' => $customer,
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:100',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:100',
            'address' => 'nullable|string|max:200',
            'notes'   => 'nullable|string|max:1000',
        ]);

        $customer->update($data);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Customer updated.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer removed.');
    }

    // Quick search for autocomplete (POS, work orders)
    public function search(Request $request)
    {
        $customers = Customer::query()
            ->search($request->input('q'))
            ->with(['vehicles' => fn ($q) => $q->select('id', 'customer_id', 'make', 'model', 'year', 'plate', 'type', 'tire_size')])
            ->limit(10)
            ->get(['id', 'name', 'phone', 'email']);

        return response()->json($customers);
    }
}
