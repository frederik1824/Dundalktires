<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServicePriceRule;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ServiceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_services|manage_services', only: ['index', 'search']),
            new Middleware('permission:manage_services', only: ['create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $services = Service::query()
            ->with('category')
            ->search($request->input('q'))
            ->when($request->input('category'), fn ($q, $v) => $q->where('category_id', $v))
            ->when($request->input('active') !== null, fn ($q) => $q->where('is_active', $request->boolean('active')))
            ->orderBy('sort_order')->orderBy('name')
            ->paginate(25)->withQueryString();

        return Inertia::render('Services/Index', [
            'services'   => $services,
            'categories' => ServiceCategory::orderBy('sort_order')->get(),
            'filters'    => $request->only('q', 'category', 'active'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Services/Form', [
            'categories'  => ServiceCategory::orderBy('sort_order')->get(),
            'vehicleTypes' => Vehicle::vehicleTypes(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'        => 'nullable|exists:service_categories,id',
            'name'               => 'required|string|max:100',
            'description'        => 'nullable|string|max:500',
            'pricing_type'       => 'required|in:fixed,by_vehicle_type,variable',
            'base_price'         => 'required|numeric|min:0',
            'cost_price'         => 'nullable|numeric|min:0',
            'estimated_minutes'  => 'nullable|integer|min:1|max:480',
            'is_active'          => 'boolean',
            'sort_order'         => 'integer|min:0',
            'price_rules'        => 'array',
            'price_rules.*.vehicle_type' => 'required|string',
            'price_rules.*.price'        => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($data) {
            $rules = $data['price_rules'] ?? [];
            unset($data['price_rules']);

            $service = Service::create($data);

            if ($data['pricing_type'] === 'by_vehicle_type' && $rules) {
                foreach ($rules as $rule) {
                    ServicePriceRule::create([
                        'service_id'   => $service->id,
                        'vehicle_type' => $rule['vehicle_type'],
                        'price'        => $rule['price'],
                    ]);
                }
            }
        });

        return redirect()->route('services.index')
            ->with('success', 'Service created successfully.');
    }

    public function edit(Service $service)
    {
        $service->load('priceRules');

        return Inertia::render('Services/Form', [
            'service'      => $service,
            'categories'   => ServiceCategory::orderBy('sort_order')->get(),
            'vehicleTypes' => Vehicle::vehicleTypes(),
        ]);
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'category_id'        => 'nullable|exists:service_categories,id',
            'name'               => 'required|string|max:100',
            'description'        => 'nullable|string|max:500',
            'pricing_type'       => 'required|in:fixed,by_vehicle_type,variable',
            'base_price'         => 'required|numeric|min:0',
            'cost_price'         => 'nullable|numeric|min:0',
            'estimated_minutes'  => 'nullable|integer|min:1|max:480',
            'is_active'          => 'boolean',
            'sort_order'         => 'integer|min:0',
            'price_rules'        => 'array',
            'price_rules.*.vehicle_type' => 'required|string',
            'price_rules.*.price'        => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($data, $service) {
            $rules = $data['price_rules'] ?? [];
            unset($data['price_rules']);

            $service->update($data);

            $service->priceRules()->delete();
            if ($data['pricing_type'] === 'by_vehicle_type' && $rules) {
                foreach ($rules as $rule) {
                    ServicePriceRule::create([
                        'service_id'   => $service->id,
                        'vehicle_type' => $rule['vehicle_type'],
                        'price'        => $rule['price'],
                    ]);
                }
            }
        });

        return redirect()->route('services.index')
            ->with('success', 'Service updated.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('services.index')
            ->with('success', 'Service removed.');
    }

    // JSON endpoint for autocomplete
    public function search(Request $request)
    {
        $vehicleType = $request->input('vehicle_type');

        $services = Service::active()
            ->search($request->input('q'))
            ->with('priceRules')
            ->limit(15)
            ->get(['id', 'name', 'pricing_type', 'base_price', 'cost_price', 'estimated_minutes', 'category_id'])
            ->map(function ($s) use ($vehicleType) {
                return [
                    'id'           => $s->id,
                    'name'         => $s->name,
                    'price'        => $s->priceForVehicle($vehicleType),
                    'cost_price'   => (float) ($s->cost_price ?? 0),
                    'pricing_type' => $s->pricing_type,
                ];
            });

        return response()->json($services);
    }
}
