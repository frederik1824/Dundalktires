<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;

class SupplierController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_inventory|manage_inventory', only: ['index']),
            new Middleware('permission:manage_inventory', only: ['create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $suppliers = Supplier::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('contact_name', 'like', "%{$s}%")
                ->orWhere('phone', 'like', "%{$s}%"))
            ->when($request->active === 'true', fn ($q) => $q->where('is_active', true))
            ->withCount('products')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Inventory/Suppliers/Index', [
            'suppliers' => $suppliers,
            'filters'   => $request->only('search', 'active'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Inventory/Suppliers/Form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:120',
            'contact_name' => 'nullable|string|max:100',
            'phone'        => 'nullable|string|max:30',
            'email'        => 'nullable|email|max:120',
            'address'      => 'nullable|string|max:255',
            'notes'        => 'nullable|string',
            'is_active'    => 'boolean',
        ]);

        Supplier::create($data);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier created.');
    }

    public function edit(Supplier $supplier)
    {
        return Inertia::render('Inventory/Suppliers/Form', ['supplier' => $supplier]);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:120',
            'contact_name' => 'nullable|string|max:100',
            'phone'        => 'nullable|string|max:30',
            'email'        => 'nullable|email|max:120',
            'address'      => 'nullable|string|max:255',
            'notes'        => 'nullable|string',
            'is_active'    => 'boolean',
        ]);

        $supplier->update($data);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier updated.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier removed.');
    }
}
