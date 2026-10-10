<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\WebLead;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;

class WebLeadController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_customers|manage_customers'),
        ];
    }

    public function index(Request $request)
    {
        $leads = WebLead::query()
            ->when($request->input('q'), fn ($q, $v) =>
                $q->where(fn ($w) => $w->where('name', 'like', "%$v%")
                    ->orWhere('phone', 'like', "%$v%")
                    ->orWhere('vehicle', 'like', "%$v%"))
            )
            ->when($request->input('status'), fn ($q, $v) => $q->where('status', $v))
            ->when($request->input('from'),   fn ($q, $v) => $q->where('created_at', '>=', $v))
            ->when($request->input('to'),     fn ($q, $v) => $q->where('created_at', '<=', $v . ' 23:59:59'))
            ->orderByDesc('created_at')
            ->paginate(25)->withQueryString();

        return Inertia::render('Landing/WebLeads/Index', [
            'leads'   => $leads,
            'filters' => $request->only('q', 'status', 'from', 'to'),
            'counts'  => [
                'new'        => WebLead::where('status', 'new')->count(),
                'contacted'  => WebLead::where('status', 'contacted')->count(),
                'closed'     => WebLead::where('status', 'closed')->count(),
            ],
        ]);
    }

    public function update(Request $request, WebLead $webLead)
    {
        $data = $request->validate([
            'status'      => 'required|in:new,contacted,closed',
            'staff_notes' => 'nullable|string|max:2000',
        ]);

        if ($data['status'] === 'contacted' && ! $webLead->contacted_at) {
            $data['contacted_at'] = now();
        }

        $webLead->update($data);

        return back()->with('success', 'Lead updated.');
    }

    public function destroy(WebLead $webLead)
    {
        $webLead->delete();
        return back()->with('success', 'Lead deleted.');
    }
}
