<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Roles
        $admin     = Role::firstOrCreate(['name' => 'admin']);
        $owner     = Role::firstOrCreate(['name' => 'owner']);
        $secretary = Role::firstOrCreate(['name' => 'secretary']);
        $mechanic  = Role::firstOrCreate(['name' => 'mechanic']);

        // Permissions
        $permissions = [
            'view_dashboard', 'view_reports',
            'manage_inventory', 'view_inventory',
            'manage_purchases', 'view_purchases',
            'manage_sales', 'view_sales',
            'manage_customers', 'view_customers',
            'manage_services', 'view_services',
            'manage_work_orders', 'view_work_orders',
            'manage_expenses', 'view_expenses',
            'manage_employees', 'view_employees',
            'manage_settings', 'manage_users',
            'view_audit_log',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Admin gets all
        $admin->syncPermissions(Permission::all());

        // Owner: view everything
        $owner->syncPermissions([
            'view_dashboard', 'view_reports', 'view_inventory', 'view_purchases',
            'view_sales', 'view_customers', 'view_services', 'view_work_orders',
            'view_expenses', 'view_employees', 'view_audit_log',
        ]);

        // Secretary: operational
        $secretary->syncPermissions([
            'view_dashboard', 'manage_inventory', 'view_inventory',
            'manage_purchases', 'manage_sales', 'manage_customers',
            'view_services', 'manage_work_orders', 'view_work_orders',
            'manage_expenses', 'view_expenses', 'view_reports',
        ]);

        // Mechanic: work orders + services
        $mechanic->syncPermissions([
            'view_dashboard', 'view_work_orders', 'manage_work_orders', 'view_services',
        ]);

        // Default branch
        $branch = Branch::firstOrCreate(
            ['name' => 'Dundalk Used & New Tires'],
            ['address' => 'Dundalk, MD', 'phone' => '', 'email' => '', 'timezone' => 'America/New_York', 'currency' => 'USD']
        );

        // Default settings
        Setting::set('tax_rate', '0', 'decimal');
        Setting::set('tax_enabled', 'false', 'boolean');
        Setting::set('tax_label', 'Sales Tax', 'string');
        Setting::set('allow_manual_discounts', 'true', 'boolean');
        Setting::set('currency_symbol', '$', 'string');
        Setting::set('invoice_prefix', 'INV', 'string');
        Setting::set('work_order_prefix', 'WO', 'string');
        Setting::set('purchase_prefix', 'PO', 'string');

        // Admin user
        if (! User::where('email', 'admin@dundalk.com')->exists()) {
            $user = User::create([
                'name' => 'Administrador',
                'email' => 'admin@dundalk.com',
                'password' => Hash::make('Dundalk2026!'),
                'branch_id' => $branch->id,
                'is_active' => true,
            ]);
            $user->assignRole('admin');
        }
    }
}
