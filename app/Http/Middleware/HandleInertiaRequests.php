<?php

namespace App\Http\Middleware;

use App\Models\Product;
use App\Models\WebLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();

        $userData = null;
        if ($user) {
            // Cache user roles and permissions in the user's session in memory (0 DB queries per request)
            $session = $request->session();
            if (! $session->has("user_{$user->id}_perms")) {
                $session->put("user_{$user->id}_roles", $user->getRoleNames());
                $session->put("user_{$user->id}_perms", $user->getAllPermissions()->pluck('name')->toArray());
            }

            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $session->get("user_{$user->id}_roles"),
                'permissions' => $session->get("user_{$user->id}_perms"),
            ];
        }

        $lowStockCount = 0;
        $newLeadsCount = 0;

        if ($user) {
            // Cache badge notification counts for 60 seconds to eliminate expensive table scans on every Inertia request
            $lowStockCount = (int) Cache::remember('global_low_stock_count', 60, function () {
                return Product::where('is_active', true)->whereColumn('stock_qty', '<=', 'stock_min')->count();
            });

            $newLeadsCount = (int) Cache::remember('global_new_leads_count', 60, function () {
                return WebLead::where('status', 'new')->count();
            });
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $userData,
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
            'low_stock_count' => $lowStockCount,
            'new_leads_count' => $newLeadsCount,
        ]);
    }
}
