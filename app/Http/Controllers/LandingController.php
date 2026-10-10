<?php

namespace App\Http\Controllers;

use App\Models\LandingPromotion;
use App\Models\Product;
use App\Models\Testimonial;
use App\Models\WebLead;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $tires = Product::tire()
            ->where('is_active', true)
            ->where('stock_qty', '>', 0)
            ->orderByDesc('is_featured')
            ->orderBy('tire_brand')
            ->select([
                'id', 'name', 'tire_brand', 'tire_line',
                'tire_width', 'tire_ratio', 'tire_diameter',
                'tire_type', 'sale_price', 'stock_qty', 'is_featured',
            ])
            ->limit(9)
            ->get();

        $base = Product::tire()->where('is_active', true)->where('stock_qty', '>', 0);

        $filters = [
            'widths'    => (clone $base)->whereNotNull('tire_width')->distinct()
                ->orderByRaw('CAST(tire_width AS UNSIGNED)')->pluck('tire_width'),
            'ratios'    => (clone $base)->whereNotNull('tire_ratio')->distinct()
                ->orderByRaw('CAST(tire_ratio AS UNSIGNED)')->pluck('tire_ratio'),
            'diameters' => (clone $base)->whereNotNull('tire_diameter')->distinct()
                ->orderByRaw('CAST(tire_diameter AS UNSIGNED)')->pluck('tire_diameter'),
            'brands'    => (clone $base)->whereNotNull('tire_brand')->distinct()
                ->orderBy('tire_brand')->pluck('tire_brand'),
            'total'     => (clone $base)->count(),
        ];

        $promotions = LandingPromotion::active()
            ->orderBy('sort_order')
            ->get();

        $testimonials = Testimonial::active()
            ->orderBy('sort_order')
            ->orderByDesc('reviewed_at')
            ->get();

        return view('landing', compact('tires', 'filters', 'promotions', 'testimonials'));
    }

    public function quote(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:120',
            'phone'        => 'required|string|max:30',
            'service_type' => 'required|string|max:60',
            'vehicle'      => 'nullable|string|max:120',
            'tire_size'    => 'nullable|string|max:30',
            'notes'        => 'nullable|string|max:1000',
        ]);

        WebLead::create($data);

        return response()->json(['message' => 'ok']);
    }
}
