<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\LandingPromotion;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;

class PromotionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:manage_settings'),
        ];
    }

    public function index()
    {
        return Inertia::render('Landing/Promotions/Index', [
            'promotions' => LandingPromotion::orderBy('sort_order')->orderByDesc('created_at')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Landing/Promotions/Form');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        LandingPromotion::create($data);
        return redirect()->route('landing.promotions.index')->with('success', 'Promotion created.');
    }

    public function edit(LandingPromotion $promotion)
    {
        return Inertia::render('Landing/Promotions/Form', ['promotion' => $promotion]);
    }

    public function update(Request $request, LandingPromotion $promotion)
    {
        $data = $this->validated($request);
        $promotion->update($data);
        return redirect()->route('landing.promotions.index')->with('success', 'Promotion updated.');
    }

    public function destroy(LandingPromotion $promotion)
    {
        $promotion->delete();
        return back()->with('success', 'Promotion deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title'       => 'required|string|max:120',
            'description' => 'nullable|string|max:500',
            'badge_text'  => 'nullable|string|max:60',
            'cta_text'    => 'nullable|string|max:60',
            'is_active'   => 'boolean',
            'starts_at'   => 'nullable|date',
            'ends_at'     => 'nullable|date|after_or_equal:starts_at',
            'sort_order'  => 'integer|min:0',
        ]);
    }
}
