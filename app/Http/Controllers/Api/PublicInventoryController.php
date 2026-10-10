<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicInventoryController extends Controller
{
    // GET /api/public/tires
    public function tires(Request $request): JsonResponse
    {
        $query = Product::tire()
            ->where('is_active', true)
            ->where('stock_qty', '>', 0);

        if ($request->filled('brand')) {
            $query->where('tire_brand', $request->brand);
        }

        if ($request->filled('width')) {
            $query->where('tire_width', $request->width);
        }

        if ($request->filled('ratio')) {
            $query->where('tire_ratio', $request->ratio);
        }

        if ($request->filled('diameter')) {
            $query->where('tire_diameter', $request->diameter);
        }

        if ($request->filled('tire_type')) {
            $query->where('tire_type', $request->tire_type);
        }

        if ($request->filled('size')) {
            $parsed = Product::parseTireSize($request->size);
            if ($parsed) {
                $query->where('tire_width', $parsed['width'])
                      ->where('tire_ratio', $parsed['ratio'])
                      ->where('tire_diameter', $parsed['diameter']);
            }
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $tires = $query
            ->orderByDesc('is_featured')
            ->orderBy('tire_brand')
            ->orderBy('tire_width')
            ->select([
                'id', 'name', 'tire_brand', 'tire_line',
                'tire_width', 'tire_ratio', 'tire_diameter',
                'tire_type', 'tire_size_normalized',
                'sale_price', 'stock_qty', 'notes', 'is_featured',
            ])
            ->paginate($request->integer('per_page', 24));

        return response()->json([
            'data'  => $tires->items(),
            'meta'  => [
                'total'        => $tires->total(),
                'per_page'     => $tires->perPage(),
                'current_page' => $tires->currentPage(),
                'last_page'    => $tires->lastPage(),
            ],
        ]);
    }

    // GET /api/public/tires/filters
    public function filters(): JsonResponse
    {
        $base = Product::tire()->where('is_active', true)->where('stock_qty', '>', 0);

        $brands = (clone $base)
            ->whereNotNull('tire_brand')
            ->distinct()
            ->orderBy('tire_brand')
            ->pluck('tire_brand');

        $diameters = (clone $base)
            ->whereNotNull('tire_diameter')
            ->distinct()
            ->orderByRaw('CAST(tire_diameter AS UNSIGNED)')
            ->pluck('tire_diameter');

        $widths = (clone $base)
            ->whereNotNull('tire_width')
            ->distinct()
            ->orderByRaw('CAST(tire_width AS UNSIGNED)')
            ->pluck('tire_width');

        $ratios = (clone $base)
            ->whereNotNull('tire_ratio')
            ->distinct()
            ->orderByRaw('CAST(tire_ratio AS UNSIGNED)')
            ->pluck('tire_ratio');

        $types = (clone $base)
            ->whereNotNull('tire_type')
            ->distinct()
            ->orderBy('tire_type')
            ->pluck('tire_type');

        $total = (clone $base)->count();

        return response()->json(compact('brands', 'diameters', 'widths', 'ratios', 'types', 'total'));
    }

    // GET /api/public/vehicles/makes
    public function vehicleMakes(): JsonResponse
    {
        $makes = Vehicle::whereNotNull('make')
            ->distinct()
            ->orderBy('make')
            ->pluck('make');

        return response()->json($makes);
    }

    // GET /api/public/vehicles/models?make=Toyota
    public function vehicleModels(Request $request): JsonResponse
    {
        $models = Vehicle::where('make', $request->make)
            ->whereNotNull('model')
            ->distinct()
            ->orderBy('model')
            ->pluck('model');

        return response()->json($models);
    }

    // GET /api/public/vehicles/years?make=Toyota&model=Camry
    public function vehicleYears(Request $request): JsonResponse
    {
        $years = Vehicle::where('make', $request->make)
            ->where('model', $request->model)
            ->whereNotNull('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return response()->json($years);
    }

    // GET /api/public/vehicles/tire-size?make=Toyota&model=Camry&year=2020
    // Returns the most common tire size recorded for this vehicle
    public function vehicleTireSize(Request $request): JsonResponse
    {
        $size = Vehicle::where('make', $request->make)
            ->where('model', $request->model)
            ->when($request->filled('year'), fn ($q) => $q->where('year', $request->year))
            ->whereNotNull('tire_size')
            ->where('tire_size', '!=', '')
            ->selectRaw('tire_size, COUNT(*) as cnt')
            ->groupBy('tire_size')
            ->orderByDesc('cnt')
            ->value('tire_size');

        return response()->json(['tire_size' => $size]);
    }
}
