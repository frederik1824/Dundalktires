const fs = require('fs');
const file = 'app/Http/Controllers/Customers/VehicleController.php';
let content = fs.readFileSync(file, 'utf8');

// The line is currently:
// 'catalog' => \App\Models\Vehicle::select('make', 'model', 'year', 'type', 'tire_size')->distinct()->orderBy('make')->orderBy('model')->get(),

content = content.replace(
    /'catalog'\s*=>\s*\\App\\Models\\Vehicle::select\('make', 'model', 'year', 'type', 'tire_size'\)->distinct\(\)->orderBy\('make'\)->orderBy\('model'\)->get\(\),/g,
    `'catalog' => collect(\\App\\Helpers\\VehicleCatalog::get())
                ->concat(\\App\\Models\\Vehicle::select('make', 'model', 'year', 'type', 'tire_size')->distinct()->get()->toArray())
                ->unique(function ($item) { return $item['make'] . $item['model'] . $item['year']; })
                ->sortBy('make')
                ->values()
                ->toArray(),`
);

fs.writeFileSync(file, content);
