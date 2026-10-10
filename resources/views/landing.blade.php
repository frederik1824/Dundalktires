<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dundalk Used & New Tires | Premium Auto Care</title>
    <meta name="description" content="New and used tires, wheel alignment, balancing and auto repair in Dundalk, MD. Quality you can trust. 667-727-3115.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            red: '#dc2626', redhover: '#b91c1c',
                            black: '#111111', dark: '#1a1a1a', gray: '#f4f4f5',
                        }
                    },
                    fontFamily: {
                        heading: ['"Barlow Condensed"', 'sans-serif'],
                        body:    ['"Inter"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <script>
    // Pre-load server data before Alpine initializes
    window.__dundalkData = {
        tires:   @json($tires),
        filters: @json($filters),
    };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }
        h1, h2, h3, h4, .font-heading { font-family: 'Barlow Condensed', sans-serif; text-transform: uppercase; }
        .hero-bg {
            background-image: linear-gradient(to right, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.4) 100%),
                              url('{{ asset('negocio/WhatsApp Image 2026-09-28 at 10.10.55 AM.jpeg') }}');
            background-size: cover; background-position: center;
        }
        select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat; background-position: right 1rem center; background-size: 1em;
        }
        .tire-diagram { background-image: radial-gradient(#d1d5db 1px, transparent 1px); background-size: 20px 20px; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-white text-gray-900"
      x-data="{ mobileMenu: false, scrolled: false }"
      @scroll.window="scrolled = (window.pageYOffset > 20)">

    <!-- Top Bar -->
    <div class="bg-brand-black text-gray-300 text-xs py-2 hidden md:block">
        <div class="max-w-7xl mx-auto px-6 flex justify-between items-center">
            <div class="flex gap-6">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    7888 Dundalk Ave, Dundalk MD 21222
                </span>
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Mon – Sat: 8:00 AM – 6:00 PM
                </span>
            </div>
            <div class="flex gap-4">
                <a href="#book" class="hover:text-white transition-colors">Book Appointment</a>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav :class="{ 'py-3 shadow-lg bg-white/95 backdrop-blur-md': scrolled, 'py-5 bg-white': !scrolled }"
         class="sticky top-0 z-50 transition-all duration-300 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 flex justify-between items-center">
            <a href="#" class="flex-shrink-0 flex items-center gap-3">
                <img src="{{ asset('Logo Dundalk.png') }}" alt="Dundalk Tires" class="h-10 w-auto">
            </a>
            <div class="hidden lg:flex items-center gap-8 font-heading text-lg font-bold tracking-wide">
                <a href="#tires"     class="hover:text-brand-red transition-colors">Tires</a>
                <a href="#services"  class="hover:text-brand-red transition-colors">Services</a>
                <a href="#alignment" class="hover:text-brand-red transition-colors">Wheel Alignment</a>
                <a href="#about"     class="hover:text-brand-red transition-colors">About Us</a>
            </div>
            <div class="hidden lg:flex items-center gap-6">
                <a href="tel:6677273115" class="flex items-center gap-2 font-bold text-lg hover:text-brand-red transition-colors">
                    <svg class="w-5 h-5 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    667-727-3115
                </a>
                <a href="#book" class="bg-brand-red text-white px-6 py-2.5 font-heading text-xl font-bold rounded-sm shadow-md hover:bg-brand-redhover hover:-translate-y-0.5 transition-all">
                    Book Service
                </a>
            </div>
            <button @click="mobileMenu = !mobileMenu" class="lg:hidden p-2 text-brand-black">
                <svg x-show="!mobileMenu" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="mobileMenu" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div x-show="mobileMenu" x-cloak class="lg:hidden bg-white border-t border-gray-100 absolute w-full left-0 shadow-xl">
            <div class="px-6 py-4 flex flex-col gap-4 font-heading text-xl font-bold">
                <a href="#tires"     class="block py-2 border-b border-gray-50" @click="mobileMenu=false">Tires</a>
                <a href="#services"  class="block py-2 border-b border-gray-50" @click="mobileMenu=false">Services</a>
                <a href="#alignment" class="block py-2 border-b border-gray-50" @click="mobileMenu=false">Wheel Alignment</a>
                <a href="#about"     class="block py-2 border-b border-gray-50" @click="mobileMenu=false">About Us</a>
                <a href="tel:6677273115" class="flex items-center gap-2 py-2 text-brand-red">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    667-727-3115
                </a>
                <a href="#book" @click="mobileMenu=false" class="block text-center bg-brand-red text-white py-3 rounded-sm mt-2">Book Service</a>
            </div>
        </div>
    </nav>

    <!-- HERO + TIRE FINDER -->
    <header class="relative w-full min-h-[600px] lg:min-h-[700px] flex items-center pt-8 pb-16 lg:py-0 hero-bg">
        <div class="max-w-7xl mx-auto px-6 w-full grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-7 text-white">
                <div class="inline-block bg-brand-red px-4 py-1 font-heading font-bold text-lg mb-6 tracking-wide uppercase">New & Used Tires</div>
                <h1 class="text-5xl md:text-7xl font-extrabold mb-6 leading-[1.05] tracking-wide">
                    FIND THE RIGHT TIRES.<br><span class="text-gray-400">GET BACK ON THE ROAD.</span>
                </h1>
                <p class="text-lg md:text-xl text-gray-300 font-body mb-8 max-w-lg leading-relaxed">
                    Professional installation, precision alignment, balancing, and full mechanical services in Dundalk. Quality you can trust.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#book" class="bg-white text-brand-black px-8 py-3.5 font-heading text-xl font-bold rounded-sm shadow-lg hover:bg-gray-100 transition-colors">
                        Book a Service
                    </a>
                    <a href="#tires" class="border-2 border-white text-white px-8 py-3.5 font-heading text-xl font-bold rounded-sm hover:bg-white hover:text-brand-black transition-colors">
                        View Inventory
                    </a>
                </div>
            </div>

            <!-- Tire Finder Widget -->
            <div class="lg:col-span-5 w-full max-w-md mx-auto lg:ml-auto"
                 x-data="tireFinder"
                 x-init="init()">
                <div class="bg-white rounded-md shadow-2xl overflow-hidden border-t-8 border-brand-red">
                    <div class="p-6 pb-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="font-heading text-3xl font-bold text-brand-black flex items-center gap-2">
                            <svg class="w-6 h-6 text-brand-red" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
                            FIND YOUR TIRES
                        </h2>
                    </div>

                    <!-- Tabs -->
                    <div class="flex border-b border-gray-200">
                        <button @click="tab = 'vehicle'"
                                :class="tab === 'vehicle' ? 'border-brand-red text-brand-red bg-white' : 'border-transparent text-gray-500 bg-gray-50 hover:text-gray-700'"
                                class="flex-1 py-3 px-4 font-heading text-xl font-bold text-center border-b-2 transition-colors">
                            By Vehicle
                        </button>
                        <button @click="tab = 'size'"
                                :class="tab === 'size' ? 'border-brand-red text-brand-red bg-white' : 'border-transparent text-gray-500 bg-gray-50 hover:text-gray-700'"
                                class="flex-1 py-3 px-4 font-heading text-xl font-bold text-center border-b-2 transition-colors">
                            By Size
                        </button>
                    </div>

                    <!-- By Vehicle -->
                    <div x-show="tab === 'vehicle'" class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wide">Year</label>
                            <select x-model="vehicle.year" @change="onYearChange()"
                                    class="w-full border border-gray-300 rounded-sm py-3 px-4 bg-white focus:outline-none focus:border-brand-red focus:ring-1 focus:ring-brand-red text-gray-700">
                                <option value="">Select Year</option>
                                <template x-for="y in years" :key="y">
                                    <option :value="y" x-text="y"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wide">Make</label>
                            <select x-model="vehicle.make" @change="onMakeChange()"
                                    :disabled="!vehicle.year || makes.length === 0"
                                    :class="(!vehicle.year || makes.length === 0) ? 'bg-gray-50 text-gray-400 cursor-not-allowed' : 'bg-white'"
                                    class="w-full border border-gray-300 rounded-sm py-3 px-4 focus:outline-none focus:border-brand-red text-gray-700">
                                <option value="">Select Make</option>
                                <template x-for="m in makes" :key="m">
                                    <option :value="m" x-text="m"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wide">Model</label>
                            <select x-model="vehicle.model"
                                    :disabled="!vehicle.make || models.length === 0"
                                    :class="(!vehicle.make || models.length === 0) ? 'bg-gray-50 text-gray-400 cursor-not-allowed' : 'bg-white'"
                                    class="w-full border border-gray-300 rounded-sm py-3 px-4 focus:outline-none focus:border-brand-red text-gray-700">
                                <option value="">Select Model</option>
                                <template x-for="m in models" :key="m">
                                    <option :value="m" x-text="m"></option>
                                </template>
                            </select>
                        </div>
                        <button @click="searchByVehicle()" :disabled="!vehicle.make"
                                :class="vehicle.make ? 'bg-brand-red hover:bg-brand-redhover' : 'bg-gray-400 cursor-not-allowed'"
                                class="w-full text-white py-4 font-heading text-2xl font-bold rounded-sm mt-2 transition-colors shadow-md">
                            SHOW TIRES
                        </button>
                    </div>

                    <!-- By Size -->
                    <div x-show="tab === 'size'" x-cloak class="p-6 space-y-4">
                        <div class="flex gap-2 items-end">
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Width</label>
                                <select x-model="size.width"
                                        class="w-full border border-gray-300 rounded-sm py-3 px-2 bg-white focus:outline-none focus:border-brand-red text-center">
                                    <option value="">W</option>
                                    @foreach($filters['widths'] as $w)
                                        <option value="{{ $w }}">{{ $w }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-center text-2xl font-light text-gray-300 pb-3">/</div>
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Ratio</label>
                                <select x-model="size.ratio"
                                        class="w-full border border-gray-300 rounded-sm py-3 px-2 bg-white focus:outline-none focus:border-brand-red text-center">
                                    <option value="">R</option>
                                    @foreach($filters['ratios'] as $r)
                                        <option value="{{ $r }}">{{ $r }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-center text-2xl font-light text-gray-300 pb-3">R</div>
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Rim</label>
                                <select x-model="size.diameter"
                                        class="w-full border border-gray-300 rounded-sm py-3 px-2 bg-white focus:outline-none focus:border-brand-red text-center">
                                    <option value="">D</option>
                                    @foreach($filters['diameters'] as $d)
                                        <option value="{{ $d }}">{{ $d }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="text-center pt-1 pb-1">
                            <span class="text-sm text-gray-500 font-medium">Or type your size:</span>
                            <input type="text" x-model="size.manual"
                                   placeholder="e.g. 215/55R17"
                                   class="w-full mt-2 border border-gray-300 rounded-sm py-3 px-4 focus:outline-none focus:border-brand-red text-center font-bold">
                        </div>
                        <button @click="searchBySize()"
                                class="w-full bg-brand-red text-white py-4 font-heading text-2xl font-bold rounded-sm hover:bg-brand-redhover transition-colors shadow-md">
                            SHOW TIRES
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- BRANDS -->
    <section class="py-12 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6">
            <p class="text-center text-sm font-bold text-gray-400 uppercase tracking-widest mb-8">We carry all major brands</p>
            <div class="flex flex-wrap justify-center gap-8 md:gap-16 opacity-60">
                @foreach($filters['brands'] as $brand)
                    <div class="text-2xl font-bold font-heading tracking-wider text-gray-500 hover:text-brand-red hover:opacity-100 transition-all cursor-default">{{ strtoupper($brand) }}</div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- NEW VS USED -->
    <section class="py-20 bg-brand-gray border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="font-heading text-4xl md:text-5xl font-extrabold text-brand-black uppercase">THE DUNDALK ADVANTAGE</h2>
                <div class="w-20 h-1.5 bg-brand-red mx-auto mt-4 mb-6"></div>
                <p class="text-gray-600 max-w-2xl mx-auto text-lg">Whether you need maximum performance from top brands or budget-friendly reliability, we carry a massive inventory of both New and Used tires.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">
                <div class="bg-white p-8 md:p-10 rounded-sm shadow-sm border border-gray-100 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gray-50 rounded-bl-full group-hover:bg-red-50 transition-colors"></div>
                    <h3 class="font-heading text-3xl font-bold text-brand-black mb-4 flex items-center gap-3">
                        <span class="w-3 h-8 bg-brand-red inline-block"></span> NEW TIRES
                    </h3>
                    <ul class="space-y-3 text-gray-600 mb-8 font-medium">
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Top industry brands available</li>
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Full manufacturer warranties</li>
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Professional installation included</li>
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Competitive, transparent pricing</li>
                    </ul>
                    <a href="#tires" class="text-brand-red font-bold uppercase tracking-wider hover:text-brand-redhover flex items-center gap-1">Shop New Tires <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                </div>
                <div class="bg-brand-black text-white p-8 md:p-10 rounded-sm shadow-xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gray-800 rounded-bl-full group-hover:bg-brand-red transition-colors"></div>
                    <h3 class="font-heading text-3xl font-bold mb-4 flex items-center gap-3">
                        <span class="w-3 h-8 bg-brand-red inline-block"></span> USED TIRES
                    </h3>
                    <ul class="space-y-3 text-gray-300 mb-8 font-medium">
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-brand-red flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Cost-effective alternative</li>
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-brand-red flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Rigorously hand-inspected for safety</li>
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-brand-red flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Plenty of tread depth remaining</li>
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-brand-red flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> High turnover — Inventory changes daily</li>
                    </ul>
                    <a href="#tires" class="text-brand-red font-bold uppercase tracking-wider hover:text-white transition-colors flex items-center gap-1">Check Availability <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                </div>
            </div>
        </div>
    </section>

    <!-- INVENTORY -->
    <section id="tires" class="py-24 bg-gray-50"
             x-data="inventoryGrid()"
             x-init="boot()">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-end mb-8">
                <div>
                    <h2 class="font-heading text-4xl md:text-5xl font-extrabold text-brand-black uppercase">
                        CURRENT <span class="text-brand-red">INVENTORY</span>
                    </h2>
                    <div class="w-20 h-1.5 bg-brand-red mt-4"></div>
                    <p class="text-gray-500 mt-2 text-sm" x-text="meta.total + ' tires available'"></p>
                </div>
                <div class="mt-6 md:mt-0 flex gap-3 flex-wrap">
                    <button @click="filterByType(null)"
                            :class="activeType === null ? 'bg-brand-black text-white' : 'border-2 border-brand-black text-brand-black hover:bg-brand-black hover:text-white'"
                            class="px-5 py-2 font-bold uppercase text-sm transition-colors">All</button>
                    <button @click="filterByType('new')"
                            :class="activeType === 'new' ? 'bg-brand-black text-white' : 'border-2 border-brand-black text-brand-black hover:bg-brand-black hover:text-white'"
                            class="px-5 py-2 font-bold uppercase text-sm transition-colors">New</button>
                    <button @click="filterByType('used')"
                            :class="activeType === 'used' ? 'bg-brand-black text-white' : 'border-2 border-brand-black text-brand-black hover:bg-brand-black hover:text-white'"
                            class="px-5 py-2 font-bold uppercase text-sm transition-colors">Used</button>
                </div>
            </div>

            <!-- Brand filters -->
            <div class="flex flex-wrap gap-2 mb-8">
                <button @click="filterByBrand(null)"
                        :class="activeBrand === null ? 'bg-brand-red text-white' : 'bg-white border border-gray-300 text-gray-600 hover:border-brand-red hover:text-brand-red'"
                        class="px-4 py-1.5 rounded-full text-sm font-bold transition-colors">All Brands</button>
                <template x-for="brand in serverFilters.brands" :key="brand">
                    <button @click="filterByBrand(brand)"
                            :class="activeBrand === brand ? 'bg-brand-red text-white' : 'bg-white border border-gray-300 text-gray-600 hover:border-brand-red hover:text-brand-red'"
                            class="px-4 py-1.5 rounded-full text-sm font-bold transition-colors"
                            x-text="brand"></button>
                </template>
            </div>

            <!-- Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 min-h-[200px]">
                <!-- Loading -->
                <template x-if="loading">
                    <div class="col-span-full py-16 text-center text-gray-400">
                        <svg class="w-10 h-10 mx-auto mb-3 animate-spin text-brand-red" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Loading inventory...
                    </div>
                </template>

                <!-- Tire cards -->
                <template x-if="!loading">
                    <template x-for="tire in tires" :key="tire.id">
                        <div class="bg-white border border-gray-200 p-6 rounded-md shadow-sm hover:shadow-xl transition-all group relative"
                             :class="tire.is_featured ? 'ring-2 ring-brand-red' : ''">
                            <div class="absolute top-6 left-6 z-10 flex gap-2">
                                <span class="bg-green-500 text-white text-xs font-bold px-3 py-1 uppercase tracking-wider rounded-sm">In Stock</span>
                                <span x-show="tire.is_featured" class="bg-brand-red text-white text-xs font-bold px-2 py-1 rounded-sm flex items-center gap-1">
                                    ★ Featured
                                </span>
                            </div>
                            <div class="aspect-square bg-gray-100 rounded-sm mb-6 flex items-center justify-center p-8 overflow-hidden">
                                <svg viewBox="0 0 100 100" class="w-full h-full opacity-20">
                                    <circle cx="50" cy="50" r="46" fill="none" stroke="#374151" stroke-width="8"/>
                                    <circle cx="50" cy="50" r="28" fill="none" stroke="#374151" stroke-width="6"/>
                                    <circle cx="50" cy="50" r="12" fill="#374151"/>
                                </svg>
                            </div>
                            <div class="mb-2">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest" x-text="tire.tire_brand"></span>
                            </div>
                            <h3 class="text-2xl font-heading font-bold text-gray-900 mb-1" x-text="tire.tire_line || tire.name"></h3>
                            <p class="text-brand-red font-bold font-mono text-lg mb-1"
                               x-text="tire.tire_width + '/' + tire.tire_ratio + 'R' + tire.tire_diameter"></p>
                            <p class="text-xs text-gray-400 uppercase tracking-wider mb-4"
                               x-text="tire.tire_type ? tire.tire_type.replace('_', ' ') : ''"></p>
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <div>
                                    <span class="text-xs text-gray-400 block mb-1">Starting at</span>
                                    <span class="text-2xl font-bold text-gray-900"
                                          x-text="'$' + parseFloat(tire.sale_price).toFixed(2)"></span>
                                </div>
                                <a href="#book"
                                   @click="prefillService('Tire Installation (New or Used)', tire.tire_width + '/' + tire.tire_ratio + 'R' + tire.tire_diameter)"
                                   class="bg-brand-black text-white px-4 py-2 text-sm font-bold uppercase hover:bg-brand-red transition-colors rounded-sm">
                                    Get Quote
                                </a>
                            </div>
                        </div>
                    </template>
                </template>

                <!-- Empty state -->
                <template x-if="!loading && tires.length === 0">
                    <div class="col-span-full py-16 text-center bg-gray-100 rounded-md border-2 border-dashed border-gray-300">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <h3 class="text-2xl font-heading font-bold text-gray-900 mb-2">No tires found for that search</h3>
                        <p class="text-gray-500 mb-6">We have hundreds of tires in our local warehouse not listed here.</p>
                        <div class="flex justify-center gap-4">
                            <button @click="clearSearch()" class="px-6 py-2 border border-gray-300 font-bold uppercase text-sm hover:bg-gray-200 transition-colors">Clear Search</button>
                            <a href="tel:6677273115" class="px-6 py-2 bg-brand-black text-white font-bold uppercase text-sm hover:bg-brand-red transition-colors">Call Us to Check</a>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Pagination -->
            <div class="mt-10 flex justify-center gap-2" x-show="meta.last_page > 1">
                <template x-for="p in meta.last_page" :key="p">
                    <button @click="goToPage(p)"
                            :class="p === meta.current_page ? 'bg-brand-red text-white' : 'bg-white border border-gray-300 text-gray-700 hover:border-brand-red'"
                            class="w-10 h-10 font-bold text-sm transition-colors rounded-sm"
                            x-text="p"></button>
                </template>
            </div>

            <div class="mt-12 text-center bg-white border border-gray-200 p-6 rounded-md">
                <p class="text-gray-600 font-medium">Looking for a specific tire?
                    <a href="tel:6677273115" class="text-brand-red font-bold ml-2 underline decoration-2 underline-offset-4 hover:text-brand-black transition-colors">Call us to check live stock</a>
                </p>
            </div>
        </div>
    </section>

    <!-- SERVICES -->
    <section id="services" class="py-24 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12">
                <div>
                    <h2 class="font-heading text-4xl md:text-5xl font-extrabold text-brand-black uppercase">AUTO CARE & <span class="text-brand-red">SERVICES</span></h2>
                    <div class="w-20 h-1.5 bg-brand-red mt-4"></div>
                </div>
                <a href="#book" class="hidden md:inline-flex items-center gap-2 font-bold text-brand-red hover:text-brand-black transition-colors uppercase tracking-wider mt-6 md:mt-0">
                    Book a Service <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="border border-gray-200 p-8 rounded-sm hover:border-brand-red transition-colors group cursor-pointer flex flex-col justify-between h-full bg-white shadow-sm hover:shadow-md">
                    <div>
                        <div class="w-12 h-12 bg-red-50 text-brand-red rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <h3 class="font-heading text-2xl font-bold mb-2">Wheel Alignment</h3>
                        <p class="text-gray-600 text-sm mb-6">Prevent uneven tire wear and steering pull with our precision computerized alignment.</p>
                    </div>
                    <a href="#alignment" class="text-sm font-bold uppercase tracking-wider text-brand-red group-hover:text-brand-black transition-colors flex items-center gap-1">Learn More <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                </div>
                <div class="border border-gray-200 p-8 rounded-sm hover:border-brand-red transition-colors group cursor-pointer flex flex-col justify-between h-full bg-white shadow-sm hover:shadow-md">
                    <div>
                        <div class="w-12 h-12 bg-red-50 text-brand-red rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="font-heading text-2xl font-bold mb-2">Wheel Balancing</h3>
                        <p class="text-gray-600 text-sm mb-6">Eliminate vibrations and enjoy a smooth highway ride with our dynamic balancing service.</p>
                    </div>
                    <a href="#book" class="text-sm font-bold uppercase tracking-wider text-brand-red group-hover:text-brand-black transition-colors flex items-center gap-1">Book Now <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                </div>
                <div class="border border-gray-200 p-8 rounded-sm hover:border-brand-red transition-colors group cursor-pointer flex flex-col justify-between h-full bg-white shadow-sm hover:shadow-md">
                    <div>
                        <div class="w-12 h-12 bg-red-50 text-brand-red rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"/></svg>
                        </div>
                        <h3 class="font-heading text-2xl font-bold mb-2">Tire Repair</h3>
                        <p class="text-gray-600 text-sm mb-6">Got a flat? We inspect and perform safe, industry-standard plug and patch repairs.</p>
                    </div>
                    <a href="#book" class="text-sm font-bold uppercase tracking-wider text-brand-red group-hover:text-brand-black transition-colors flex items-center gap-1">Book Now <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                </div>
                <div class="bg-brand-black text-white p-8 rounded-sm hover:bg-gray-900 transition-colors group cursor-pointer flex flex-col justify-between h-full border-b-4 border-brand-red shadow-lg">
                    <div>
                        <div class="w-12 h-12 bg-gray-800 text-brand-red rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <h3 class="font-heading text-2xl font-bold mb-2">Mechanical Services</h3>
                        <p class="text-gray-300 text-sm mb-6">Oil changes, brakes, diagnostics and full auto repair by experienced technicians.</p>
                    </div>
                    <a href="#book" class="text-sm font-bold uppercase tracking-wider text-brand-red group-hover:text-white transition-colors flex items-center gap-1">Request Quote <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                </div>
            </div>
        </div>
    </section>

    <!-- ALIGNMENT -->
    <section id="alignment" class="py-24 bg-white border-b border-gray-100 overflow-hidden">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-center">
                <div>
                    <p class="text-brand-red font-bold tracking-widest text-sm mb-2 uppercase">Specialized Service</p>
                    <h3 class="font-heading text-4xl md:text-5xl font-extrabold mb-6 text-brand-black">PRECISION WHEEL <br>ALIGNMENT</h3>
                    <p class="text-gray-600 mb-8 text-lg leading-relaxed">Proper alignment is crucial for your safety, tire longevity, and fuel efficiency. Our state-of-the-art computerized alignment system ensures your wheels are perfectly calibrated to factory specifications.</p>
                    <div class="space-y-6 mb-10">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-red-50 text-brand-red rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-brand-black mb-1">When do you need it?</h4>
                                <p class="text-sm text-gray-500">If your car pulls to one side, your steering wheel vibrates, or your tires are wearing unevenly.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-red-50 text-brand-red rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-brand-black mb-1">The Benefits</h4>
                                <p class="text-sm text-gray-500">Maximize tire life, improve fuel economy, and enjoy a smoother, safer ride.</p>
                            </div>
                        </div>
                    </div>
                    <a href="#book" class="inline-block bg-brand-red text-white px-8 py-3.5 font-heading text-xl font-bold rounded-sm shadow-md hover:bg-brand-redhover transition-colors">Book Alignment</a>
                </div>
                <div class="relative mt-8 lg:mt-0">
                    <div class="absolute inset-0 bg-brand-red transform translate-x-4 translate-y-4 rounded-sm -z-10"></div>
                    <img src="{{ asset('negocio/WhatsApp Image 2026-09-28 at 10.10.57 AM.jpeg') }}"
                         alt="Wheel alignment service at Dundalk Tires"
                         class="w-full h-96 object-cover rounded-sm shadow-2xl relative z-10">
                    <div class="absolute top-8 -left-8 bg-brand-black text-white px-6 py-4 shadow-2xl z-20 rounded-sm">
                        <div class="text-xs uppercase tracking-widest text-brand-red font-bold mb-1">System Status</div>
                        <div class="font-mono text-xl font-bold flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse"></span> CALIBRATED
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- BALANCING & REPAIR -->
    <section id="balancing-repair" class="grid grid-cols-1 md:grid-cols-2">
        <div class="bg-brand-black text-white py-20 px-6 md:px-16 flex flex-col justify-center">
            <p class="text-brand-red font-bold tracking-widest text-sm mb-2 uppercase">Dynamic Balancing</p>
            <h3 class="font-heading text-4xl font-extrabold mb-6">ELIMINATE <br>VIBRATIONS</h3>
            <p class="text-gray-400 mb-6 leading-relaxed">If you feel a vibration in your steering wheel at highway speeds, your tires are likely out of balance. We use precision dynamic balancing machines to distribute weight perfectly across the wheel assembly.</p>
            <ul class="space-y-3 text-sm text-gray-300 mb-8 font-medium">
                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-brand-red flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Smoother highway driving</li>
                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-brand-red flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Prevents premature suspension wear</li>
            </ul>
            <a href="#book" class="inline-block border-2 border-brand-red text-brand-red font-bold uppercase tracking-wider text-sm px-8 py-3 hover:bg-brand-red hover:text-white transition-colors rounded-sm w-fit">Book Balancing</a>
        </div>
        <div class="bg-brand-gray py-20 px-6 md:px-16 flex flex-col justify-center border-l border-gray-200">
            <p class="text-brand-red font-bold tracking-widest text-sm mb-2 uppercase">Safe Flat Repair</p>
            <h3 class="font-heading text-4xl font-extrabold mb-6 text-brand-black">PROFESSIONAL <br>PATCHING</h3>
            <p class="text-gray-600 mb-6 leading-relaxed">Not all flat tires need to be replaced. If a puncture is within the central tread area, our experts can safely patch it from the inside out, strictly following industry safety standards.</p>
            <ul class="space-y-3 text-sm text-gray-600 mb-8 font-medium">
                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Fast 15-minute turnaround</li>
                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Safer than temporary plugs</li>
            </ul>
            <a href="#book" class="inline-block bg-brand-black text-white font-bold uppercase tracking-wider text-sm px-8 py-3 hover:bg-brand-red transition-colors rounded-sm w-fit">Book Repair</a>
        </div>
    </section>

    <!-- SPECIAL OFFERS -->
    @if($promotions->isNotEmpty())
    <section class="py-20 bg-brand-red text-white relative overflow-hidden">
        <div class="absolute right-0 top-0 opacity-10 pointer-events-none transform translate-x-1/4 -translate-y-1/4">
            <svg class="w-96 h-96" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        </div>
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="text-center mb-12">
                <h2 class="font-heading text-4xl md:text-5xl font-extrabold uppercase mb-4">CURRENT <span class="text-brand-black">OFFERS</span></h2>
                <p class="text-white/80 font-medium text-lg">Save more on your next visit to Dundalk Tires.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach($promotions as $promo)
                <div class="bg-white/10 backdrop-blur-sm border border-white/20 p-8 md:p-10 rounded-sm hover:bg-white/20 transition-colors">
                    @if($promo->badge_text)
                    <div class="bg-brand-black text-white text-xs font-bold uppercase tracking-widest px-3 py-1 inline-block mb-4 rounded-sm">{{ $promo->badge_text }}</div>
                    @endif
                    <h3 class="text-3xl font-heading font-bold mb-4">{{ $promo->title }}</h3>
                    @if($promo->description)
                    <p class="text-white/90 mb-8 font-light leading-relaxed">{{ $promo->description }}</p>
                    @endif
                    <a href="#book" class="inline-block border-2 border-white text-white font-bold uppercase tracking-widest text-sm px-8 py-3 hover:bg-white hover:text-brand-red transition-colors rounded-sm">{{ $promo->cta_text }}</a>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- ABOUT / VIDEO -->
    <section id="about" class="py-24 bg-brand-black text-white tire-diagram">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div>
                    <p class="text-brand-red font-bold tracking-widest text-sm mb-2 uppercase">Help Center</p>
                    <h3 class="font-heading text-4xl md:text-5xl font-extrabold mb-8">HOW TO READ <br>YOUR TIRE SIZE</h3>
                    <p class="text-gray-400 mb-8 text-lg leading-relaxed">Finding the right tire starts with knowing your size. You can find this on the sidewall of your current tires or on the sticker inside your driver's side door.</p>
                    <div class="bg-brand-dark p-6 rounded-md border border-gray-800">
                        <div class="text-4xl font-heading font-bold mb-6 text-center tracking-widest">
                            <span class="text-white">215</span><span class="text-gray-500">/</span><span class="text-brand-red">55</span><span class="text-gray-500">R</span><span class="text-blue-400">17</span>
                        </div>
                        <ul class="space-y-4 text-sm">
                            <li class="flex justify-between border-b border-gray-800 pb-2"><span class="font-bold text-white">215</span><span class="text-gray-400">Width in millimeters</span></li>
                            <li class="flex justify-between border-b border-gray-800 pb-2"><span class="font-bold text-brand-red">55</span><span class="text-gray-400">Aspect Ratio (Profile)</span></li>
                            <li class="flex justify-between border-b border-gray-800 pb-2"><span class="font-bold text-gray-500">R</span><span class="text-gray-400">Radial Construction</span></li>
                            <li class="flex justify-between pb-2"><span class="font-bold text-blue-400">17</span><span class="text-gray-400">Wheel Diameter in inches</span></li>
                        </ul>
                    </div>
                </div>
                <div class="relative shadow-xl rounded-sm">
                    <video src="{{ asset('negocio/Dundalk-Corporate-Commercial-30s.mp4') }}"
                           controls preload="metadata"
                           class="w-full h-[500px] object-cover rounded-sm border-b-8 border-brand-red bg-gray-900"></video>
                    <div class="absolute -bottom-8 -left-8 bg-brand-red p-8 hidden md:block shadow-2xl pointer-events-none z-10">
                        <div class="text-5xl font-heading font-extrabold mb-1">LOCAL & <br>TRUSTED</div>
                        <p class="text-white/90 font-medium">Serving the Dundalk community</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- REVIEWS -->
    <section class="py-24 bg-gray-50 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <div class="flex justify-center items-center gap-1 mb-4">
                    @for($i = 0; $i < 5; $i++)
                        <svg class="w-8 h-8 text-yellow-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                    @endfor
                </div>
                <h2 class="font-heading text-4xl md:text-5xl font-extrabold text-brand-black uppercase">WHAT OUR <span class="text-brand-red">CUSTOMERS SAY</span></h2>
                <div class="w-20 h-1.5 bg-brand-red mx-auto mt-4"></div>
                @if($testimonials->isNotEmpty())
                <p class="text-gray-500 mt-4">
                    Rated <strong class="text-brand-black">{{ number_format($testimonials->avg('rating'), 1) }} / 5</strong>
                    based on {{ $testimonials->count() }} verified Google reviews
                </p>
                @endif
            </div>

            @php
                /* Use DB testimonials if available, else show placeholder cards */
                $showTestimonials = $testimonials->isNotEmpty();
                $fallback = [
                    ['text' => '"Got a flat on my way to work. They patched it up in under 15 minutes. Very professional, no upselling, just honest mechanics. Highly recommend this local shop."', 'author' => 'Mark D.', 'rating' => 5],
                    ['text' => '"Bought a pair of used tires for my Honda. They looked practically brand new and the price was unbeatable. Installation was fast and they balanced everything perfectly."', 'author' => 'Sarah T.', 'rating' => 5],
                    ['text' => '"Best wheel alignment in Dundalk. The car drives straight as an arrow now. The facility is clean and the guys at the front desk are super friendly."', 'author' => 'Kevin R.', 'rating' => 5],
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @if($showTestimonials)
                    @foreach($testimonials as $review)
                    <div class="bg-white p-8 border border-gray-200 rounded-sm shadow-sm relative flex flex-col">
                        <!-- Google G badge -->
                        <div class="absolute top-5 right-5 w-7 h-7 rounded-full bg-white border border-gray-200 shadow-sm flex items-center justify-center">
                            <span class="text-sm font-black" style="color:#4285F4">G</span>
                        </div>
                        <!-- Stars -->
                        <div class="flex gap-0.5 text-yellow-400 mb-4">
                            @for($i = 0; $i < $review->rating; $i++)<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>@endfor
                            @for($i = $review->rating; $i < 5; $i++)<svg class="w-4 h-4 text-gray-200" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>@endfor
                        </div>
                        <p class="text-gray-600 italic leading-relaxed relative z-10 flex-1">"{{ $review->body }}"</p>
                        <!-- Author -->
                        <div class="flex items-center gap-3 mt-6 pt-4 border-t border-gray-100">
                            <div class="w-9 h-9 rounded-full bg-red-600 flex items-center justify-center text-white text-xs font-bold shrink-0">
                                {{ $review->initials }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 text-sm">{{ $review->author_name }}</p>
                                @if($review->reviewed_at)
                                <p class="text-xs text-gray-400">{{ $review->reviewed_at->format('M Y') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    @foreach($fallback as $review)
                    <div class="bg-white p-8 border border-gray-200 rounded-sm shadow-sm relative">
                        <svg class="w-10 h-10 text-gray-200 absolute top-6 right-6" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                        <div class="flex gap-1 text-yellow-400 mb-4">
                            @for($i = 0; $i < 5; $i++)<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>@endfor
                        </div>
                        <p class="text-gray-600 mb-6 italic leading-relaxed relative z-10">{{ $review['text'] }}</p>
                        <div class="font-bold text-gray-900">— {{ $review['author'] }}</div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="py-24 bg-white border-t border-gray-100">
        <div class="max-w-4xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="font-heading text-4xl font-extrabold text-brand-black uppercase">FREQUENTLY ASKED <span class="text-brand-red">QUESTIONS</span></h2>
                <div class="w-16 h-1.5 bg-brand-red mx-auto mt-4"></div>
            </div>
            <div class="space-y-4" x-data="{ active: null }">
                @php $faqs = [
                    ['q' => 'How long does a tire installation take?', 'a' => 'Usually, a full set of 4 tires takes about 45 to 60 minutes to mount, balance, and install on your vehicle. Single tire replacements or simple flat repairs are often completed in under 20 minutes!'],
                    ['q' => 'Do you offer warranties on used tires?', 'a' => 'Yes! All our used tires are rigorously hand-inspected for safety, tread depth, and structural integrity. We stand behind our used inventory and offer a 30-day guarantee against any structural defects.'],
                    ['q' => 'Do I need an appointment?', 'a' => 'Walk-ins are always welcome! However, booking an appointment online or calling ahead guarantees your time slot and significantly reduces any potential waiting time, especially for wheel alignments.'],
                    ['q' => 'What tire brands do you carry?', 'a' => 'We carry all major brands including Michelin, Goodyear, Bridgestone, Continental, Firestone, Pirelli, Cooper, Hankook, and many more. Call us for current availability.'],
                ]; @endphp
                @foreach($faqs as $i => $faq)
                <div class="border border-gray-200 rounded-sm">
                    <button @click="active = active === {{ $i }} ? null : {{ $i }}"
                            class="w-full flex justify-between items-center p-6 text-left focus:outline-none hover:bg-gray-50 transition-colors">
                        <span class="font-bold text-gray-900 text-lg">{{ $faq['q'] }}</span>
                        <svg class="w-6 h-6 text-brand-red transform transition-transform flex-shrink-0 ml-4"
                             :class="active === {{ $i }} ? 'rotate-180' : ''"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="active === {{ $i }}" x-cloak class="px-6 pb-6 text-gray-600">
                        {{ $faq['a'] }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- BOOK APPOINTMENT -->
    <section id="book" class="py-24 bg-white relative"
             x-data="bookingForm"
             x-init="init()">
        <div class="max-w-7xl mx-auto px-6">
            <div class="bg-brand-black rounded-sm shadow-2xl overflow-hidden flex flex-col lg:flex-row">
                <!-- Left Info Panel -->
                <div class="lg:w-1/3 bg-brand-red text-white p-10 flex flex-col justify-between relative overflow-hidden">
                    <div class="absolute right-0 top-0 opacity-10 pointer-events-none transform translate-x-1/4 -translate-y-1/4">
                        <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>
                    </div>
                    <div class="relative z-10">
                        <h2 class="font-heading text-4xl font-bold mb-6">READY TO ROLL?</h2>
                        <p class="text-white/90 mb-8 font-medium">Request a quote or schedule your service. Fast response guaranteed during business hours.</p>
                        <div class="space-y-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <div class="text-xs uppercase tracking-widest opacity-80 font-bold mb-1">Fast Service</div>
                                    <div class="font-medium">Same-day availability for most repairs</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </div>
                                <div>
                                    <div class="text-xs uppercase tracking-widest opacity-80 font-bold mb-1">Call Direct</div>
                                    <a href="tel:6677273115" class="font-medium text-white/90 hover:text-white">667-727-3115</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Panel -->
                <div class="lg:w-2/3 p-10 lg:p-16">
                    <h3 class="font-heading text-3xl font-bold text-white mb-8">Request an Appointment or Quote</h3>

                    <!-- Success message -->
                    <div x-show="success" x-cloak class="bg-green-900 border border-green-700 text-green-300 p-6 rounded-sm mb-6 text-center">
                        <svg class="w-10 h-10 mx-auto mb-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="font-bold text-lg">Request received!</p>
                        <p class="text-sm mt-1">We'll contact you shortly to confirm your appointment.</p>
                    </div>

                    <form @submit.prevent="submit()" x-show="!success">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wide">Service Needed</label>
                                <select x-model="form.service_type" required
                                        class="w-full bg-brand-dark border border-gray-700 text-white rounded-sm py-3 px-4 focus:outline-none focus:border-brand-red">
                                    <option value="">Select a service...</option>
                                    <option value="Tire Installation (New or Used)">Tire Installation (New or Used)</option>
                                    <option value="Wheel Alignment">Wheel Alignment</option>
                                    <option value="Wheel Balancing">Wheel Balancing</option>
                                    <option value="Tire Repair (Flat)">Tire Repair (Flat)</option>
                                    <option value="General Auto Repair / Oil Change">General Auto Repair / Oil Change</option>
                                    <option value="Other">Other / Not Sure</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wide">Vehicle (Year/Make/Model)</label>
                                <input type="text" x-model="form.vehicle" placeholder="e.g. 2024 Honda Civic"
                                       class="w-full bg-brand-dark border border-gray-700 text-white rounded-sm py-3 px-4 focus:outline-none focus:border-brand-red placeholder-gray-600">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wide">Tire Size (if known)</label>
                                <input type="text" x-model="form.tire_size" placeholder="e.g. 215/55R17"
                                       class="w-full bg-brand-dark border border-gray-700 text-white rounded-sm py-3 px-4 focus:outline-none focus:border-brand-red placeholder-gray-600">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wide">Your Name</label>
                                <input type="text" x-model="form.name" required placeholder="John Doe"
                                       class="w-full bg-brand-dark border border-gray-700 text-white rounded-sm py-3 px-4 focus:outline-none focus:border-brand-red placeholder-gray-600">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wide">Phone Number</label>
                                <input type="tel" x-model="form.phone" required placeholder="(---) --- ----"
                                       class="w-full bg-brand-dark border border-gray-700 text-white rounded-sm py-3 px-4 focus:outline-none focus:border-brand-red placeholder-gray-600">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wide">Additional Notes</label>
                                <textarea x-model="form.notes" rows="3" placeholder="Any additional details..."
                                          class="w-full bg-brand-dark border border-gray-700 text-white rounded-sm py-3 px-4 focus:outline-none focus:border-brand-red placeholder-gray-600 resize-none"></textarea>
                            </div>
                            <div class="md:col-span-2">
                                <button type="submit" :disabled="submitting"
                                        :class="submitting ? 'opacity-60 cursor-not-allowed' : 'hover:bg-brand-redhover hover:-translate-y-0.5'"
                                        class="w-full bg-brand-red text-white py-4 font-heading text-2xl font-bold rounded-sm transition-all shadow-md">
                                    <span x-show="!submitting">REQUEST APPOINTMENT</span>
                                    <span x-show="submitting" x-cloak>Sending...</span>
                                </button>
                                <p x-show="error" x-cloak class="text-red-400 text-sm mt-3 text-center" x-text="error"></p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-brand-black text-gray-400 py-16 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
                <div>
                    <img src="{{ asset('Logo Dundalk.png') }}" alt="Dundalk Tires" class="h-12 w-auto mb-4 brightness-200">
                    <p class="text-sm leading-relaxed">Your trusted source for new and used tires, alignment, balancing, and full auto care in Dundalk, Maryland.</p>
                </div>
                <div>
                    <h4 class="font-heading text-xl font-bold text-white mb-4 uppercase">Contact</h4>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-start gap-2"><svg class="w-4 h-4 text-brand-red mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> 7888 Dundalk Ave, Dundalk MD 21222</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-brand-red flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg> <a href="tel:6677273115" class="hover:text-white transition-colors">667-727-3115</a></li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-brand-red flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Mon – Sat: 8:00 AM – 6:00 PM</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-heading text-xl font-bold text-white mb-4 uppercase">Services</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#tires"     class="hover:text-white transition-colors">New & Used Tires</a></li>
                        <li><a href="#alignment" class="hover:text-white transition-colors">Wheel Alignment</a></li>
                        <li><a href="#balancing-repair" class="hover:text-white transition-colors">Wheel Balancing</a></li>
                        <li><a href="#balancing-repair" class="hover:text-white transition-colors">Tire Repair & Patching</a></li>
                        <li><a href="#book"       class="hover:text-white transition-colors">Auto Repair</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-gray-600">
                <p>&copy; {{ date('Y') }} Dundalk Tires. All rights reserved.</p>
                <a href="/login" class="hover:text-gray-400 transition-colors">Staff Login</a>
            </div>
        </div>
    </footer>

    <script>
    // Register Alpine components before Alpine initializes
    document.addEventListener('alpine:init', () => {
        Alpine.data('tireFinder',      () => tireFinder());
        Alpine.data('inventoryGrid',   () => inventoryGrid());
        Alpine.data('bookingForm',     () => bookingForm());
    });

    // Tire Finder (Hero Widget)
    function tireFinder() {
        const API = '/api/public';
        return {
            tab: 'vehicle',
            vehicle: { year: '', make: '', model: '' },
            size: { width: '', ratio: '', diameter: '', manual: '' },
            years: [],
            makes: [],
            models: [],

            init() {
                const currentYear = new Date().getFullYear();
                this.years = Array.from({ length: currentYear - 1989 }, (_, i) => currentYear - i);
                this.loadMakes();
            },

            async loadMakes() {
                try {
                    const r = await fetch(`${API}/vehicles/makes`);
                    this.makes = await r.json();
                } catch (e) {}
            },

            async onYearChange() {
                this.vehicle.make = '';
                this.vehicle.model = '';
                this.models = [];
                if (this.makes.length === 0) await this.loadMakes();
            },

            async onMakeChange() {
                this.vehicle.model = '';
                this.models = [];
                if (!this.vehicle.make) return;
                try {
                    const r = await fetch(`${API}/vehicles/models?make=${encodeURIComponent(this.vehicle.make)}`);
                    this.models = await r.json();
                } catch (e) {}
            },

            async searchByVehicle() {
                if (!this.vehicle.make) return;
                try {
                    const params = new URLSearchParams({ make: this.vehicle.make });
                    if (this.vehicle.model) params.set('model', this.vehicle.model);
                    if (this.vehicle.year) params.set('year', this.vehicle.year);
                    const r = await fetch(`${API}/vehicles/tire-size?${params}`);
                    const data = await r.json();
                    if (data.tire_size) {
                        window.__pendingSearch = { size: data.tire_size };
                    }
                } catch (e) {}
                document.getElementById('tires').scrollIntoView({ behavior: 'smooth' });
                if (window.__pendingSearch) {
                    Alpine.store && Alpine.$data && document.getElementById('tires')._x_dataStack;
                }
            },

            searchBySize() {
                let sizeStr = this.size.manual;
                if (!sizeStr && this.size.width && this.size.ratio && this.size.diameter) {
                    sizeStr = `${this.size.width}/${this.size.ratio}R${this.size.diameter}`;
                }
                if (sizeStr) {
                    window.__pendingSearch = { size: sizeStr };
                }
                document.getElementById('tires').scrollIntoView({ behavior: 'smooth' });
            },
        };
    }

    // Inventory Grid
    function inventoryGrid() {
        const __data = window.__dundalkData || { tires: [], filters: { total: 0, brands: [] } };
        const initialTires  = __data.tires;
        const serverFilters = __data.filters;
        const API = '/api/public';
        return {
            tires: initialTires,
            meta: { total: serverFilters.total || initialTires.length, per_page: 9, current_page: 1, last_page: 1 },
            loading: false,
            activeBrand: null,
            activeType: null,
            serverFilters: serverFilters,
            currentParams: {},

            boot() {
                // Check if the tire finder sent a pending search
                if (window.__pendingSearch) {
                    const pending = window.__pendingSearch;
                    delete window.__pendingSearch;
                    this.fetchTires(pending);
                    return;
                }
                // Fetch first page fresh so meta.last_page is correct
                this.fetchTires({});
            },

            async fetchTires(params = {}) {
                this.loading = true;
                this.currentParams = params;
                try {
                    const url = new URL(window.location.origin + `${API}/tires`);
                    const merged = { ...params, per_page: 9 };
                    Object.entries(merged).forEach(([k, v]) => { if (v !== null && v !== '') url.searchParams.set(k, v); });
                    const r = await fetch(url);
                    const data = await r.json();
                    this.tires = data.data;
                    this.meta  = data.meta;
                } catch (e) {
                    // Keep server-rendered data on error
                } finally {
                    this.loading = false;
                }
            },

            filterByBrand(brand) {
                this.activeBrand = brand;
                const p = { ...this.currentParams };
                delete p.brand;
                if (brand) p.brand = brand;
                this.fetchTires(p);
            },

            filterByType(type) {
                this.activeType = type;
                const p = { ...this.currentParams };
                delete p.tire_condition;
                this.fetchTires(p);
            },

            clearSearch() {
                this.activeBrand = null;
                this.activeType  = null;
                this.currentParams = {};
                this.fetchTires({});
            },

            goToPage(page) {
                this.fetchTires({ ...this.currentParams, page });
                document.getElementById('tires').scrollIntoView({ behavior: 'smooth' });
            },

            prefillService(service, size) {
                this.$nextTick(() => {
                    const bookSection = document.getElementById('book');
                    if (bookSection) {
                        const bookData = bookSection._x_dataStack && bookSection._x_dataStack[0];
                        if (bookData) {
                            bookData.form.service_type = service;
                            bookData.form.tire_size    = size;
                        }
                    }
                });
            },
        };
    }

    // Booking Form
    function bookingForm() {
        return {
            form: {
                name: '', phone: '', service_type: '', vehicle: '', tire_size: '', notes: '',
            },
            submitting: false,
            success: false,
            error: '',

            init() {},

            async submit() {
                this.submitting = true;
                this.error = '';
                try {
                    const r = await fetch('/quote', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(this.form),
                    });
                    const data = await r.json();
                    if (r.ok && data.message === 'ok') {
                        this.success = true;
                    } else {
                        const msgs = data.errors ? Object.values(data.errors).flat().join(' ') : 'Something went wrong. Please call us directly.';
                        this.error = msgs;
                    }
                } catch (e) {
                    this.error = 'Connection error. Please call us at 667-727-3115.';
                } finally {
                    this.submitting = false;
                }
            },
        };
    }
    </script>
</body>
</html>
