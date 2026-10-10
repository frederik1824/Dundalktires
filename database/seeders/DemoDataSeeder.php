<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\{
    User, Setting, Supplier, Customer, Vehicle, Product, ProductCategory,
    Service, ServiceCategory, ServicePriceRule,
    PurchaseOrder, PurchaseOrderItem,
    WorkOrder, WorkOrderService, WorkOrderProduct,
    Invoice, InvoiceItem, Payment,
    Expense, ExpenseCategory
};

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSettings();
        $this->seedUsers();
        $admin = User::where('email', 'admin@dundalk.com')->first();

        $suppliers   = $this->seedSuppliers();
        $categories  = ProductCategory::all()->keyBy('name');
        $products    = $this->seedProducts($suppliers, $categories);

        $svcCats = ServiceCategory::all()->keyBy('name');
        $services = $this->seedServices($svcCats);

        $customers = $this->seedCustomers();
        $vehicles  = $this->seedVehicles($customers);

        $this->seedPurchases($admin, $suppliers, $products);
        $this->seedWorkOrders($admin, $customers, $vehicles, $services, $products);
        $this->seedExpenses($admin);
    }

    // ── Settings ─────────────────────────────────────────────────────────────

    private function seedSettings(): void
    {
        $settings = [
            ['key' => 'business_name',    'value' => 'Dundalk Used & New Tires', 'type' => 'string'],
            ['key' => 'business_address', 'value' => '7888 Dundalk Ave, Dundalk, MD 21222', 'type' => 'string'],
            ['key' => 'business_phone',   'value' => '667-727-3115', 'type' => 'string'],
            ['key' => 'business_email',   'value' => 'info@dundalktiresmd.com', 'type' => 'string'],
            ['key' => 'invoice_prefix',   'value' => 'INV', 'type' => 'string'],
            ['key' => 'purchase_prefix',  'value' => 'PO',  'type' => 'string'],
            ['key' => 'work_order_prefix','value' => 'WO',  'type' => 'string'],
            ['key' => 'tax_rate',         'value' => '6.00','type' => 'decimal'],
            ['key' => 'currency_symbol',  'value' => '$',   'type' => 'string'],
            ['key' => 'low_stock_alert',  'value' => '5',   'type' => 'integer'],
        ];

        foreach ($settings as $s) {
            Setting::updateOrCreate(
                ['key' => $s['key'], 'branch_id' => null],
                ['value' => $s['value'], 'type' => $s['type']]
            );
        }
    }

    // ── Users / Employees ────────────────────────────────────────────────────

    private function seedUsers(): void
    {
        $employees = [
            ['name' => 'Marcus Johnson',  'email' => 'marcus@dundalk.com',  'role' => 'mechanic'],
            ['name' => 'Derek Williams',  'email' => 'derek@dundalk.com',   'role' => 'mechanic'],
            ['name' => 'Sandra Torres',   'email' => 'sandra@dundalk.com',  'role' => 'secretary'],
            ['name' => 'Kevin Park',      'email' => 'kevin@dundalk.com',   'role' => 'owner'],
        ];

        foreach ($employees as $e) {
            $user = User::firstOrCreate(
                ['email' => $e['email']],
                ['name' => $e['name'], 'password' => Hash::make('password'), 'email_verified_at' => now()]
            );
            if ($user->roles->isEmpty()) {
                $user->assignRole($e['role']);
            }
        }
    }

    // ── Suppliers ────────────────────────────────────────────────────────────

    private function seedSuppliers(): \Illuminate\Support\Collection
    {
        $data = [
            ['name' => 'American Tire Distributors',  'contact_name' => 'Tom Bradley',    'phone' => '(704) 992-2000', 'email' => 'tbradley@atd.com',       'address' => '12200 Herbert Wayne Ct, Huntersville, NC'],
            ['name' => 'Priority Tire Wholesale',     'contact_name' => 'Lisa Chen',       'phone' => '(855) 977-7473', 'email' => 'lchen@prioritytire.com',  'address' => '800 Commerce Blvd, Carlstadt, NJ'],
            ['name' => 'NTW National Tire Wholesale', 'contact_name' => 'Bob Garrett',     'phone' => '(866) 689-6089', 'email' => 'bgarrett@ntw.com',        'address' => '2605 Enterprise Rd E, Clearwater, FL'],
            ['name' => 'Harbor Freight Tools',        'contact_name' => 'Mike Saenz',      'phone' => '(800) 444-3353', 'email' => 'mike@harborfreight.com',  'address' => '26541 Agoura Rd, Calabasas, CA'],
            ['name' => 'O\'Reilly Auto Parts',         'contact_name' => 'Pam Richards',   'phone' => '(417) 862-6708', 'email' => 'prichards@oreilly.com',   'address' => '233 S Patterson Ave, Springfield, MO'],
        ];

        foreach ($data as $d) {
            Supplier::firstOrCreate(['email' => $d['email']], array_merge($d, ['is_active' => true]));
        }

        return Supplier::all();
    }

    // ── Products ─────────────────────────────────────────────────────────────

    private function seedProducts(\Illuminate\Support\Collection $suppliers, \Illuminate\Support\Collection $categories): \Illuminate\Support\Collection
    {
        $atd = $suppliers->firstWhere('name', 'American Tire Distributors');
        $pri = $suppliers->firstWhere('name', 'Priority Tire Wholesale');
        $ntw = $suppliers->firstWhere('name', 'NTW National Tire Wholesale');
        $hf  = $suppliers->firstWhere('name', 'Harbor Freight Tools');
        $ore = $suppliers->firstWhere('name', 'O\'Reilly Auto Parts');

        $tireCat = $categories->get('Tires') ?? $categories->first();
        $matCat  = $categories->get('Materials & Parts') ?? $categories->first();

        $tires = [
            ['sku'=>'GT-21555R17', 'name'=>'GT Radial Champiro VP1 215/55R17', 'brand'=>'GT Radial', 'line'=>'Champiro VP1', 'width'=>215,'ratio'=>55,'diameter'=>17,'tire_type'=>'all_season','cost'=>48.00,'sale'=>89.99,'stock'=>24,'min'=>6,'supplier_id'=>$atd->id],
            ['sku'=>'MI-20555R16', 'name'=>'Michelin Defender 205/55R16',      'brand'=>'Michelin',  'line'=>'Defender',     'width'=>205,'ratio'=>55,'diameter'=>16,'tire_type'=>'all_season','cost'=>92.00,'sale'=>165.00,'stock'=>12,'min'=>4,'supplier_id'=>$atd->id],
            ['sku'=>'GY-23560R17', 'name'=>'Goodyear Assurance WS 235/60R17',  'brand'=>'Goodyear',  'line'=>'Assurance WS', 'width'=>235,'ratio'=>60,'diameter'=>17,'tire_type'=>'winter',    'cost'=>74.00,'sale'=>139.99,'stock'=>8, 'min'=>4,'supplier_id'=>$pri->id],
            ['sku'=>'FI-24565R17', 'name'=>'Firestone Destination LE3 245/65R17','brand'=>'Firestone','line'=>'Destination LE3','width'=>245,'ratio'=>65,'diameter'=>17,'tire_type'=>'all_season','cost'=>68.00,'sale'=>119.99,'stock'=>16,'min'=>4,'supplier_id'=>$pri->id],
            ['sku'=>'CO-22560R17', 'name'=>'Cooper Discoverer AT3 225/60R17',  'brand'=>'Cooper',    'line'=>'Discoverer AT3','width'=>225,'ratio'=>60,'diameter'=>17,'tire_type'=>'all_terrain','cost'=>82.00,'sale'=>149.99,'stock'=>10,'min'=>4,'supplier_id'=>$ntw->id],
            ['sku'=>'HK-19560R15', 'name'=>'Hankook Kinergy GT 195/60R15',     'brand'=>'Hankook',   'line'=>'Kinergy GT',   'width'=>195,'ratio'=>60,'diameter'=>15,'tire_type'=>'all_season','cost'=>42.00,'sale'=>79.99,'stock'=>20,'min'=>6,'supplier_id'=>$ntw->id],
            ['sku'=>'TO-26570R17', 'name'=>'Toyo Open Country AT3 265/70R17',  'brand'=>'Toyo',      'line'=>'Open Country AT3','width'=>265,'ratio'=>70,'diameter'=>17,'tire_type'=>'all_terrain','cost'=>95.00,'sale'=>179.99,'stock'=>6,'min'=>4,'supplier_id'=>$atd->id],
            ['sku'=>'BR-22565R17', 'name'=>'Bridgestone Dueler H/L 225/65R17', 'brand'=>'Bridgestone','line'=>'Dueler H/L',  'width'=>225,'ratio'=>65,'diameter'=>17,'tire_type'=>'all_season','cost'=>78.00,'sale'=>145.00,'stock'=>14,'min'=>4,'supplier_id'=>$pri->id],
            ['sku'=>'CO-18565R15', 'name'=>'Continental TrueContact 185/65R15','brand'=>'Continental','line'=>'TrueContact', 'width'=>185,'ratio'=>65,'diameter'=>15,'tire_type'=>'all_season','cost'=>55.00,'sale'=>99.99,'stock'=>18,'min'=>6,'supplier_id'=>$ntw->id],
            ['sku'=>'PI-24545R18', 'name'=>'Pirelli Cinturato P7 245/45R18',   'brand'=>'Pirelli',   'line'=>'Cinturato P7', 'width'=>245,'ratio'=>45,'diameter'=>18,'tire_type'=>'summer',    'cost'=>105.00,'sale'=>199.99,'stock'=>4,'min'=>2,'supplier_id'=>$atd->id],
        ];

        foreach ($tires as $t) {
            Product::firstOrCreate(['sku' => $t['sku']], [
                'supplier_id'          => $t['supplier_id'],
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
                'stock_qty'            => $t['stock'],
                'stock_min'            => $t['min'],
                'unit'                 => 'unit',
                'is_active'            => true,
            ]);
        }

        $materials = [
            ['sku'=>'VAL-STD',    'name'=>'Standard Valve Stem',         'cost'=>0.75,  'sale'=>3.50,  'stock'=>200,'min'=>50,'supplier_id'=>$ore->id],
            ['sku'=>'VAL-TPMS',   'name'=>'TPMS Valve Stem Replacement', 'cost'=>8.50,  'sale'=>24.99, 'stock'=>40, 'min'=>10,'supplier_id'=>$ore->id],
            ['sku'=>'WGT-FE-1oz', 'name'=>'Fe Clip-on Wheel Weight 1oz', 'cost'=>0.10,  'sale'=>0.75,  'stock'=>500,'min'=>100,'supplier_id'=>$hf->id],
            ['sku'=>'WGT-ST-1oz', 'name'=>'Stick-on Wheel Weight 1oz',   'cost'=>0.12,  'sale'=>0.80,  'stock'=>400,'min'=>100,'supplier_id'=>$hf->id],
            ['sku'=>'TLUB-16oz',  'name'=>'Tire Mounting Lube 16oz',      'cost'=>4.20,  'sale'=>12.99, 'stock'=>30, 'min'=>5, 'supplier_id'=>$hf->id],
            ['sku'=>'BEAD-SEAL',  'name'=>'Bead Sealer 8oz',              'cost'=>3.50,  'sale'=>9.99,  'stock'=>25, 'min'=>5, 'supplier_id'=>$ore->id],
            ['sku'=>'PLUG-KIT',   'name'=>'Tire Plug Repair Kit',         'cost'=>1.20,  'sale'=>4.99,  'stock'=>60, 'min'=>15,'supplier_id'=>$ore->id],
            ['sku'=>'PATCH-4IN',  'name'=>'Radial Tire Patch 4"',         'cost'=>0.90,  'sale'=>3.99,  'stock'=>80, 'min'=>20,'supplier_id'=>$ore->id],
            ['sku'=>'NITRO-FIL',  'name'=>'Nitrogen Inflation (per tire)', 'cost'=>0.50,  'sale'=>5.00,  'stock'=>999,'min'=>10,'supplier_id'=>$hf->id],
            ['sku'=>'LUG-NUT-5',  'name'=>'Lug Nut Set (5-pack)',         'cost'=>4.50,  'sale'=>14.99, 'stock'=>35, 'min'=>10,'supplier_id'=>$ore->id],
        ];

        foreach ($materials as $m) {
            Product::firstOrCreate(['sku' => $m['sku']], [
                'supplier_id' => $m['supplier_id'],
                'category_id' => $matCat->id,
                'type'        => 'material',
                'sku'         => $m['sku'],
                'name'        => $m['name'],
                'cost_price'  => $m['cost'],
                'sale_price'  => $m['sale'],
                'stock_qty'   => $m['stock'],
                'stock_min'   => $m['min'],
                'unit'        => 'unit',
                'is_active'   => true,
            ]);
        }

        return Product::all();
    }

    // ── Services ─────────────────────────────────────────────────────────────

    private function seedServices(\Illuminate\Support\Collection $cats): \Illuminate\Support\Collection
    {
        $tire     = $cats->get('Tire Services');
        $wheel    = $cats->get('Wheel Services');
        $balance  = $cats->get('Balancing');
        $align    = $cats->get('Alignment');
        $tpms     = $cats->get('TPMS');
        $other    = $cats->get('Other');

        $services = [
            // Tire Services
            ['cat'=>$tire,    'name'=>'Mount & Dismount (each)',        'pricing_type'=>'by_vehicle_type','base_price'=>12.00,'minutes'=>20],
            ['cat'=>$tire,    'name'=>'Tire Repair – Plug',             'pricing_type'=>'fixed',          'base_price'=>15.00,'minutes'=>20],
            ['cat'=>$tire,    'name'=>'Tire Repair – Patch',            'pricing_type'=>'fixed',          'base_price'=>25.00,'minutes'=>35],
            ['cat'=>$tire,    'name'=>'Tire Rotation (set of 4)',       'pricing_type'=>'by_vehicle_type','base_price'=>20.00,'minutes'=>30],
            ['cat'=>$tire,    'name'=>'Flat Fix (on vehicle)',          'pricing_type'=>'fixed',          'base_price'=>30.00,'minutes'=>25],
            // Wheel Services
            ['cat'=>$wheel,   'name'=>'Wheel Swap (set of 4)',          'pricing_type'=>'by_vehicle_type','base_price'=>40.00,'minutes'=>45],
            ['cat'=>$wheel,   'name'=>'Run-flat Tire Mount (each)',     'pricing_type'=>'by_vehicle_type','base_price'=>25.00,'minutes'=>30],
            // Balancing
            ['cat'=>$balance, 'name'=>'Wheel Balance (each)',           'pricing_type'=>'by_vehicle_type','base_price'=>14.00,'minutes'=>15],
            ['cat'=>$balance, 'name'=>'Wheel Balance (set of 4)',       'pricing_type'=>'by_vehicle_type','base_price'=>50.00,'minutes'=>45],
            ['cat'=>$balance, 'name'=>'Road Force Balance (each)',      'pricing_type'=>'fixed',          'base_price'=>22.00,'minutes'=>20],
            // Alignment
            ['cat'=>$align,   'name'=>'Front Alignment',                'pricing_type'=>'by_vehicle_type','base_price'=>65.00,'minutes'=>60],
            ['cat'=>$align,   'name'=>'4-Wheel Alignment',              'pricing_type'=>'by_vehicle_type','base_price'=>95.00,'minutes'=>90],
            // TPMS
            ['cat'=>$tpms,    'name'=>'TPMS Sensor Reset',              'pricing_type'=>'fixed',          'base_price'=>20.00,'minutes'=>15],
            ['cat'=>$tpms,    'name'=>'TPMS Sensor Replace (each)',     'pricing_type'=>'fixed',          'base_price'=>55.00,'minutes'=>25],
            // Other
            ['cat'=>$other,   'name'=>'Nitrogen Fill (set of 4)',       'pricing_type'=>'fixed',          'base_price'=>20.00,'minutes'=>15],
            ['cat'=>$other,   'name'=>'Tire Disposal Fee (each)',       'pricing_type'=>'fixed',          'base_price'=>4.00, 'minutes'=>5],
            ['cat'=>$other,   'name'=>'Lug Nut Torque Check',           'pricing_type'=>'fixed',          'base_price'=>10.00,'minutes'=>10],
        ];

        foreach ($services as $i => $s) {
            if (! $s['cat']) continue;
            $svc = Service::firstOrCreate(['name' => $s['name']], [
                'category_id'      => $s['cat']->id,
                'name'             => $s['name'],
                'pricing_type'     => $s['pricing_type'],
                'base_price'       => $s['base_price'],
                'estimated_minutes'=> $s['minutes'],
                'is_active'        => true,
                'sort_order'       => $i,
            ]);

            // Add vehicle-type price rules for by_vehicle_type services
            if ($s['pricing_type'] === 'by_vehicle_type') {
                $rules = [
                    'sedan'  => $s['base_price'],
                    'suv'    => round($s['base_price'] * 1.2, 2),
                    'pickup' => round($s['base_price'] * 1.25, 2),
                    'van'    => round($s['base_price'] * 1.3, 2),
                    'truck'  => round($s['base_price'] * 1.6, 2),
                ];
                foreach ($rules as $vtype => $price) {
                    ServicePriceRule::firstOrCreate(
                        ['service_id' => $svc->id, 'vehicle_type' => $vtype],
                        ['price' => $price]
                    );
                }
            }
        }

        return Service::all();
    }

    // ── Customers ────────────────────────────────────────────────────────────

    private function seedCustomers(): \Illuminate\Support\Collection
    {
        $data = [
            ['name'=>'James Carter',      'phone'=>'(410) 555-1023','email'=>'jcarter@gmail.com',     'address'=>'112 Merritt Blvd, Dundalk, MD 21222'],
            ['name'=>'Maria Gonzalez',    'phone'=>'(410) 555-3847','email'=>'mariag@yahoo.com',       'address'=>'320 Holabird Ave, Baltimore, MD 21224'],
            ['name'=>'Robert Kim',        'phone'=>'(410) 555-7291','email'=>'rkim@hotmail.com',       'address'=>'5 Inverness Dr, Dundalk, MD 21222'],
            ['name'=>'Tiffany Brooks',    'phone'=>'(410) 555-4612','email'=>'tbrooks@gmail.com',      'address'=>'800 Wise Ave, Baltimore, MD 21222'],
            ['name'=>'Anthony Nguyen',    'phone'=>'(410) 555-0837','email'=>'anguyen@gmail.com',      'address'=>'44 Moravia Rd, Baltimore, MD 21206'],
            ['name'=>'Patricia Davis',    'phone'=>'(410) 555-6530','email'=>'pdavis@outlook.com',     'address'=>'1903 Eastern Ave, Baltimore, MD 21231'],
            ['name'=>'Carlos Reyes',      'phone'=>'(410) 555-2194','email'=>'creyes@gmail.com',       'address'=>'7723 Belair Rd, Baltimore, MD 21236'],
            ['name'=>'Shawna Mitchell',   'phone'=>'(410) 555-8842','email'=>'smitch@gmail.com',       'address'=>'3301 Sinclair Ln, Baltimore, MD 21213'],
            ['name'=>'William Turner',    'phone'=>'(410) 555-3377','email'=>'wturner@yahoo.com',      'address'=>'900 S Clinton St, Baltimore, MD 21224'],
            ['name'=>'Linda Pham',        'phone'=>'(410) 555-7704','email'=>'lpham@gmail.com',        'address'=>'200 Old Eastern Ave, Dundalk, MD 21222'],
        ];

        foreach ($data as $d) {
            Customer::firstOrCreate(['email' => $d['email']], array_merge($d, ['is_active' => true]));
        }

        return Customer::all();
    }

    // ── Vehicles ─────────────────────────────────────────────────────────────

    private function seedVehicles(\Illuminate\Support\Collection $customers): \Illuminate\Support\Collection
    {
        $vehicles = [
            ['customer' => 'James Carter',   'make'=>'Toyota',    'model'=>'Camry',    'year'=>2019,'color'=>'Silver', 'type'=>'sedan', 'plate'=>'8AB1234','tire_size'=>'215/55R17'],
            ['customer' => 'James Carter',   'make'=>'Honda',     'model'=>'Pilot',    'year'=>2017,'color'=>'Black',  'type'=>'suv',   'plate'=>'8CD5678','tire_size'=>'235/60R17'],
            ['customer' => 'Maria Gonzalez', 'make'=>'Chevrolet', 'model'=>'Traverse', 'year'=>2020,'color'=>'White',  'type'=>'suv',   'plate'=>'9MG3412','tire_size'=>'245/65R17'],
            ['customer' => 'Robert Kim',     'make'=>'Ford',      'model'=>'F-150',    'year'=>2021,'color'=>'Red',    'type'=>'pickup','plate'=>'5RK7721','tire_size'=>'265/70R17'],
            ['customer' => 'Tiffany Brooks', 'make'=>'Nissan',    'model'=>'Altima',   'year'=>2018,'color'=>'Blue',   'type'=>'sedan', 'plate'=>'3TB9901','tire_size'=>'215/55R17'],
            ['customer' => 'Anthony Nguyen', 'make'=>'Hyundai',   'model'=>'Tucson',   'year'=>2022,'color'=>'Gray',   'type'=>'suv',   'plate'=>'7AN4455','tire_size'=>'225/60R17'],
            ['customer' => 'Patricia Davis', 'make'=>'Chrysler',  'model'=>'Pacifica',  'year'=>2019,'color'=>'White',  'type'=>'van',   'plate'=>'2PD6683','tire_size'=>'235/65R17'],
            ['customer' => 'Carlos Reyes',   'make'=>'Ford',      'model'=>'Explorer', 'year'=>2020,'color'=>'Gray',   'type'=>'suv',   'plate'=>'6CR2211','tire_size'=>'255/55R18'],
            ['customer' => 'Shawna Mitchell','make'=>'Kia',       'model'=>'Sorento',  'year'=>2021,'color'=>'Black',  'type'=>'suv',   'plate'=>'4SM8890','tire_size'=>'235/65R17'],
            ['customer' => 'William Turner', 'make'=>'Dodge',     'model'=>'Ram 1500', 'year'=>2018,'color'=>'Black',  'type'=>'pickup','plate'=>'8WT3360','tire_size'=>'265/70R17'],
            ['customer' => 'Linda Pham',     'make'=>'Honda',     'model'=>'CR-V',     'year'=>2020,'color'=>'Pearl',  'type'=>'suv',   'plate'=>'1LP5522','tire_size'=>'225/65R17'],
        ];

        foreach ($vehicles as $v) {
            $customer = $customers->firstWhere('name', $v['customer']);
            if (! $customer) continue;
            Vehicle::firstOrCreate(['plate' => $v['plate']], [
                'customer_id' => $customer->id,
                'make'        => $v['make'],
                'model'       => $v['model'],
                'year'        => $v['year'],
                'color'       => $v['color'],
                'type'        => $v['type'],
                'plate'       => $v['plate'],
                'tire_size'   => $v['tire_size'],
                'is_active'   => true,
            ]);
        }

        return Vehicle::all();
    }

    // ── Purchases ────────────────────────────────────────────────────────────

    private function seedPurchases(User $admin, \Illuminate\Support\Collection $suppliers, \Illuminate\Support\Collection $products): void
    {
        $atd = $suppliers->firstWhere('name', 'American Tire Distributors');
        $pri = $suppliers->firstWhere('name', 'Priority Tire Wholesale');
        $ore = $suppliers->firstWhere('name', 'O\'Reilly Auto Parts');

        $orders = [
            [
                'supplier' => $atd,
                'number'   => 'PO-001',
                'status'   => 'received',
                'ordered_at' => now()->subDays(30),
                'received_at'=> now()->subDays(25),
                'items' => [
                    ['sku'=>'GT-21555R17', 'qty'=>12, 'cost'=>48.00, 'received'=>12],
                    ['sku'=>'MI-20555R16', 'qty'=>8,  'cost'=>92.00, 'received'=>8],
                    ['sku'=>'TO-26570R17', 'qty'=>6,  'cost'=>95.00, 'received'=>6],
                ],
            ],
            [
                'supplier' => $pri,
                'number'   => 'PO-002',
                'status'   => 'received',
                'ordered_at' => now()->subDays(20),
                'received_at'=> now()->subDays(16),
                'items' => [
                    ['sku'=>'GY-23560R17', 'qty'=>8,  'cost'=>74.00, 'received'=>8],
                    ['sku'=>'FI-24565R17', 'qty'=>10, 'cost'=>68.00, 'received'=>10],
                    ['sku'=>'BR-22565R17', 'qty'=>8,  'cost'=>78.00, 'received'=>8],
                ],
            ],
            [
                'supplier' => $ore,
                'number'   => 'PO-003',
                'status'   => 'received',
                'ordered_at' => now()->subDays(10),
                'received_at'=> now()->subDays(7),
                'items' => [
                    ['sku'=>'VAL-STD',   'qty'=>100,'cost'=>0.75,  'received'=>100],
                    ['sku'=>'VAL-TPMS',  'qty'=>20, 'cost'=>8.50,  'received'=>20],
                    ['sku'=>'PLUG-KIT',  'qty'=>30, 'cost'=>1.20,  'received'=>30],
                    ['sku'=>'PATCH-4IN', 'qty'=>40, 'cost'=>0.90,  'received'=>40],
                ],
            ],
            [
                'supplier' => $atd,
                'number'   => 'PO-004',
                'status'   => 'sent',
                'ordered_at' => now()->subDays(2),
                'received_at'=> null,
                'items' => [
                    ['sku'=>'CO-22560R17', 'qty'=>8,  'cost'=>82.00, 'received'=>0],
                    ['sku'=>'HK-19560R15', 'qty'=>12, 'cost'=>42.00, 'received'=>0],
                    ['sku'=>'PI-24545R18', 'qty'=>4,  'cost'=>105.00,'received'=>0],
                ],
            ],
        ];

        foreach ($orders as $o) {
            if (PurchaseOrder::where('number', $o['number'])->exists()) continue;

            $po = PurchaseOrder::create([
                'supplier_id' => $o['supplier']->id,
                'created_by'  => $admin->id,
                'number'      => $o['number'],
                'status'      => $o['status'],
                'ordered_at'  => $o['ordered_at'],
                'received_at' => $o['received_at'],
            ]);

            foreach ($o['items'] as $item) {
                $product = $products->firstWhere('sku', $item['sku']);
                if (! $product) continue;
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id'        => $product->id,
                    'qty_ordered'       => $item['qty'],
                    'qty_received'      => $item['received'],
                    'unit_cost'         => $item['cost'],
                ]);
            }
        }
    }

    // ── Work Orders & Invoices ────────────────────────────────────────────────

    private function seedWorkOrders(
        User $admin,
        \Illuminate\Support\Collection $customers,
        \Illuminate\Support\Collection $vehicles,
        \Illuminate\Support\Collection $services,
        \Illuminate\Support\Collection $products
    ): void {
        $technician = User::where('email', 'marcus@dundalk.com')->first() ?? $admin;
        $tech2      = User::where('email', 'derek@dundalk.com')->first()  ?? $admin;

        $workOrders = [
            [
                'customer' => 'James Carter',     'vehicle_plate' => '8AB1234',
                'number' => 'WO-001', 'status' => 'completed', 'invoiced' => true,
                'assigned' => $technician,
                'days_ago' => 25,
                'services' => [['name'=>'Mount & Dismount (each)','qty'=>4,'price'=>12.00],['name'=>'Wheel Balance (set of 4)','qty'=>1,'price'=>50.00],['name'=>'Tire Disposal Fee (each)','qty'=>4,'price'=>4.00]],
                'products' => [['sku'=>'GT-21555R17','qty'=>4,'price'=>89.99],['sku'=>'VAL-STD','qty'=>4,'price'=>3.50]],
                'payment_method' => 'card', 'payment_amount' => null,
            ],
            [
                'customer' => 'Maria Gonzalez',   'vehicle_plate' => '9MG3412',
                'number' => 'WO-002', 'status' => 'completed', 'invoiced' => true,
                'assigned' => $tech2,
                'days_ago' => 20,
                'services' => [['name'=>'Mount & Dismount (each)','qty'=>4,'price'=>15.00],['name'=>'4-Wheel Alignment','qty'=>1,'price'=>95.00],['name'=>'Wheel Balance (set of 4)','qty'=>1,'price'=>60.00]],
                'products' => [['sku'=>'FI-24565R17','qty'=>4,'price'=>119.99],['sku'=>'VAL-STD','qty'=>4,'price'=>3.50]],
                'payment_method' => 'cash', 'payment_amount' => null,
            ],
            [
                'customer' => 'Robert Kim',       'vehicle_plate' => '5RK7721',
                'number' => 'WO-003', 'status' => 'completed', 'invoiced' => true,
                'assigned' => $technician,
                'days_ago' => 15,
                'services' => [['name'=>'Tire Repair – Plug','qty'=>2,'price'=>15.00]],
                'products' => [['sku'=>'PLUG-KIT','qty'=>2,'price'=>4.99]],
                'payment_method' => 'cash', 'payment_amount' => null,
            ],
            [
                'customer' => 'Tiffany Brooks',   'vehicle_plate' => '3TB9901',
                'number' => 'WO-004', 'status' => 'completed', 'invoiced' => true,
                'assigned' => $tech2,
                'days_ago' => 12,
                'services' => [['name'=>'Mount & Dismount (each)','qty'=>4,'price'=>12.00],['name'=>'Wheel Balance (set of 4)','qty'=>1,'price'=>50.00],['name'=>'Tire Disposal Fee (each)','qty'=>4,'price'=>4.00]],
                'products' => [['sku'=>'HK-19560R15','qty'=>4,'price'=>79.99],['sku'=>'VAL-STD','qty'=>4,'price'=>3.50]],
                'payment_method' => 'zelle', 'payment_amount' => null,
            ],
            [
                'customer' => 'Anthony Nguyen',   'vehicle_plate' => '7AN4455',
                'number' => 'WO-005', 'status' => 'completed', 'invoiced' => true,
                'assigned' => $technician,
                'days_ago' => 10,
                'services' => [['name'=>'TPMS Sensor Replace (each)','qty'=>4,'price'=>55.00],['name'=>'TPMS Sensor Reset','qty'=>1,'price'=>20.00]],
                'products' => [['sku'=>'VAL-TPMS','qty'=>4,'price'=>24.99]],
                'payment_method' => 'card', 'payment_amount' => null,
            ],
            [
                'customer' => 'Patricia Davis',   'vehicle_plate' => '2PD6683',
                'number' => 'WO-006', 'status' => 'completed', 'invoiced' => true,
                'assigned' => $tech2,
                'days_ago' => 8,
                'services' => [['name'=>'Tire Rotation (set of 4)','qty'=>1,'price'=>26.00],['name'=>'Front Alignment','qty'=>1,'price'=>80.00]],
                'products' => [],
                'payment_method' => 'cashapp', 'payment_amount' => null,
            ],
            [
                'customer' => 'Carlos Reyes',     'vehicle_plate' => '6CR2211',
                'number' => 'WO-007', 'status' => 'completed', 'invoiced' => true,
                'assigned' => $technician,
                'days_ago' => 5,
                'services' => [['name'=>'Mount & Dismount (each)','qty'=>4,'price'=>15.00],['name'=>'Wheel Balance (set of 4)','qty'=>1,'price'=>60.00],['name'=>'Nitrogen Fill (set of 4)','qty'=>1,'price'=>20.00],['name'=>'Tire Disposal Fee (each)','qty'=>4,'price'=>4.00]],
                'products' => [['sku'=>'CO-22560R17','qty'=>4,'price'=>149.99],['sku'=>'VAL-STD','qty'=>4,'price'=>3.50]],
                'payment_method' => 'card', 'payment_amount' => null,
            ],
            [
                'customer' => 'Shawna Mitchell',  'vehicle_plate' => '4SM8890',
                'number' => 'WO-008', 'status' => 'in_progress', 'invoiced' => false,
                'assigned' => $tech2,
                'days_ago' => 0,
                'services' => [['name'=>'Mount & Dismount (each)','qty'=>4,'price'=>15.00],['name'=>'4-Wheel Alignment','qty'=>1,'price'=>115.00]],
                'products' => [['sku'=>'GY-23560R17','qty'=>2,'price'=>139.99]],
                'payment_method' => null, 'payment_amount' => null,
            ],
            [
                'customer' => 'William Turner',   'vehicle_plate' => '8WT3360',
                'number' => 'WO-009', 'status' => 'pending', 'invoiced' => false,
                'assigned' => $technician,
                'days_ago' => 0,
                'services' => [['name'=>'Tire Repair – Patch','qty'=>1,'price'=>25.00]],
                'products' => [['sku'=>'PATCH-4IN','qty'=>1,'price'=>3.99]],
                'payment_method' => null, 'payment_amount' => null,
            ],
            [
                'customer' => 'Linda Pham',       'vehicle_plate' => '1LP5522',
                'number' => 'WO-010', 'status' => 'pending', 'invoiced' => false,
                'assigned' => $tech2,
                'days_ago' => 0,
                'services' => [['name'=>'TPMS Sensor Reset','qty'=>1,'price'=>20.00],['name'=>'Lug Nut Torque Check','qty'=>1,'price'=>10.00]],
                'products' => [],
                'payment_method' => null, 'payment_amount' => null,
            ],
        ];

        foreach ($workOrders as $w) {
            if (WorkOrder::where('number', $w['number'])->exists()) continue;

            $customer = $customers->firstWhere('name', $w['customer']);
            $vehicle  = $vehicles->firstWhere('plate',  $w['vehicle_plate']);
            if (! $customer) continue;

            $createdAt   = now()->subDays($w['days_ago'])->subHours(rand(1, 6));
            $startedAt   = in_array($w['status'], ['in_progress','completed']) ? $createdAt->copy()->addHours(1) : null;
            $completedAt = $w['status'] === 'completed' ? $startedAt?->copy()->addHours(rand(2, 4)) : null;

            $wo = WorkOrder::create([
                'customer_id'  => $customer->id,
                'vehicle_id'   => $vehicle?->id,
                'assigned_to'  => $w['assigned']->id,
                'created_by'   => $admin->id,
                'number'       => $w['number'],
                'status'       => $w['status'],
                'invoiced'     => $w['invoiced'],
                'subtotal'     => 0,
                'tax'          => 0,
                'total'        => 0,
                'promised_at'  => $createdAt->copy()->addHours(4),
                'started_at'   => $startedAt,
                'completed_at' => $completedAt,
                'created_at'   => $createdAt,
                'updated_at'   => $createdAt,
            ]);

            foreach ($w['services'] as $s) {
                $svc = $services->firstWhere('name', $s['name']);
                WorkOrderService::create([
                    'work_order_id' => $wo->id,
                    'service_id'    => $svc?->id,
                    'name'          => $s['name'],
                    'qty'           => $s['qty'],
                    'unit_price'    => $s['price'],
                ]);
            }

            foreach ($w['products'] as $p) {
                $prod = Product::where('sku', $p['sku'])->first();
                WorkOrderProduct::create([
                    'work_order_id' => $wo->id,
                    'product_id'    => $prod?->id,
                    'name'          => $prod?->name ?? $p['sku'],
                    'qty'           => $p['qty'],
                    'unit_price'    => $p['price'],
                ]);
            }

            $wo->load('services', 'products');
            $wo->recalculate();

            // Create invoice for completed + invoiced orders
            if ($w['invoiced'] && $w['status'] === 'completed') {
                $this->createInvoice($wo, $admin, $w['payment_method'], $completedAt);
            }
        }
    }

    private function createInvoice(WorkOrder $wo, User $admin, ?string $method, ?Carbon $date): void
    {
        $wo->refresh()->load('services','products');
        $subtotal = (float) $wo->subtotal;
        $taxRate  = (float) Setting::get('tax_rate', 6.0);
        $tax      = round($subtotal * $taxRate / 100, 2);
        $total    = $subtotal + $tax;

        $inv = Invoice::create([
            'customer_id'   => $wo->customer_id,
            'work_order_id' => $wo->id,
            'created_by'    => $admin->id,
            'number'        => Invoice::nextNumber(),
            'status'        => 'draft',
            'subtotal'      => $subtotal,
            'tax'           => $tax,
            'discount'      => 0,
            'total'         => $total,
            'amount_paid'   => 0,
            'created_at'    => $date,
            'updated_at'    => $date,
        ]);

        foreach ($wo->services as $s) {
            InvoiceItem::create([
                'invoice_id'   => $inv->id,
                'type'         => 'service',
                'reference_id' => $s->service_id,
                'name'         => $s->name,
                'description'  => $s->name,
                'qty'          => $s->qty,
                'unit_price'   => $s->unit_price,
                'discount'     => 0,
                'total'        => $s->qty * $s->unit_price,
            ]);
        }

        foreach ($wo->products as $p) {
            InvoiceItem::create([
                'invoice_id'   => $inv->id,
                'type'         => 'product',
                'reference_id' => $p->product_id,
                'name'         => $p->name,
                'description'  => $p->name,
                'qty'          => $p->qty,
                'unit_price'   => $p->unit_price,
                'discount'     => 0,
                'total'        => $p->qty * $p->unit_price,
            ]);
        }

        if ($method) {
            Payment::create([
                'invoice_id'  => $inv->id,
                'method'      => $method,
                'amount'      => $total,
                'paid_at'     => $date,
                'created_at'  => $date,
                'updated_at'  => $date,
            ]);
            $inv->refreshStatus();
        }
    }

    // ── Expenses ─────────────────────────────────────────────────────────────

    private function seedExpenses(User $admin): void
    {
        $cats = ExpenseCategory::all()->keyBy('name');

        $expenses = [
            // Monthly recurring
            ['cat'=>'Rent',               'desc'=>'Monthly shop lease – September 2026',   'amount'=>3200.00,'method'=>'transfer','vendor'=>'Dundalk Properties LLC',    'days_ago'=>25,'recurring'=>true, 'recurrence'=>'monthly'],
            ['cat'=>'Utilities',          'desc'=>'BGE Electric & Gas – August 2026',       'amount'=>485.50, 'method'=>'transfer','vendor'=>'BGE',                       'days_ago'=>27,'recurring'=>true, 'recurrence'=>'monthly'],
            ['cat'=>'Insurance',          'desc'=>'Commercial liability insurance – Sep',   'amount'=>620.00, 'method'=>'transfer','vendor'=>'Nationwide Business',        'days_ago'=>1, 'recurring'=>true, 'recurrence'=>'monthly'],
            ['cat'=>'Payroll',            'desc'=>'Payroll – Week 38',                      'amount'=>4800.00,'method'=>'transfer','vendor'=>'ADP Payroll Services',       'days_ago'=>5, 'recurring'=>true, 'recurrence'=>'weekly'],
            ['cat'=>'Payroll',            'desc'=>'Payroll – Week 37',                      'amount'=>4800.00,'method'=>'transfer','vendor'=>'ADP Payroll Services',       'days_ago'=>12,'recurring'=>true, 'recurrence'=>'weekly'],
            // Variable
            ['cat'=>'Equipment',          'desc'=>'Tire changer maintenance service',       'amount'=>280.00, 'method'=>'card',    'vendor'=>'Coats Company',              'days_ago'=>18,'recurring'=>false,'recurrence'=>null],
            ['cat'=>'Repairs & Maintenance','desc'=>'Shop compressor repair',               'amount'=>145.00, 'method'=>'cash',    'vendor'=>'Baltimore Air Tools',        'days_ago'=>14,'recurring'=>false,'recurrence'=>null],
            ['cat'=>'Office Supplies',    'desc'=>'Printer paper & labels',                 'amount'=>62.40,  'method'=>'card',    'vendor'=>'Staples',                    'days_ago'=>9, 'recurring'=>false,'recurrence'=>null],
            ['cat'=>'Marketing',          'desc'=>'Google Ads – September campaign',        'amount'=>350.00, 'method'=>'card',    'vendor'=>'Google LLC',                 'days_ago'=>3, 'recurring'=>true, 'recurrence'=>'monthly'],
            ['cat'=>'Inventory / Parts',  'desc'=>'Emergency brake fluid stock purchase',  'amount'=>95.60,  'method'=>'cash',    'vendor'=>'O\'Reilly Auto Parts',       'days_ago'=>7, 'recurring'=>false,'recurrence'=>null],
            ['cat'=>'Other',              'desc'=>'Waste tire disposal – 40 units',        'amount'=>120.00, 'method'=>'cash',    'vendor'=>'Maryland Tire Recycling',    'days_ago'=>6, 'recurring'=>false,'recurrence'=>null],
            ['cat'=>'Utilities',          'desc'=>'Water & sewer bill – August',           'amount'=>88.20,  'method'=>'transfer','vendor'=>'Baltimore City DPW',         'days_ago'=>22,'recurring'=>true, 'recurrence'=>'monthly'],
        ];

        foreach ($expenses as $e) {
            $cat = $cats->get($e['cat']);
            if (! $cat) continue;
            Expense::create([
                'created_by'     => $admin->id,
                'category_id'    => $cat->id,
                'description'    => $e['desc'],
                'amount'         => $e['amount'],
                'payment_method' => $e['method'],
                'vendor'         => $e['vendor'],
                'is_recurring'   => $e['recurring'],
                'recurrence'     => $e['recurrence'],
                'paid_at'        => now()->subDays($e['days_ago'])->toDateString(),
                'created_at'     => now()->subDays($e['days_ago']),
                'updated_at'     => now()->subDays($e['days_ago']),
            ]);
        }
    }
}
