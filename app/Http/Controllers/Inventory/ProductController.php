<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductMovement;
use App\Models\Supplier;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;

class ProductController extends Controller implements HasMiddleware
{
    public function __construct(private InventoryService $inventory) {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_inventory|manage_inventory', only: ['index', 'show', 'search']),
            new Middleware('permission:manage_inventory', only: ['create', 'store', 'edit', 'update', 'destroy', 'adjust', 'toggleFeatured']),
        ];
    }

    public function index(Request $request)
    {
        $type = $request->get('type', 'tire');

        $products = Product::query()
            ->when($type === 'tire', fn ($q) => $q->where('type', 'tire'), fn ($q) => $q->whereIn('type', ['material', 'consumable', 'part']))
            ->with(['supplier:id,name', 'category:id,name,color'])
            ->search($request->search)
            ->when($request->low_stock === 'true', fn ($q) => $q->lowStock())
            ->when($request->supplier_id, fn ($q, $id) => $q->where('supplier_id', $id))
            ->when($request->category_id, fn ($q, $id) => $q->where('category_id', $id))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('Inventory/Products/Index', [
            'products' => $products,
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'categories' => ProductCategory::where(function ($q) use ($type) {
                if ($type === 'tire') {
                    $q->where('type', 'tire');
                } else {
                    $q->whereIn('type', ['material', 'consumable', 'part']);
                }
            })->orderBy('name')->get(['id', 'name', 'color']),
            'filters' => $request->only('search', 'type', 'supplier_id', 'category_id', 'low_stock'),
            'productType' => $type,
        ]);
    }

    public function create(Request $request)
    {
        $type = $request->get('type', 'tire');

        return Inertia::render('Inventory/Products/Form', [
            'productType' => $type,
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'categories' => ProductCategory::where(function ($q) use ($type) {
                if ($type === 'tire') {
                    $q->where('type', 'tire');
                } else {
                    $q->whereIn('type', ['material', 'consumable', 'part']);
                }
            })->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validate($request);

        // Auto-compute normalized tire size
        if ($data['type'] === 'tire' && isset($data['tire_width'])) {
            $data['tire_size_normalized'] = Product::normalizeTireSize(
                "{$data['tire_width']}{$data['tire_ratio']}R{$data['tire_diameter']}"
            );
        }

        $product = Product::create($data);

        // Log initial stock movement record (stock was already set by create())
        if ($product->stock_qty > 0) {
            ProductMovement::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'branch_id' => $product->branch_id,
                'type' => 'adjustment',
                'qty_before' => 0,
                'qty_change' => $product->stock_qty,
                'qty_after' => $product->stock_qty,
                'notes' => 'Initial stock entry',
            ]);
        }

        return redirect()->route('products.index', ['type' => $data['type']])
            ->with('success', 'Product created.');
    }

    public function show(Product $product)
    {
        $product->load(['supplier:id,name', 'category:id,name,color']);

        $movements = $product->movements()
            ->with('user:id,name')
            ->latest()
            ->paginate(20);

        return Inertia::render('Inventory/Products/Show', [
            'product' => $product,
            'movements' => $movements,
        ]);
    }

    public function edit(Product $product)
    {
        return Inertia::render('Inventory/Products/Form', [
            'product' => $product,
            'productType' => $product->type,
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'categories' => ProductCategory::where('type', $product->type)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validate($request);

        if ($data['type'] === 'tire' && isset($data['tire_width'])) {
            $data['tire_size_normalized'] = Product::normalizeTireSize(
                "{$data['tire_width']}{$data['tire_ratio']}R{$data['tire_diameter']}"
            );
        }

        $product->update($data);

        return redirect()->route('products.show', $product)
            ->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index', ['type' => $product->type])
            ->with('success', 'Product removed.');
    }

    public function adjust(Request $request, Product $product)
    {
        $data = $request->validate([
            'qty_change' => 'required|integer|not_in:0',
            'notes' => 'required|string|max:255',
        ]);

        if ($product->stock_qty + $data['qty_change'] < 0) {
            return back()->withErrors(['qty_change' => "El ajuste no puede dejar el stock en negativo. Stock actual: {$product->stock_qty}."]);
        }

        $this->inventory->adjust($product, $data['qty_change'], 'correction', $data['notes']);

        return back()->with('success', 'Stock adjusted.');
    }

    public function toggleFeatured(Product $product)
    {
        $product->update(['is_featured' => ! $product->is_featured]);

        return back()->with('success', $product->is_featured ? 'Marked as featured.' : 'Removed from featured.');
    }

    // JSON search endpoint used by POS/Work Orders
    public function search(Request $request)
    {
        $term = $request->get('q', '');

        $products = Product::search($term)
            ->where('is_active', true)
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->orderByRaw('stock_qty > 0 DESC') // in-stock first
            ->limit(15)
            ->get(['id', 'name', 'tire_brand', 'tire_line', 'tire_size_normalized',
                'tire_width', 'tire_ratio', 'tire_diameter', 'sale_price', 'cost_price', 'stock_qty', 'type']);

        return response()->json($products->map(fn ($p) => [
            'id' => $p->id,
            'name' => $p->type === 'tire'
                ? "{$p->tire_brand} {$p->tire_line} {$p->tire_size}"
                : $p->name,
            'size' => $p->tire_size,
            'price' => $p->sale_price,
            'cost_price' => (float) ($p->cost_price ?? 0),
            'stock' => $p->stock_qty,
            'type' => $p->type,
            'low_stock' => $p->low_stock,
        ]));
    }

    private function validate(Request $request): array
    {
        return $request->validate([
            'type' => 'required|in:tire,material,consumable,part',
            'name' => 'required|string|max:150',
            'sku' => 'nullable|string|max:50',
            'barcode' => 'nullable|string|max:50',
            'unit' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'category_id' => 'nullable|exists:product_categories,id',
            'tire_brand' => 'nullable|string|max:80',
            'tire_line' => 'nullable|string|max:80',
            'tire_width' => 'nullable|string|max:10',
            'tire_ratio' => 'nullable|string|max:10',
            'tire_diameter' => 'nullable|string|max:10',
            'tire_type' => 'nullable|string|max:40',
            'cost_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'stock_qty' => 'required|integer|min:0',
            'stock_min' => 'required|integer|min:0',
            'location' => 'nullable|string|max:40',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
    }
}
