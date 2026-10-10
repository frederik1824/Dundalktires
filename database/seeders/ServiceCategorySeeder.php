<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Tire Services',       'color' => '#2563eb', 'sort_order' => 1],
            ['name' => 'Wheel Services',       'color' => '#7c3aed', 'sort_order' => 2],
            ['name' => 'Balancing',            'color' => '#059669', 'sort_order' => 3],
            ['name' => 'Alignment',            'color' => '#d97706', 'sort_order' => 4],
            ['name' => 'TPMS Services',        'color' => '#dc2626', 'sort_order' => 5],
            ['name' => 'Other Services',       'color' => '#64748b', 'sort_order' => 6],
        ];

        foreach ($categories as $cat) {
            ServiceCategory::firstOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
