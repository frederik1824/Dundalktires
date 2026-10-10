<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\{
    Product, ProductCategory, Service, ServiceCategory,
    ServicePriceRule, Setting, Supplier, User
};

/**
 * Siembra únicamente datos esenciales para producción:
 *   - Roles y permisos + usuario admin
 *   - Configuración del negocio
 *   - Proveedores de referencia
 *   - Catálogo de gomas y materiales (stock en 0)
 *   - Catálogo de servicios con sus price rules
 *
 * NO incluye clientes, vehículos, órdenes, facturas ni gastos de prueba.
 */
class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            ProductCategorySeeder::class,
            ServiceCategorySeeder::class,
            ExpenseCategorySeeder::class,
        ]);

        $this->seedSettings();

        $suppliers  = $this->seedSuppliers();
        $categories = ProductCategory::all()->keyBy('name');

        $this->seedProducts($suppliers, $categories);
        $this->seedServices();
    }

    // ── Settings ─────────────────────────────────────────────────────────────

    private function seedSettings(): void
    {
        $settings = [
            ['key' => 'business_name',        'value' => 'Dundalk Used & New Tires',          'type' => 'string'],
            ['key' => 'business_address',     'value' => '7888 Dundalk Ave, Dundalk, MD 21222','type' => 'string'],
            ['key' => 'business_phone',       'value' => '667-727-3115',                       'type' => 'string'],
            ['key' => 'business_email',       'value' => 'info@dundalktiresmd.com',            'type' => 'string'],
            ['key' => 'invoice_prefix',       'value' => 'INV',                                'type' => 'string'],
            ['key' => 'purchase_prefix',      'value' => 'PO',                                 'type' => 'string'],
            ['key' => 'tax_rate',             'value' => '0',                                  'type' => 'float'],
            ['key' => 'tire_disposal_cost',   'value' => '4.00',                               'type' => 'float'],
            ['key' => 'currency',             'value' => 'USD',                                'type' => 'string'],
            ['key' => 'timezone',             'value' => 'America/New_York',                   'type' => 'string'],
        ];

        foreach ($settings as $s) {
            Setting::firstOrCreate(['key' => $s['key']], $s);
        }
    }

    // ── Suppliers ────────────────────────────────────────────────────────────

    private function seedSuppliers(): \Illuminate\Support\Collection
    {
        $data = [
            ['name' => 'American Tire Distributors',  'contact_name' => 'Tom Bradley',  'phone' => '(704) 992-2000', 'email' => 'tbradley@atd.com',       'address' => '12200 Herbert Wayne Ct, Huntersville, NC'],
            ['name' => 'Priority Tire Wholesale',     'contact_name' => 'Lisa Chen',    'phone' => '(855) 977-7473', 'email' => 'lchen@prioritytire.com',  'address' => '800 Commerce Blvd, Carlstadt, NJ'],
            ['name' => 'NTW National Tire Wholesale', 'contact_name' => 'Bob Garrett',  'phone' => '(866) 689-6089', 'email' => 'bgarrett@ntw.com',        'address' => '2605 Enterprise Rd E, Clearwater, FL'],
            ['name' => 'Harbor Freight Tools',        'contact_name' => 'Mike Saenz',   'phone' => '(800) 444-3353', 'email' => 'mike@harborfreight.com',  'address' => '26541 Agoura Rd, Calabasas, CA'],
            ['name' => "O'Reilly Auto Parts",         'contact_name' => 'Pam Richards', 'phone' => '(417) 862-6708', 'email' => 'prichards@oreilly.com',   'address' => '233 S Patterson Ave, Springfield, MO'],
        ];

        foreach ($data as $d) {
            Supplier::firstOrCreate(['email' => $d['email']], array_merge($d, ['is_active' => true]));
        }

        return Supplier::all();
    }

    // ── Products (stock_qty = 0) ──────────────────────────────────────────────

    private function seedProducts(\Illuminate\Support\Collection $suppliers, \Illuminate\Support\Collection $categories): void
    {
        $atd = $suppliers->firstWhere('name', 'American Tire Distributors');
        $pri = $suppliers->firstWhere('name', 'Priority Tire Wholesale');
        $ntw = $suppliers->firstWhere('name', 'NTW National Tire Wholesale');
        $hf  = $suppliers->firstWhere('name', 'Harbor Freight Tools');
        $ore = $suppliers->firstWhere('name', "O'Reilly Auto Parts");

        $tireCat = $categories->get('Tires') ?? $categories->first();
        $matCat  = $categories->get('Materials & Parts') ?? $categories->first();

        $tires = [
            ['sku' => 'GT-21555R17', 'name' => 'GT Radial Champiro VP1 215/55R17',        'brand' => 'GT Radial',   'line' => 'Champiro VP1',     'width' => 215, 'ratio' => 55, 'diameter' => 17, 'tire_type' => 'all_season',  'cost' =>  48.00, 'sale' =>  89.99, 'min' => 6,  'supplier' => $atd],
            ['sku' => 'MI-20555R16', 'name' => 'Michelin Defender 205/55R16',             'brand' => 'Michelin',    'line' => 'Defender',         'width' => 205, 'ratio' => 55, 'diameter' => 16, 'tire_type' => 'all_season',  'cost' =>  92.00, 'sale' => 165.00, 'min' => 4,  'supplier' => $atd],
            ['sku' => 'GY-23560R17', 'name' => 'Goodyear Assurance WS 235/60R17',         'brand' => 'Goodyear',    'line' => 'Assurance WS',     'width' => 235, 'ratio' => 60, 'diameter' => 17, 'tire_type' => 'winter',      'cost' =>  74.00, 'sale' => 139.99, 'min' => 4,  'supplier' => $pri],
            ['sku' => 'FI-24565R17', 'name' => 'Firestone Destination LE3 245/65R17',     'brand' => 'Firestone',   'line' => 'Destination LE3',  'width' => 245, 'ratio' => 65, 'diameter' => 17, 'tire_type' => 'all_season',  'cost' =>  68.00, 'sale' => 119.99, 'min' => 4,  'supplier' => $pri],
            ['sku' => 'CO-22560R17', 'name' => 'Cooper Discoverer AT3 225/60R17',         'brand' => 'Cooper',      'line' => 'Discoverer AT3',   'width' => 225, 'ratio' => 60, 'diameter' => 17, 'tire_type' => 'all_terrain', 'cost' =>  82.00, 'sale' => 149.99, 'min' => 4,  'supplier' => $ntw],
            ['sku' => 'HK-19560R15', 'name' => 'Hankook Kinergy GT 195/60R15',            'brand' => 'Hankook',     'line' => 'Kinergy GT',       'width' => 195, 'ratio' => 60, 'diameter' => 15, 'tire_type' => 'all_season',  'cost' =>  42.00, 'sale' =>  79.99, 'min' => 6,  'supplier' => $ntw],
            ['sku' => 'TO-26570R17', 'name' => 'Toyo Open Country AT3 265/70R17',         'brand' => 'Toyo',        'line' => 'Open Country AT3', 'width' => 265, 'ratio' => 70, 'diameter' => 17, 'tire_type' => 'all_terrain', 'cost' =>  95.00, 'sale' => 179.99, 'min' => 4,  'supplier' => $atd],
            ['sku' => 'BR-22565R17', 'name' => 'Bridgestone Dueler H/L 225/65R17',        'brand' => 'Bridgestone', 'line' => 'Dueler H/L',       'width' => 225, 'ratio' => 65, 'diameter' => 17, 'tire_type' => 'all_season',  'cost' =>  78.00, 'sale' => 145.00, 'min' => 4,  'supplier' => $pri],
            ['sku' => 'CO-18565R15', 'name' => 'Continental TrueContact 185/65R15',       'brand' => 'Continental', 'line' => 'TrueContact',      'width' => 185, 'ratio' => 65, 'diameter' => 15, 'tire_type' => 'all_season',  'cost' =>  55.00, 'sale' =>  99.99, 'min' => 6,  'supplier' => $ntw],
            ['sku' => 'PI-24545R18', 'name' => 'Pirelli Cinturato P7 245/45R18',          'brand' => 'Pirelli',     'line' => 'Cinturato P7',     'width' => 245, 'ratio' => 45, 'diameter' => 18, 'tire_type' => 'summer',      'cost' => 105.00, 'sale' => 199.99, 'min' => 2,  'supplier' => $atd],
        ];

        foreach ($tires as $t) {
            Product::firstOrCreate(['sku' => $t['sku']], [
                'supplier_id'          => $t['supplier']->id,
                'category_id'          => $tireCat->id,
                'type'                 => 'tire',
                'sku'                  => $t['sku'],
                'name'                 => $t['name'],
                'tire_brand'           => $t['brand'],
                'tire_line'            => $t['line'],
                'tire_width'           => $t['width'],
                'tire_ratio'           => $t['ratio'],
                'tire_diameter'        => $t['diameter'],
                'tire_type'            => $t['tire_type'],
                'tire_size_normalized' => Product::normalizeTireSize("{$t['width']}/{$t['ratio']}R{$t['diameter']}"),
                'cost_price'           => $t['cost'],
                'sale_price'           => $t['sale'],
                'stock_qty'            => 0,
                'stock_min'            => $t['min'],
                'unit'                 => 'unit',
                'is_active'            => true,
            ]);
        }

        $materials = [
            ['sku' => 'VAL-STD',    'name' => 'Standard Valve Stem',           'cost' =>  0.75, 'sale' =>  3.50, 'min' =>  50, 'supplier' => $ore],
            ['sku' => 'VAL-TPMS',   'name' => 'TPMS Valve Stem Replacement',   'cost' =>  8.50, 'sale' => 24.99, 'min' =>  10, 'supplier' => $ore],
            ['sku' => 'WGT-FE-1oz', 'name' => 'Fe Clip-on Wheel Weight 1oz',   'cost' =>  0.10, 'sale' =>  0.75, 'min' => 100, 'supplier' => $hf],
            ['sku' => 'WGT-ST-1oz', 'name' => 'Stick-on Wheel Weight 1oz',     'cost' =>  0.12, 'sale' =>  0.80, 'min' => 100, 'supplier' => $hf],
            ['sku' => 'TLUB-16oz',  'name' => 'Tire Mounting Lube 16oz',        'cost' =>  4.20, 'sale' => 12.99, 'min' =>   5, 'supplier' => $hf],
            ['sku' => 'BEAD-SEAL',  'name' => 'Bead Sealer 8oz',               'cost' =>  3.50, 'sale' =>  9.99, 'min' =>   5, 'supplier' => $ore],
            ['sku' => 'PLUG-KIT',   'name' => 'Tire Plug Repair Kit',           'cost' =>  1.20, 'sale' =>  4.99, 'min' =>  15, 'supplier' => $ore],
            ['sku' => 'PATCH-4IN',  'name' => 'Radial Tire Patch 4"',           'cost' =>  0.90, 'sale' =>  3.99, 'min' =>  20, 'supplier' => $ore],
            ['sku' => 'NITRO-FIL',  'name' => 'Nitrogen Inflation (per tire)',  'cost' =>  0.50, 'sale' =>  5.00, 'min' =>  10, 'supplier' => $hf],
            ['sku' => 'LUG-NUT-5',  'name' => 'Lug Nut Set (5-pack)',           'cost' =>  4.50, 'sale' => 14.99, 'min' =>  10, 'supplier' => $ore],
        ];

        foreach ($materials as $m) {
            Product::firstOrCreate(['sku' => $m['sku']], [
                'supplier_id' => $m['supplier']->id,
                'category_id' => $matCat->id,
                'type'        => 'material',
                'sku'         => $m['sku'],
                'name'        => $m['name'],
                'cost_price'  => $m['cost'],
                'sale_price'  => $m['sale'],
                'stock_qty'   => 0,
                'stock_min'   => $m['min'],
                'unit'        => 'unit',
                'is_active'   => true,
            ]);
        }
    }

    // ── Services ─────────────────────────────────────────────────────────────

    private function seedServices(): void
    {
        $cats = ServiceCategory::all()->keyBy('name');

        $tire    = $cats->get('Tire Services');
        $wheel   = $cats->get('Wheel Services');
        $balance = $cats->get('Balancing');
        $align   = $cats->get('Alignment');
        $tpms    = $cats->get('TPMS');
        $other   = $cats->get('Other');

        $services = [
            ['cat' => $tire,    'name' => 'Mount & Dismount (each)',      'pricing_type' => 'by_vehicle_type', 'base_price' => 12.00, 'minutes' => 20],
            ['cat' => $tire,    'name' => 'Tire Repair – Plug',           'pricing_type' => 'fixed',           'base_price' => 15.00, 'minutes' => 20],
            ['cat' => $tire,    'name' => 'Tire Repair – Patch',          'pricing_type' => 'fixed',           'base_price' => 25.00, 'minutes' => 35],
            ['cat' => $tire,    'name' => 'Tire Rotation (set of 4)',     'pricing_type' => 'by_vehicle_type', 'base_price' => 20.00, 'minutes' => 30],
            ['cat' => $tire,    'name' => 'Flat Fix (on vehicle)',        'pricing_type' => 'fixed',           'base_price' => 30.00, 'minutes' => 25],
            ['cat' => $wheel,   'name' => 'Wheel Swap (set of 4)',        'pricing_type' => 'by_vehicle_type', 'base_price' => 40.00, 'minutes' => 45],
            ['cat' => $wheel,   'name' => 'Run-flat Tire Mount (each)',   'pricing_type' => 'by_vehicle_type', 'base_price' => 25.00, 'minutes' => 30],
            ['cat' => $balance, 'name' => 'Wheel Balance (each)',         'pricing_type' => 'by_vehicle_type', 'base_price' => 14.00, 'minutes' => 15],
            ['cat' => $balance, 'name' => 'Wheel Balance (set of 4)',     'pricing_type' => 'by_vehicle_type', 'base_price' => 50.00, 'minutes' => 45],
            ['cat' => $balance, 'name' => 'Road Force Balance (each)',    'pricing_type' => 'fixed',           'base_price' => 22.00, 'minutes' => 20],
            ['cat' => $align,   'name' => 'Front Alignment',              'pricing_type' => 'by_vehicle_type', 'base_price' => 65.00, 'minutes' => 60],
            ['cat' => $align,   'name' => '4-Wheel Alignment',            'pricing_type' => 'by_vehicle_type', 'base_price' => 95.00, 'minutes' => 90],
            ['cat' => $tpms,    'name' => 'TPMS Sensor Reset',            'pricing_type' => 'fixed',           'base_price' => 20.00, 'minutes' => 15],
            ['cat' => $tpms,    'name' => 'TPMS Sensor Replace (each)',   'pricing_type' => 'fixed',           'base_price' => 55.00, 'minutes' => 25],
            ['cat' => $other,   'name' => 'Nitrogen Fill (set of 4)',     'pricing_type' => 'fixed',           'base_price' => 20.00, 'minutes' => 15],
            ['cat' => $other,   'name' => 'Tire Disposal Fee (each)',     'pricing_type' => 'fixed',           'base_price' =>  4.00, 'minutes' =>  5],
            ['cat' => $other,   'name' => 'Lug Nut Torque Check',         'pricing_type' => 'fixed',           'base_price' => 10.00, 'minutes' => 10],
        ];

        foreach ($services as $i => $s) {
            if (! $s['cat']) continue;

            $svc = Service::firstOrCreate(['name' => $s['name']], [
                'category_id'       => $s['cat']->id,
                'name'              => $s['name'],
                'pricing_type'      => $s['pricing_type'],
                'base_price'        => $s['base_price'],
                'estimated_minutes' => $s['minutes'],
                'is_active'         => true,
                'sort_order'        => $i,
            ]);

            if ($s['pricing_type'] === 'by_vehicle_type') {
                $p = $s['base_price'];
                foreach ([
                    'sedan'  => $p,
                    'suv'    => round($p * 1.20, 2),
                    'pickup' => round($p * 1.25, 2),
                    'van'    => round($p * 1.30, 2),
                    'truck'  => round($p * 1.60, 2),
                ] as $vtype => $price) {
                    ServicePriceRule::firstOrCreate(
                        ['service_id' => $svc->id, 'vehicle_type' => $vtype],
                        ['price' => $price]
                    );
                }
            }
        }
    }
}
