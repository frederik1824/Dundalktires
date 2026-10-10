<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;

class TestimonialController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:manage_settings'),
        ];
    }

    public function index()
    {
        return Inertia::render('Landing/Testimonials/Index', [
            'testimonials' => Testimonial::orderBy('sort_order')->orderByDesc('reviewed_at')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Landing/Testimonials/Form');
    }

    public function store(Request $request)
    {
        Testimonial::create($this->validated($request));
        return redirect()->route('landing.testimonials.index')->with('success', 'Review added.');
    }

    public function edit(Testimonial $testimonial)
    {
        return Inertia::render('Landing/Testimonials/Form', ['testimonial' => $testimonial]);
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $testimonial->update($this->validated($request));
        return redirect()->route('landing.testimonials.index')->with('success', 'Review updated.');
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();
        return back()->with('success', 'Review deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'author_name' => 'required|string|max:100',
            'rating'      => 'required|integer|min:1|max:5',
            'body'        => 'required|string|max:1000',
            'reviewed_at' => 'nullable|date',
            'is_active'   => 'boolean',
            'sort_order'  => 'integer|min:0',
        ]);
    }
}
