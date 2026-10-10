const fs = require('fs');

const heroImgPath = '/Users/usuario/Downloads/Desarrollo/Dunkdalk/public/negocio/WhatsApp Image 2026-09-28 at 10.10.57 AM.jpeg';
const facadeImgPath = '/Users/usuario/Downloads/Desarrollo/Dunkdalk/public/negocio/WhatsApp Image 2026-09-28 at 10.10.55 AM.jpeg';
const aboutImgPath = '/Users/usuario/Downloads/Desarrollo/Dunkdalk/public/negocio/WhatsApp Image 2026-09-28 at 10.10.55 AM (1).jpeg';
const logoPath = '/Users/usuario/Downloads/Desarrollo/Dunkdalk/public/Logo Dundalk.png';

function getBase64(file) {
    if (!fs.existsSync(file)) return '';
    const ext = file.split('.').pop().toLowerCase();
    let mime = 'image/jpeg';
    if (ext === 'png') mime = 'image/png';
    return `data:${mime};base64,` + fs.readFileSync(file).toString('base64');
}

const heroImg = getBase64(heroImgPath);
const facadeImg = getBase64(facadeImgPath);
const aboutImg = getBase64(aboutImgPath);
const logoImg = getBase64(logoPath);

const html = `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dundalk Used & New Tires</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            red: '#dc2626',
                            black: '#0f172a',
                            white: '#ffffff',
                        }
                    },
                    fontFamily: {
                        heading: ['"Barlow Condensed"', 'sans-serif'],
                        body: ['"Inter"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Barlow Condensed', sans-serif; text-transform: uppercase; }
        
        .photo-band {
            display: grid;
            grid-template-columns: 1fr;
        }
        @media (min-width: 768px) {
            .photo-band {
                grid-template-columns: 2fr 1fr;
            }
        }
    </style>
</head>
<body class="bg-brand-white text-brand-black antialiased">

    <!-- Navigation -->
    <nav class="bg-brand-black text-brand-white py-4 px-6 sticky top-0 z-50 shadow-lg border-b-4 border-brand-red">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <a href="#" class="flex items-center gap-3">
                <img src="${logoImg}" alt="Dundalk Tires Logo" class="h-12 w-auto object-contain bg-brand-white p-1 rounded">
            </a>
            <div class="hidden md:flex gap-6 font-heading text-xl tracking-wider">
                <a href="#services" class="hover:text-brand-red transition-colors">Services</a>
                <a href="#about" class="hover:text-brand-red transition-colors">About Us</a>
                <a href="#contact" class="hover:text-brand-red transition-colors">Contact</a>
            </div>
            <a href="tel:6677273115" class="bg-brand-red text-brand-white px-5 py-2 font-heading text-xl font-bold rounded shadow hover:bg-red-700 transition-colors">
                667-727-3115
            </a>
        </div>
    </nav>

    <!-- Photo Band Hero -->
    <header class="w-full">
        <div class="photo-band min-h-[60vh] lg:min-h-[70vh]">
            <!-- Left (2fr): Mustang -->
            <div class="bg-cover bg-center relative" style="background-image: url('${heroImg}'); min-height: 400px;">
                <div class="absolute inset-0 bg-gradient-to-r from-brand-black/90 to-brand-black/40 flex flex-col justify-center px-8 md:px-16">
                    <h1 class="text-5xl md:text-7xl font-extrabold text-brand-white mb-4 leading-tight tracking-wide">
                        PREMIUM <span class="text-brand-red">TIRE SERVICE</span><br>IN DUNDALK
                    </h1>
                    <p class="text-xl md:text-2xl text-gray-200 font-body mb-8 max-w-xl">
                        Fast, reliable, and professional. From plug & patch to brand new sets, we keep you rolling safely.
                    </p>
                    <div class="flex gap-4">
                        <a href="#services" class="bg-brand-red text-brand-white px-8 py-3 font-heading text-2xl font-bold rounded shadow-lg hover:bg-red-700 transition-transform transform hover:-translate-y-1">OUR SERVICES</a>
                    </div>
                </div>
            </div>
            <!-- Right (1fr): Fachada -->
            <div class="bg-cover bg-center hidden md:block border-l-8 border-brand-red" style="background-image: url('${facadeImg}');">
            </div>
        </div>
    </header>

    <!-- Services Section -->
    <section id="services" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-extrabold text-brand-black mb-2">Expert <span class="text-brand-red">Services</span></h2>
                <div class="w-24 h-1 bg-brand-red mx-auto"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Service Items -->
                <div class="bg-white p-8 rounded-xl shadow-md border-b-4 border-brand-black hover:border-brand-red transition-colors">
                    <h3 class="text-2xl font-bold mb-3">Tire Plug & Patch</h3>
                    <p class="text-gray-600">Quick and safe flat tire repairs using professional plug and patch methods.</p>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-md border-b-4 border-brand-black hover:border-brand-red transition-colors">
                    <h3 class="text-2xl font-bold mb-3">Mounting & Rotation</h3>
                    <p class="text-gray-600">Professional tire mounting and regular rotation to extend your tire's lifespan.</p>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-md border-b-4 border-brand-black hover:border-brand-red transition-colors">
                    <h3 class="text-2xl font-bold mb-3">Wheel Balancing</h3>
                    <p class="text-gray-600">Precision computer balancing for a smooth, vibration-free ride.</p>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-md border-b-4 border-brand-black hover:border-brand-red transition-colors">
                    <h3 class="text-2xl font-bold mb-3">Rims & Stems</h3>
                    <p class="text-gray-600">Valve stem replacement, rim cleaning, and basic wheel services.</p>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-md border-b-4 border-brand-red bg-red-50 transition-colors">
                    <h3 class="text-2xl font-bold mb-3 text-brand-red">New Tires</h3>
                    <p class="text-gray-700 font-medium">Top brands and sizes available. Unbeatable prices on brand new sets.</p>
                </div>
                <div class="bg-brand-black p-8 rounded-xl shadow-md border-b-4 border-brand-red text-white transition-colors">
                    <h3 class="text-2xl font-bold mb-3 text-brand-red">Used Tires</h3>
                    <p class="text-gray-300">High-quality, inspected used tires with plenty of tread left for any budget.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="rounded-2xl overflow-hidden shadow-2xl relative border-4 border-brand-red">
                    <img src="${aboutImg}" alt="Dundalk Tires Exterior" class="w-full h-auto object-cover">
                </div>
                <div>
                    <h2 class="text-4xl md:text-5xl font-extrabold text-brand-black mb-6">Local & <span class="text-brand-red">Trusted</span></h2>
                    <p class="text-lg text-gray-700 mb-6 leading-relaxed">
                        Dundalk Used & New Tires is your neighborhood shop dedicated to keeping you safe on the road. We know how important your vehicle is, which is why we provide fast, honest, and high-quality tire services without the upselling.
                    </p>
                    <p class="text-lg text-gray-700 mb-8 leading-relaxed">
                        Whether you need a quick patch for a flat, or a whole new set of wheels, our experienced technicians have you covered. Stop by and experience the best service in MD!
                    </p>
                    <a href="tel:6677273115" class="inline-block bg-brand-black text-brand-white px-8 py-3 font-heading text-2xl font-bold rounded hover:bg-gray-800 transition-colors">
                        CALL US TODAY
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer / Contact -->
    <footer id="contact" class="bg-brand-black text-white pt-16 pb-8 border-t-8 border-brand-red">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-12 mb-12">
            <div>
                <img src="${logoImg}" alt="Logo" class="h-16 w-auto object-contain bg-white p-2 rounded mb-6">
                <p class="text-gray-400 max-w-sm">Quality tires, professional service, and honest prices. Your safety is our top priority.</p>
            </div>
            <div class="flex flex-col md:items-end">
                <h3 class="text-3xl font-heading font-bold text-brand-red mb-4">Visit Us</h3>
                <p class="text-xl mb-2 flex items-center gap-2">
                    <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    7888 Dundalk Ave, Dundalk, MD 21222
                </p>
                <p class="text-xl mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    667-727-3115
                </p>
            </div>
        </div>
        <div class="text-center text-gray-500 text-sm border-t border-gray-800 pt-8">
            &copy; 2026 Dundalk Used & New Tires. All rights reserved.
        </div>
    </footer>

</body>
</html>`;

fs.writeFileSync('/Users/usuario/Downloads/Desarrollo/Dunkdalk/public/dundalk-landing.html', html);
