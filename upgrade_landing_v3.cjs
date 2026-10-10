const fs = require('fs');

const file = '/Users/usuario/Downloads/Desarrollo/Dunkdalk/public/dundalk-landing.html';

const heroImgPath = '/Users/usuario/Downloads/Desarrollo/Dunkdalk/public/negocio/WhatsApp Image 2026-09-28 at 10.10.57 AM.jpeg';
const facadeImgPath = '/Users/usuario/Downloads/Desarrollo/Dunkdalk/public/negocio/WhatsApp Image 2026-09-28 at 10.10.55 AM.jpeg';
const aboutImgPath = '/Users/usuario/Downloads/Desarrollo/Dunkdalk/public/negocio/WhatsApp Image 2026-09-28 at 10.10.55 AM (1).jpeg';
const logoPath = '/Users/usuario/Downloads/Desarrollo/Dunkdalk/public/Logo Dundalk.png';

function getBase64(f) {
    if (!fs.existsSync(f)) return '';
    const ext = f.split('.').pop().toLowerCase();
    let mime = 'image/jpeg';
    if (ext === 'png') mime = 'image/png';
    return `data:${mime};base64,` + fs.readFileSync(f).toString('base64');
}

const heroImg = getBase64(heroImgPath);
const facadeImg = getBase64(facadeImgPath);
const aboutImg = getBase64(aboutImgPath);
const logoImg = getBase64(logoPath);

const html = `<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dundalk Used & New Tires | Premium Auto Care</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            red: '#dc2626',
                            redhover: '#b91c1c',
                            black: '#111111',
                            dark: '#1a1a1a',
                            gray: '#f4f4f5',
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

    <!-- Alpine.js for interactions -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }
        h1, h2, h3, h4, .font-heading { font-family: 'Barlow Condensed', sans-serif; text-transform: uppercase; }
        
        .hero-bg {
            background-image: linear-gradient(to right, rgba(17,17,17,0.95) 0%, rgba(17,17,17,0.8) 50%, rgba(17,17,17,0.4) 100%), url('${heroImg}');
            background-size: cover;
            background-position: center;
        }

        /* Custom select styling for tire finder */
        select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1em;
        }
        
        .tire-diagram { background-image: radial-gradient(#d1d5db 1px, transparent 1px); background-size: 20px 20px; }
    </style>
</head>
<body class="bg-white text-gray-900" x-data="{ mobileMenu: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 20)">

    <!-- Top Bar -->
    <div class="bg-brand-black text-gray-300 text-xs py-2 hidden md:block">
        <div class="max-w-7xl mx-auto px-6 flex justify-between items-center">
            <div class="flex gap-6">
                <span class="flex items-center gap-2"><svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> 7888 Dundalk Ave, MD 21222</span>
                <span class="flex items-center gap-2"><svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Mon - Sat: 8:00 AM - 6:00 PM</span>
            </div>
            <div class="flex gap-4">
                <a href="#" class="hover:text-white transition-colors">Contact Us</a>
                <a href="#" class="hover:text-white transition-colors">Book Appointment</a>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav :class="{ 'py-3 shadow-lg bg-white/95 backdrop-blur-md': scrolled, 'py-5 bg-white': !scrolled }" class="sticky top-0 z-50 transition-all duration-300 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 flex justify-between items-center">
            <!-- Logo -->
            <a href="#" class="flex-shrink-0">
                <img src="${logoImg}" alt="Dundalk Tires Logo" class="h-12 w-auto object-contain">
            </a>

            <!-- Desktop Menu -->
            <div class="hidden lg:flex items-center gap-8 font-heading text-lg font-bold tracking-wide">
                <a href="#tires" class="hover:text-brand-red transition-colors">Tires</a>
                <a href="#services" class="hover:text-brand-red transition-colors">Services</a>
                <a href="#alignment" class="hover:text-brand-red transition-colors">Wheel Alignment</a>
                <a href="#about" class="hover:text-brand-red transition-colors">About Us</a>
            </div>

            <!-- CTA & Phone -->
            <div class="hidden lg:flex items-center gap-6">
                <a href="tel:6677273115" class="flex items-center gap-2 font-bold text-lg hover:text-brand-red transition-colors">
                    <svg class="w-5 h-5 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    667-727-3115
                </a>
                <a href="#book" class="bg-brand-red text-white px-6 py-2.5 font-heading text-xl font-bold rounded-sm shadow-md hover:bg-brand-redhover hover:-translate-y-0.5 transition-all">
                    Book Service
                </a>
            </div>

            <!-- Mobile Toggle -->
            <button @click="mobileMenu = !mobileMenu" class="lg:hidden p-2 text-brand-black">
                <svg x-show="!mobileMenu" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="mobileMenu" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenu" x-collapse class="lg:hidden bg-white border-t border-gray-100 absolute w-full left-0 shadow-xl" style="display: none;">
            <div class="px-6 py-4 flex flex-col gap-4 font-heading text-xl font-bold">
                <a href="#tires" class="block py-2 border-b border-gray-50">Tires</a>
                <a href="#services" class="block py-2 border-b border-gray-50">Services</a>
                <a href="#alignment" class="block py-2 border-b border-gray-50">Wheel Alignment</a>
                <a href="tel:6677273115" class="flex items-center gap-2 py-2 text-brand-red"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg> 667-727-3115</a>
                <a href="#book" class="block text-center bg-brand-red text-white py-3 rounded-sm mt-2">Book Service</a>
            </div>
        </div>
    </nav>

    <!-- HERO & TIRE FINDER -->
    <header class="relative w-full min-h-[600px] lg:min-h-[700px] flex items-center pt-8 pb-16 lg:py-0 hero-bg">
        <div class="max-w-7xl mx-auto px-6 w-full grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Hero Text -->
            <div class="lg:col-span-7 text-white">
                <div class="inline-block bg-brand-red px-4 py-1 font-heading font-bold text-lg mb-6 tracking-wide uppercase">New & Used Tires</div>
                <h1 class="text-5xl md:text-7xl font-extrabold mb-6 leading-[1.05] tracking-wide">
                    FIND THE RIGHT TIRES. <br><span class="text-gray-400">GET BACK ON THE ROAD.</span>
                </h1>
                <p class="text-lg md:text-xl text-gray-300 font-body mb-8 max-w-lg leading-relaxed">
                    Professional installation, precision alignment, balancing, and full mechanical services in Dundalk. Quality you can trust.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#book" class="bg-white text-brand-black px-8 py-3.5 font-heading text-xl font-bold rounded-sm shadow-lg hover:bg-gray-100 transition-colors">
                        Book a Service
                    </a>
                </div>
            </div>

            <!-- Tire Finder Widget -->
            <div class="lg:col-span-5 w-full max-w-md mx-auto lg:ml-auto" x-data="{ tab: 'vehicle' }">
                <div class="bg-white rounded-md shadow-2xl overflow-hidden border-t-8 border-brand-red">
                    <div class="p-6 pb-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="font-heading text-3xl font-bold text-brand-black flex items-center gap-2">
                            <svg class="w-6 h-6 text-brand-red" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
                            FIND YOUR TIRES
                        </h2>
                    </div>
                    
                    <!-- Tabs -->
                    <div class="flex border-b border-gray-200">
                        <button @click="tab = 'vehicle'" :class="tab === 'vehicle' ? 'border-brand-red text-brand-red bg-white' : 'border-transparent text-gray-500 bg-gray-50 hover:text-gray-700'" class="flex-1 py-3 px-4 font-heading text-xl font-bold text-center border-b-2 transition-colors">
                            Shop by Vehicle
                        </button>
                        <button @click="tab = 'size'" :class="tab === 'size' ? 'border-brand-red text-brand-red bg-white' : 'border-transparent text-gray-500 bg-gray-50 hover:text-gray-700'" class="flex-1 py-3 px-4 font-heading text-xl font-bold text-center border-b-2 transition-colors">
                            Shop by Size
                        </button>
                    </div>

                    <!-- By Vehicle Form -->
                    <div x-show="tab === 'vehicle'" class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wide">Year</label>
                            <select class="w-full border border-gray-300 rounded-sm py-3 px-4 bg-white focus:outline-none focus:border-brand-red focus:ring-1 focus:ring-brand-red transition-colors text-gray-700">
                                <option value="">Select Year</option>
                                <option value="2024">2024</option>
                                <option value="2023">2023</option>
                                <option value="2022">2022</option>
                                <option value="2021">2021</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wide">Make</label>
                            <select disabled class="w-full border border-gray-300 rounded-sm py-3 px-4 bg-gray-50 text-gray-400 cursor-not-allowed">
                                <option value="">Select Make</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wide">Model</label>
                            <select disabled class="w-full border border-gray-300 rounded-sm py-3 px-4 bg-gray-50 text-gray-400 cursor-not-allowed">
                                <option value="">Select Model</option>
                            </select>
                        </div>
                        <button class="w-full bg-brand-red text-white py-4 font-heading text-2xl font-bold rounded-sm mt-2 hover:bg-brand-redhover transition-colors shadow-md">
                            SHOW TIRES
                        </button>
                    </div>

                    <!-- By Size Form -->
                    <div x-show="tab === 'size'" class="p-6 space-y-4" style="display: none;">
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Width</label>
                                <select class="w-full border border-gray-300 rounded-sm py-3 px-2 bg-white focus:outline-none focus:border-brand-red text-center">
                                    <option value="">215</option>
                                </select>
                            </div>
                            <div class="flex items-center text-2xl font-light text-gray-300 pt-4">/</div>
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Ratio</label>
                                <select class="w-full border border-gray-300 rounded-sm py-3 px-2 bg-white focus:outline-none focus:border-brand-red text-center">
                                    <option value="">55</option>
                                </select>
                            </div>
                            <div class="flex items-center text-2xl font-light text-gray-300 pt-4">R</div>
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Diameter</label>
                                <select class="w-full border border-gray-300 rounded-sm py-3 px-2 bg-white focus:outline-none focus:border-brand-red text-center">
                                    <option value="">17</option>
                                </select>
                            </div>
                        </div>
                        <div class="text-center pt-2 pb-2">
                            <span class="text-sm text-gray-500 font-medium">Or type your size:</span>
                            <input type="text" placeholder="e.g. 215/55R17" class="w-full mt-2 border border-gray-300 rounded-sm py-3 px-4 focus:outline-none focus:border-brand-red text-center font-bold">
                        </div>
                        <button class="w-full bg-brand-red text-white py-4 font-heading text-2xl font-bold rounded-sm hover:bg-brand-redhover transition-colors shadow-md">
                            SHOW TIRES
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- NEW VS USED SECTION (Dundalk's Advantage) -->
    <section class="py-20 bg-brand-gray border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="font-heading text-4xl md:text-5xl font-extrabold text-brand-black uppercase">THE DUNDALK ADVANTAGE</h2>
                <div class="w-20 h-1.5 bg-brand-red mx-auto mt-4 mb-6"></div>
                <p class="text-gray-600 max-w-2xl mx-auto text-lg">Whether you need maximum performance from top brands or budget-friendly reliability, we carry a massive inventory of both New and Used tires.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">
                <!-- New Tires -->
                <div class="bg-white p-8 md:p-10 rounded-sm shadow-sm border border-gray-100 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gray-50 rounded-bl-full -z-10 group-hover:bg-red-50 transition-colors"></div>
                    <h3 class="font-heading text-3xl font-bold text-brand-black mb-4 flex items-center gap-3">
                        <span class="w-3 h-8 bg-brand-red inline-block"></span> NEW TIRES
                    </h3>
                    <ul class="space-y-3 text-gray-600 mb-8 font-medium">
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Top industry brands available</li>
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Full manufacturer warranties</li>
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Professional installation included</li>
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Competitive, transparent pricing</li>
                    </ul>
                    <a href="#" class="text-brand-red font-bold uppercase tracking-wider hover:text-brand-redhover flex items-center gap-1">Shop New Tires <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                </div>

                <!-- Used Tires -->
                <div class="bg-brand-black text-white p-8 md:p-10 rounded-sm shadow-xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gray-800 rounded-bl-full -z-10 group-hover:bg-brand-red transition-colors"></div>
                    <h3 class="font-heading text-3xl font-bold mb-4 flex items-center gap-3">
                        <span class="w-3 h-8 bg-brand-red inline-block"></span> USED TIRES
                    </h3>
                    <ul class="space-y-3 text-gray-300 mb-8 font-medium">
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Cost-effective alternative</li>
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Rigorously hand-inspected for safety</li>
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Plenty of tread depth remaining</li>
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> High turnover - Inventory changes daily</li>
                    </ul>
                    <a href="#" class="text-brand-red font-bold uppercase tracking-wider hover:text-white transition-colors flex items-center gap-1">Check Availability <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                </div>
            </div>
        </div>
    </section>

    <!-- CORE SERVICES -->
    <section id="services" class="py-24 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12">
                <div>
                    <h2 class="font-heading text-4xl md:text-5xl font-extrabold text-brand-black uppercase">AUTO CARE & <span class="text-brand-red">SERVICES</span></h2>
                    <div class="w-20 h-1.5 bg-brand-red mt-4"></div>
                </div>
                <a href="#book" class="hidden md:inline-flex items-center gap-2 font-bold text-brand-red hover:text-brand-black transition-colors uppercase tracking-wider">
                    View All Services <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Alignments -->
                <div class="border border-gray-200 p-8 rounded-sm hover:border-brand-red transition-colors group cursor-pointer flex flex-col justify-between h-full bg-white shadow-sm hover:shadow-md">
                    <div>
                        <div class="w-12 h-12 bg-red-50 text-brand-red rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <h3 class="font-heading text-2xl font-bold mb-2">Wheel Alignment</h3>
                        <p class="text-gray-600 text-sm mb-6">Prevent uneven tire wear and steering pull with our precision computerized alignment.</p>
                    </div>
                    <span class="text-sm font-bold uppercase tracking-wider text-brand-red group-hover:text-brand-black transition-colors flex items-center gap-1">Learn More <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
                </div>

                <!-- Balancing -->
                <div class="border border-gray-200 p-8 rounded-sm hover:border-brand-red transition-colors group cursor-pointer flex flex-col justify-between h-full bg-white shadow-sm hover:shadow-md">
                    <div>
                        <div class="w-12 h-12 bg-red-50 text-brand-red rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="font-heading text-2xl font-bold mb-2">Wheel Balancing</h3>
                        <p class="text-gray-600 text-sm mb-6">Eliminate vibrations and enjoy a smooth highway ride with our dynamic balancing service.</p>
                    </div>
                    <span class="text-sm font-bold uppercase tracking-wider text-brand-red group-hover:text-brand-black transition-colors flex items-center gap-1">Learn More <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
                </div>

                <!-- Repair -->
                <div class="border border-gray-200 p-8 rounded-sm hover:border-brand-red transition-colors group cursor-pointer flex flex-col justify-between h-full bg-white shadow-sm hover:shadow-md">
                    <div>
                        <div class="w-12 h-12 bg-red-50 text-brand-red rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"/></svg>
                        </div>
                        <h3 class="font-heading text-2xl font-bold mb-2">Tire Repair</h3>
                        <p class="text-gray-600 text-sm mb-6">Got a flat? We inspect and perform safe, industry-standard plug and patch repairs.</p>
                    </div>
                    <span class="text-sm font-bold uppercase tracking-wider text-brand-red group-hover:text-brand-black transition-colors flex items-center gap-1">Learn More <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
                </div>

                <!-- Mechanics -->
                <div class="bg-brand-black text-white p-8 rounded-sm hover:bg-gray-900 transition-colors group cursor-pointer flex flex-col justify-between h-full border-b-4 border-brand-red shadow-lg">
                    <div>
                        <div class="w-12 h-12 bg-gray-800 text-brand-red rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <h3 class="font-heading text-2xl font-bold mb-2">Mechanical Services</h3>
                        <p class="text-gray-300 text-sm mb-6">Oil changes, brakes, diagnostics and full auto repair by experienced technicians.</p>
                    </div>
                    <span class="text-sm font-bold uppercase tracking-wider text-brand-red group-hover:text-white transition-colors flex items-center gap-1">Request Quote <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
                </div>
            </div>
        </div>
    </section>

    <!-- TIRE EDUCATION & TRUST -->
    <section class="py-24 bg-brand-black text-white tire-diagram">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div>
                    <h2 class="text-brand-red font-bold tracking-widest text-sm mb-2 uppercase">Help Center</h2>
                    <h3 class="font-heading text-4xl md:text-5xl font-extrabold mb-8">HOW TO READ <br>YOUR TIRE SIZE</h3>
                    <p class="text-gray-400 mb-8 text-lg leading-relaxed">Finding the right tire starts with knowing your size. You can find this information on the sidewall of your current tires or on the sticker inside your driver's side door.</p>
                    
                    <div class="bg-brand-dark p-6 rounded-md border border-gray-800">
                        <div class="text-4xl font-heading font-bold mb-6 text-center tracking-widest">
                            <span class="text-white">215</span><span class="text-gray-500">/</span><span class="text-brand-red">55</span><span class="text-gray-500">R</span><span class="text-blue-400">17</span>
                        </div>
                        <ul class="space-y-4 text-sm">
                            <li class="flex justify-between border-b border-gray-800 pb-2">
                                <span class="font-bold text-white">215</span>
                                <span class="text-gray-400">Width in millimeters</span>
                            </li>
                            <li class="flex justify-between border-b border-gray-800 pb-2">
                                <span class="font-bold text-brand-red">55</span>
                                <span class="text-gray-400">Aspect Ratio (Profile)</span>
                            </li>
                            <li class="flex justify-between border-b border-gray-800 pb-2">
                                <span class="font-bold text-gray-500">R</span>
                                <span class="text-gray-400">Radial Construction</span>
                            </li>
                            <li class="flex justify-between pb-2">
                                <span class="font-bold text-blue-400">17</span>
                                <span class="text-gray-400">Wheel Diameter in inches</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="relative">
                    <img src="${facadeImg}" alt="Dundalk Tires Shop" class="w-full h-[500px] object-cover rounded-sm grayscale opacity-80 border-b-8 border-brand-red">
                    <div class="absolute -bottom-8 -left-8 bg-brand-red p-8 hidden md:block shadow-2xl">
                        <div class="text-5xl font-heading font-extrabold mb-1">LOCAL & <br>TRUSTED</div>
                        <p class="text-white/90 font-medium">Serving the Dundalk community</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-white border-t border-gray-200 pt-20 pb-10">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                <!-- Brand -->
                <div>
                    <img src="${logoImg}" alt="Dundalk Tires" class="h-12 w-auto mb-6">
                    <p class="text-gray-600 mb-6 leading-relaxed">Professional tire services, alignment, and auto repair in Dundalk, MD. Your safety is our priority.</p>
                    <div class="flex gap-4">
                        <!-- Social placeholders -->
                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-500 hover:bg-brand-red hover:text-white transition-colors cursor-pointer"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg></div>
                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-500 hover:bg-brand-red hover:text-white transition-colors cursor-pointer"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg></div>
                    </div>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="font-heading text-xl font-bold mb-6">Contact Us</h4>
                    <ul class="space-y-4 text-gray-600">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-brand-red mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>7888 Dundalk Ave<br>Dundalk, MD 21222</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <a href="tel:6677273115" class="hover:text-brand-red font-bold">667-727-3115</a>
                        </li>
                    </ul>
                </div>

                <!-- Services Links -->
                <div>
                    <h4 class="font-heading text-xl font-bold mb-6">Services</h4>
                    <ul class="space-y-3 text-gray-600">
                        <li><a href="#" class="hover:text-brand-red transition-colors">Tire Installation</a></li>
                        <li><a href="#" class="hover:text-brand-red transition-colors">Wheel Alignment</a></li>
                        <li><a href="#" class="hover:text-brand-red transition-colors">Wheel Balancing</a></li>
                        <li><a href="#" class="hover:text-brand-red transition-colors">Tire Repair</a></li>
                        <li><a href="#" class="hover:text-brand-red transition-colors">Auto Repair</a></li>
                    </ul>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="font-heading text-xl font-bold mb-6">Tires</h4>
                    <ul class="space-y-3 text-gray-600">
                        <li><a href="#" class="hover:text-brand-red transition-colors">Shop New Tires</a></li>
                        <li><a href="#" class="hover:text-brand-red transition-colors">Used Tire Availability</a></li>
                        <li><a href="#" class="hover:text-brand-red transition-colors">Tire Brands</a></li>
                        <li><a href="#" class="hover:text-brand-red transition-colors">Tire Help Center</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-gray-500">
                <p>&copy; 2026 Dundalk Tires LLC. All rights reserved.</p>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-brand-red">Privacy Policy</a>
                    <a href="#" class="hover:text-brand-red">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Fixed Mobile Bottom Bar -->
    <div class="lg:hidden fixed bottom-0 w-full bg-white border-t border-gray-200 grid grid-cols-3 z-50 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
        <a href="tel:6677273115" class="flex flex-col items-center justify-center py-3 text-gray-700 hover:text-brand-red">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            <span class="text-[10px] font-bold uppercase">Call Now</span>
        </a>
        <a href="#" class="flex flex-col items-center justify-center py-3 text-gray-700 hover:text-brand-red border-l border-r border-gray-100">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span class="text-[10px] font-bold uppercase">Directions</span>
        </a>
        <a href="#book" class="flex flex-col items-center justify-center py-3 bg-brand-red text-white">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span class="text-[10px] font-bold uppercase">Book</span>
        </a>
    </div>

</body>
</html>`;

fs.writeFileSync(file, html);
