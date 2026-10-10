<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;

class VehicleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_customers|manage_customers', only: ['show']),
            new Middleware('permission:manage_customers', only: ['create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function show(Customer $customer, Vehicle $vehicle)
    {
        abort_unless($vehicle->customer_id === $customer->id, 404);

        $workOrders = WorkOrder::where('vehicle_id', $vehicle->id)
            ->with(['services', 'products', 'invoices', 'assignedTo'])
            ->orderByDesc('created_at')
            ->get();

        // Aggregate service/product history from work order line items
        $serviceHistory = $workOrders->flatMap(fn ($wo) =>
            $wo->services->map(fn ($s) => [
                'date'      => $wo->created_at->toDateString(),
                'wo_number' => $wo->number,
                'wo_id'     => $wo->id,
                'type'      => 'service',
                'name'      => $s->name,
                'qty'       => $s->qty,
                'price'     => (float) $s->unit_price,
                'status'    => $wo->status,
            ])
        )->concat(
            $workOrders->flatMap(fn ($wo) =>
                $wo->products->map(fn ($p) => [
                    'date'      => $wo->created_at->toDateString(),
                    'wo_number' => $wo->number,
                    'wo_id'     => $wo->id,
                    'type'      => 'product',
                    'name'      => $p->name,
                    'qty'       => $p->qty,
                    'price'     => (float) $p->unit_price,
                    'status'    => $wo->status,
                ])
            )
        )->sortByDesc('date')->values();

        return Inertia::render('Vehicles/Show', [
            'customer'       => $customer->only('id', 'name'),
            'vehicle'        => $vehicle,
            'workOrders'     => $workOrders->map(fn ($wo) => [
                'id'          => $wo->id,
                'number'      => $wo->number,
                'status'      => $wo->status,
                'total'       => $wo->total,
                'created_at'  => $wo->created_at,
                'invoice_id'  => $wo->invoices->first()?->id,
                'invoice_num' => $wo->invoices->first()?->number,
                'assigned_to' => $wo->assignedTo?->name,
                'services'    => $wo->services->pluck('name'),
                'products'    => $wo->products->pluck('name'),
            ]),
            'serviceHistory' => $serviceHistory,
        ]);
    }

    public function create(Customer $customer)
    {
        return Inertia::render('Vehicles/Form', [
            'customer'     => $customer,
            'vehicleTypes' => Vehicle::vehicleTypes(),
        
            'catalog' => collect(\App\Helpers\VehicleCatalog::get())
                ->concat(\App\Models\Vehicle::select('make', 'model', 'year', 'type', 'tire_size')->distinct()->get()->toArray())
                ->unique(function ($item) { return $item['make'] . $item['model'] . $item['year']; })
                ->sortBy('make')
                ->values()
                ->toArray(),
        ]);
    }

    public function store(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'make'      => 'required|string|max:50',
            'model'     => 'required|string|max:50',
            'year'      => 'required|integer|min:1950|max:' . (date('Y') + 2),
            'color'     => 'nullable|string|max:30',
            'plate'     => 'nullable|string|max:20',
            'vin'       => 'nullable|string|size:17',
            'type'      => 'required|in:sedan,suv,pickup,van,truck,other',
            'tire_size' => 'nullable|string|max:20',
            'mileage'   => 'nullable|integer|min:0',
            'notes'     => 'nullable|string|max:1000',
        ]);

        $customer->vehicles()->create($data);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Vehicle added.');
    }

    public function edit(Customer $customer, Vehicle $vehicle)
    {
        abort_unless($vehicle->customer_id === $customer->id, 404);

        return Inertia::render('Vehicles/Form', [
            'customer'     => $customer,
            'vehicle'      => $vehicle,
            'vehicleTypes' => Vehicle::vehicleTypes(),
        
            'catalog' => collect(\App\Helpers\VehicleCatalog::get())
                ->concat(\App\Models\Vehicle::select('make', 'model', 'year', 'type', 'tire_size')->distinct()->get()->toArray())
                ->unique(function ($item) { return $item['make'] . $item['model'] . $item['year']; })
                ->sortBy('make')
                ->values()
                ->toArray(),
        ]);
    }

    public function update(Request $request, Customer $customer, Vehicle $vehicle)
    {
        abort_unless($vehicle->customer_id === $customer->id, 404);

        $data = $request->validate([
            'make'      => 'required|string|max:50',
            'model'     => 'required|string|max:50',
            'year'      => 'required|integer|min:1950|max:' . (date('Y') + 2),
            'color'     => 'nullable|string|max:30',
            'plate'     => 'nullable|string|max:20',
            'vin'       => 'nullable|string|size:17',
            'type'      => 'required|in:sedan,suv,pickup,van,truck,other',
            'tire_size' => 'nullable|string|max:20',
            'mileage'   => 'nullable|integer|min:0',
            'notes'     => 'nullable|string|max:1000',
        ]);

        $vehicle->update($data);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Vehicle updated.');
    }

    public function destroy(Customer $customer, Vehicle $vehicle)
    {
        abort_unless($vehicle->customer_id === $customer->id, 404);
        $vehicle->delete();

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Vehicle removed.');
    }
}
