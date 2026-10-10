<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Rent / Lease',        'type' => 'fixed',    'color' => '#2563eb'],
            ['name' => 'Utilities',            'type' => 'fixed',    'color' => '#7c3aed'],
            ['name' => 'Insurance',            'type' => 'fixed',    'color' => '#059669'],
            ['name' => 'Payroll',              'type' => 'fixed',    'color' => '#d97706'],
            ['name' => 'Inventory / Parts',    'type' => 'variable', 'color' => '#dc2626'],
            ['name' => 'Equipment',            'type' => 'variable', 'color' => '#0891b2'],
            ['name' => 'Marketing',            'type' => 'variable', 'color' => '#7c3aed'],
            ['name' => 'Repairs & Maintenance','type' => 'variable', 'color' => '#ea580c'],
            ['name' => 'Office Supplies',      'type' => 'variable', 'color' => '#64748b'],
            ['name' => 'Other',                'type' => 'variable', 'color' => '#94a3b8'],
        ];

        foreach ($categories as $cat) {
            ExpenseCategory::firstOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
