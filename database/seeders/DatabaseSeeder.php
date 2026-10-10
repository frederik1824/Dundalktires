<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            ProductCategorySeeder::class,
            ServiceCategorySeeder::class,
            ExpenseCategorySeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}
