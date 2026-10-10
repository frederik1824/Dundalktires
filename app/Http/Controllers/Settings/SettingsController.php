<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;

class SettingsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('permission:manage_settings')];
    }

    private array $keys = [
        'business_name' => ['label' => 'Business Name',      'type' => 'string'],
        'business_address' => ['label' => 'Address',            'type' => 'string'],
        'business_phone' => ['label' => 'Phone',              'type' => 'string'],
        'business_email' => ['label' => 'Email',              'type' => 'string'],
        'tax_rate' => ['label' => 'Default Tax Rate %', 'type' => 'float'],
        'invoice_prefix' => ['label' => 'Invoice Prefix',     'type' => 'string'],
        'purchase_prefix' => ['label' => 'Purchase Prefix',    'type' => 'string'],
        'work_order_prefix' => ['label' => 'Work Order Prefix',  'type' => 'string'],
        'low_stock_alert' => ['label' => 'Low Stock Alert Qty', 'type' => 'integer'],
        'currency_symbol' => ['label' => 'Currency Symbol',          'type' => 'string'],
        'tire_disposal_cost' => ['label' => 'Costo por Disposición de Llanta (por unidad)', 'type' => 'float'],
    ];

    public function index()
    {
        $branchId = auth()->user()->branch_id;

        $settings = [];
        foreach ($this->keys as $key => $meta) {
            $settings[$key] = [
                'key' => $key,
                'label' => $meta['label'],
                'type' => $meta['type'],
                'value' => Setting::get($key, null, $branchId) ?? $this->defaults()[$key] ?? '',
            ];
        }

        return Inertia::render('Settings/Index', [
            'settings' => array_values($settings),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate(['settings' => 'required|array']);
        $branchId = auth()->user()->branch_id;

        foreach ($data['settings'] as $key => $value) {
            if (! array_key_exists($key, $this->keys)) {
                continue;
            }

            $type = $this->keys[$key]['type'];
            Setting::set($key, $value, $type, $branchId);
        }

        return back()->with('success', 'Settings saved.');
    }

    private function defaults(): array
    {
        return [
            'business_name' => 'Dundalk Used & New Tires',
            'currency_symbol' => '$',
            'invoice_prefix' => 'INV',
            'purchase_prefix' => 'PO',
            'work_order_prefix' => 'WO',
            'tax_rate' => '0',
            'low_stock_alert' => '5',
            'tire_disposal_cost' => '0',
        ];
    }
}
