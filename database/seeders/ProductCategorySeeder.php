<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $tireCategories = [
            ['name' => 'All-Season',    'color' => '#2563eb'],
            ['name' => 'Summer',        'color' => '#f59e0b'],
            ['name' => 'Winter',        'color' => '#06b6d4'],
            ['name' => 'All-Terrain',   'color' => '#16a34a'],
            ['name' => 'Performance',   'color' => '#dc2626'],
            ['name' => 'Used Tires',    'color' => '#6b7280'],
        ];

        $materialCategories = [
            ['name' => 'Valves & TPMS',    'color' => '#8b5cf6'],
            ['name' => 'Wheel Weights',    'color' => '#0ea5e9'],
            ['name' => 'Lubricants',       'color' => '#f97316'],
            ['name' => 'Cleaning Products','color' => '#14b8a6'],
            ['name' => 'Mounting Supplies','color' => '#a78bfa'],
            ['name' => 'Other',            'color' => '#94a3b8'],
        ];

        foreach ($tireCategories as $cat) {
            ProductCategory::firstOrCreate(
                ['name' => $cat['name'], 'type' => 'tire'],
                ['color' => $cat['color']]
            );
        }

        foreach ($materialCategories as $cat) {
            ProductCategory::firstOrCreate(
                ['name' => $cat['name'], 'type' => 'material'],
                ['color' => $cat['color']]
            );
        }
    }
}
