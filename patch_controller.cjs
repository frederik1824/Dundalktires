const fs = require('fs');
const file = 'app/Http/Controllers/Customers/VehicleController.php';
let content = fs.readFileSync(file, 'utf8');

// Replace create
content = content.replace(
    /return Inertia::render\('Vehicles\/Form', \[([\s\S]*?)\]\);/g,
    (match, p1) => {
        if (!p1.includes('catalog')) {
            return `return Inertia::render('Vehicles/Form', [${p1}
            'catalog' => \App\Models\Vehicle::select('make', 'model', 'year', 'type', 'tire_size')->distinct()->orderBy('make')->orderBy('model')->get(),
        ]);`;
        }
        return match;
    }
);

fs.writeFileSync(file, content);
