const fs = require('fs');

const file = '/Users/usuario/Downloads/Desarrollo/Dunkdalk/public/dundalk-landing.html';

// Extract the base64 images from existing file to keep them
let currentHtml = fs.readFileSync(file, 'utf8');
const heroImgMatch = currentHtml.match(/hero-bg\s*\{\s*background-image:[^,]+,\s*url\('([^']+)'\)/);
const facadeImgMatch = currentHtml.match(/<img src="([^"]+)" alt="Dundalk Tires Shop"/);
const logoImgMatch = currentHtml.match(/<img src="([^"]+)" alt="Dundalk Tires Logo"/);

const heroImg = heroImgMatch ? heroImgMatch[1] : '';
const facadeImg = facadeImgMatch ? facadeImgMatch[1] : '';
const logoImg = logoImgMatch ? logoImgMatch[1] : '';

const newHtml = `<!DOCTYPE html>
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

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- UX/Animation Styles -->
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }
        h1, h2, h3, h4, .font-heading { font-family: 'Barlow Condensed', sans-serif; text-transform: uppercase; }
        
        .hero-bg {
            background-image: linear-gradient(to right, rgba(17,17,17,0.95) 0%, rgba(17,17,17,0.8) 50%, rgba(17,17,17,0.4) 100%), url('${heroImg}');
            background-size: cover;
            background-position: center;
        }

        /* Forms / Selects */
        select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1em;
            transition: all 0.2s ease;
        }
        select:disabled { opacity: 0.5; cursor: not-allowed; }
        input { transition: all 0.2s ease; }
        
        .tire-diagram { background-image: radial-gradient(#d1d5db 1px, transparent 1px); background-size: 20px 20px; }

        /* Scroll Reveal (Intersection Observer) */
        .reveal { opacity: 0; transform: translateY(20px); transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), transform 0.6s cubic-bezier(0.16, 1, 0.3, 1); will-change: opacity, transform; }
        .reveal.active { opacity: 1; transform: translateY(0); }
        .reveal-delay-1 { transition-delay: 100ms; }
        .reveal-delay-2 { transition-delay: 200ms; }
        .reveal-delay-3 { transition-delay: 300ms; }

        /* Staggers */
        .stagger-parent > * { opacity: 0; transform: translateY(15px); transition: all 0.5s ease; }
        .stagger-parent.active > * { opacity: 1; transform: translateY(0); }
        .stagger-parent.active > *:nth-child(1) { transition-delay: 50ms; }
        .stagger-parent.active > *:nth-child(2) { transition-delay: 100ms; }
        .stagger-parent.active > *:nth-child(3) { transition-delay: 150ms; }
        .stagger-parent.active > *:nth-child(4) { transition-delay: 200ms; }

        /* Button Micro-interactions */
        .btn-magnetic { transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275), background-color 0.2s ease; }
        .btn-magnetic:active { transform: scale(0.96); }

        /* Loader */
        .loader-ring { border: 2px solid rgba(255,255,255,0.2); border-top-color: white; border-radius: 50%; width: 16px; height: 16px; animation: spin 0.8s linear infinite; display: inline-block; vertical-align: middle; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Accessibility (Reduce Motion) */
        @media (prefers-reduced-motion: reduce) {
            .reveal, .stagger-parent > *, .btn-magnetic { transition: none !important; opacity: 1 !important; transform: none !important; }
            .loader-ring { animation: none; }
        }
    </style>
</head>
<body class="bg-white text-gray-900" x-data="{ mobileMenu: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 20)">

    <!-- Top Bar -->
    <div class="bg-brand-black text-gray-300 text-xs py-2 hidden md:block relative z-50">
        <div class="max-w-7xl mx-auto px-6 flex justify-between items-center">
            <div class="flex gap-6">
                <a href="#about" class="flex items-center gap-2 hover:text-white transition-colors group"><svg class="w-4 h-4 text-brand-red group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> 7888 Dundalk Ave, MD 21222</a>
                <span class="flex items-center gap-2"><svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Mon - Sat: 8:00 AM - 6:00 PM</span>
            </div>
            <div class="flex gap-4">
                <a href="#book" class="hover:text-white transition-colors">Book Appointment</a>
            </div>
        </div>
    </div>

    <!-- Main Navigation (Sticky + Glassmorphism transition) -->
    <nav :class="{ 'py-3 shadow-md bg-white/95 backdrop-blur-md': scrolled, 'py-5 bg-white': !scrolled }" class="sticky top-0 z-50 transition-all duration-300 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 flex justify-between items-center">
            <!-- Logo -->
            <a href="#" class="flex-shrink-0 transition-transform hover:scale-105 duration-300">
                <img src="${logoImg}" alt="Dundalk Tires Logo" class="h-10 md:h-12 w-auto object-contain">
            </a>

            <!-- Desktop Menu -->
            <div class="hidden lg:flex items-center gap-8 font-heading text-lg font-bold tracking-wide">
                <a href="#tires" class="hover:text-brand-red transition-colors relative group">Tires
                    <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-brand-red transition-all duration-300 group-hover:w-full"></span>
                </a>
                <a href="#services" class="hover:text-brand-red transition-colors relative group">Services
                    <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-brand-red transition-all duration-300 group-hover:w-full"></span>
                </a>
                <a href="#alignment" class="hover:text-brand-red transition-colors relative group">Wheel Alignment
                    <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-brand-red transition-all duration-300 group-hover:w-full"></span>
                </a>
                <a href="#about" class="hover:text-brand-red transition-colors relative group">About Us
                    <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-brand-red transition-all duration-300 group-hover:w-full"></span>
                </a>
            </div>

            <!-- CTA & Phone -->
            <div class="hidden lg:flex items-center gap-6">
                <a href="tel:6677273115" class="flex items-center gap-2 font-bold text-lg hover:text-brand-red transition-colors group">
                    <svg class="w-5 h-5 text-brand-red group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    667-727-3115
                </a>
                <a href="#book" class="btn-magnetic bg-brand-red text-white px-6 py-2.5 font-heading text-xl font-bold rounded-sm shadow-md hover:bg-brand-redhover">
                    Book Service
                </a>
            </div>

            <!-- Mobile Toggle -->
            <button @click="mobileMenu = !mobileMenu" class="lg:hidden p-2 text-brand-black transition-transform duration-200" :class="{ 'rotate-90': mobileMenu }">
                <svg x-show="!mobileMenu" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="mobileMenu" x-cloak class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Mobile Menu (Smooth transition) -->
        <div x-show="mobileMenu" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="lg:hidden bg-white border-t border-gray-100 absolute w-full left-0 shadow-xl z-40" x-cloak>
            <div class="px-6 py-4 flex flex-col gap-4 font-heading text-xl font-bold">
                <a @click="mobileMenu = false" href="#tires" class="block py-2 border-b border-gray-50 hover:text-brand-red transition-colors">Tires</a>
                <a @click="mobileMenu = false" href="#services" class="block py-2 border-b border-gray-50 hover:text-brand-red transition-colors">Services</a>
                <a @click="mobileMenu = false" href="#alignment" class="block py-2 border-b border-gray-50 hover:text-brand-red transition-colors">Wheel Alignment</a>
                <a @click="mobileMenu = false" href="#about" class="block py-2 border-b border-gray-50 hover:text-brand-red transition-colors">About Us</a>
                <a href="tel:6677273115" class="flex items-center gap-2 py-2 text-brand-red transition-transform active:scale-95"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg> 667-727-3115</a>
            </div>
        </div>
    </nav>

    <!-- HERO & TIRE FINDER (Progressive Reveal) -->
    <header class="relative w-full min-h-[600px] lg:min-h-[700px] flex items-center pt-8 pb-16 lg:py-0 hero-bg overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 w-full grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Hero Text -->
            <div class="lg:col-span-7 text-white reveal active">
                <div class="inline-block bg-brand-red px-4 py-1 font-heading font-bold text-lg mb-6 tracking-wide uppercase transition-transform hover:scale-105 cursor-default">New & Used Tires</div>
                <h1 class="text-5xl md:text-7xl font-extrabold mb-6 leading-[1.05] tracking-wide">
                    FIND THE RIGHT TIRES. <br><span class="text-gray-400">GET BACK ON THE ROAD.</span>
                </h1>
                <p class="text-lg md:text-xl text-gray-300 font-body mb-8 max-w-lg leading-relaxed">
                    Professional installation, precision alignment, balancing, and full mechanical services in Dundalk. Quality you can trust.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#book" class="btn-magnetic bg-white text-brand-black px-8 py-3.5 font-heading text-xl font-bold rounded-sm shadow-lg hover:bg-gray-100">
                        Book a Service
                    </a>
                </div>
            </div>

            <!-- Interactive Tire Finder Widget -->
            <div class="lg:col-span-5 w-full max-w-md mx-auto lg:ml-auto reveal active reveal-delay-2" 
                 x-data="{ 
                    tab: 'vehicle',
                    year: '', make: '', model: '',
                    sizeW: '', sizeR: '', sizeD: '',
                    loading: false, loadingMake: false, loadingModel: false,
                    
                    search() {
                        this.loading = true;
                        setTimeout(() => { this.loading = false; window.location.hash = '#tires'; }, 600);
                    },
                    onYearChange() {
                        if(!this.year) { this.make = ''; this.model = ''; return; }
                        this.loadingMake = true; this.make = ''; this.model = '';
                        setTimeout(() => { this.loadingMake = false; }, 300);
                    },
                    onMakeChange() {
                        if(!this.make) { this.model = ''; return; }
                        this.loadingModel = true; this.model = '';
                        setTimeout(() => { this.loadingModel = false; }, 300);
                    }
                 }">
                <div class="bg-white rounded-md shadow-2xl overflow-hidden border-t-8 border-brand-red">
                    <div class="p-6 pb-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="font-heading text-3xl font-bold text-brand-black flex items-center gap-2">
                            <svg class="w-6 h-6 text-brand-red" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
                            FIND TIRES
                        </h2>
                    </div>
                    
                    <!-- Tabs (Smooth sliding transition) -->
                    <div class="flex border-b border-gray-200 relative">
                        <button @click="tab = 'vehicle'" :class="tab === 'vehicle' ? 'text-brand-red' : 'text-gray-500 hover:text-gray-700'" class="flex-1 py-3 px-4 font-heading text-xl font-bold text-center transition-colors relative z-10">
                            By Vehicle
                        </button>
                        <button @click="tab = 'size'" :class="tab === 'size' ? 'text-brand-red' : 'text-gray-500 hover:text-gray-700'" class="flex-1 py-3 px-4 font-heading text-xl font-bold text-center transition-colors relative z-10">
                            By Size
                        </button>
                        <!-- Active tab indicator -->
                        <div class="absolute bottom-0 h-0.5 bg-brand-red w-1/2 transition-transform duration-300 ease-out z-20" :class="tab === 'size' ? 'translate-x-full' : 'translate-x-0'"></div>
                    </div>

                    <div class="relative overflow-hidden" style="min-height: 320px;">
                        <!-- By Vehicle Form -->
                        <div x-show="tab === 'vehicle'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-200 absolute w-full top-0" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 -translate-x-4" class="p-6 space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wide flex justify-between">Year <span x-show="year" class="text-green-500 text-xs">✓</span></label>
                                <select x-model="year" @change="onYearChange" class="w-full border border-gray-300 rounded-sm py-3 px-4 bg-white focus:outline-none focus:border-brand-red focus:ring-1 focus:ring-brand-red transition-all cursor-pointer">
                                    <option value="">Select Year</option>
                                    <option value="2024">2024</option>
                                    <option value="2023">2023</option>
                                    <option value="2022">2022</option>
                                </select>
                            </div>
                            <div class="relative">
                                <label class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wide flex justify-between">Make <span x-show="make" class="text-green-500 text-xs">✓</span></label>
                                <select x-model="make" @change="onMakeChange" :disabled="!year || loadingMake" class="w-full border border-gray-300 rounded-sm py-3 px-4 focus:outline-none focus:border-brand-red transition-all" :class="(!year || loadingMake) ? 'bg-gray-50 text-gray-400' : 'bg-white cursor-pointer'">
                                    <option value="">Select Make</option>
                                    <option value="Honda">Honda</option>
                                    <option value="Toyota">Toyota</option>
                                </select>
                                <div x-show="loadingMake" class="absolute right-10 top-10 loader-ring" style="border-top-color: #dc2626; width: 14px; height: 14px;"></div>
                            </div>
                            <div class="relative">
                                <label class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wide flex justify-between">Model <span x-show="model" class="text-green-500 text-xs">✓</span></label>
                                <select x-model="model" :disabled="!make || loadingModel" class="w-full border border-gray-300 rounded-sm py-3 px-4 focus:outline-none focus:border-brand-red transition-all" :class="(!make || loadingModel) ? 'bg-gray-50 text-gray-400' : 'bg-white cursor-pointer'">
                                    <option value="">Select Model</option>
                                    <option value="Civic">Civic</option>
                                    <option value="CRV">CR-V</option>
                                </select>
                                <div x-show="loadingModel" class="absolute right-10 top-10 loader-ring" style="border-top-color: #dc2626; width: 14px; height: 14px;"></div>
                            </div>
                            <button @click="search()" :disabled="!year || !make || !model" class="btn-magnetic w-full py-4 font-heading text-2xl font-bold rounded-sm mt-2 transition-all shadow-md flex items-center justify-center gap-2" :class="(!year || !make || !model) ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-brand-red text-white hover:bg-brand-redhover'">
                                <span x-show="!loading">SHOW TIRES</span>
                                <span x-show="loading" class="loader-ring"></span>
                            </button>
                        </div>

                        <!-- By Size Form -->
                        <div x-show="tab === 'size'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-200 absolute w-full top-0" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-4" class="p-6 space-y-4">
                            <div class="flex gap-2">
                                <div class="flex-1">
                                    <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Width</label>
                                    <select x-model="sizeW" class="w-full border border-gray-300 rounded-sm py-3 px-2 bg-white focus:outline-none focus:border-brand-red text-center transition-colors cursor-pointer">
                                        <option value="">---</option>
                                        <option value="205">205</option>
                                        <option value="215">215</option>
                                        <option value="225">225</option>
                                    </select>
                                </div>
                                <div class="flex items-center text-2xl font-light text-gray-300 pt-4">/</div>
                                <div class="flex-1">
                                    <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Ratio</label>
                                    <select x-model="sizeR" class="w-full border border-gray-300 rounded-sm py-3 px-2 bg-white focus:outline-none focus:border-brand-red text-center transition-colors cursor-pointer">
                                        <option value="">---</option>
                                        <option value="55">55</option>
                                        <option value="60">60</option>
                                    </select>
                                </div>
                                <div class="flex items-center text-2xl font-light text-gray-300 pt-4">R</div>
                                <div class="flex-1">
                                    <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Diam</label>
                                    <select x-model="sizeD" class="w-full border border-gray-300 rounded-sm py-3 px-2 bg-white focus:outline-none focus:border-brand-red text-center transition-colors cursor-pointer">
                                        <option value="">---</option>
                                        <option value="16">16</option>
                                        <option value="17">17</option>
                                        <option value="18">18</option>
                                    </select>
                                </div>
                            </div>
                            <div class="text-center pt-2 pb-2">
                                <span class="text-sm text-gray-500 font-medium">Or type your size:</span>
                                <input type="text" placeholder="e.g. 215/55R17" class="w-full mt-2 border border-gray-300 rounded-sm py-3 px-4 focus:outline-none focus:border-brand-red focus:ring-1 focus:ring-brand-red text-center font-bold">
                            </div>
                            <button @click="search()" class="btn-magnetic w-full py-4 font-heading text-2xl font-bold rounded-sm mt-2 transition-all shadow-md flex items-center justify-center gap-2 bg-brand-red text-white hover:bg-brand-redhover">
                                <span x-show="!loading">SHOW TIRES</span>
                                <span x-show="loading" class="loader-ring"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- TIRE BRANDS (Stagger Reveal) -->
    <section class="py-10 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 reveal">
            <p class="text-center text-sm font-bold text-gray-400 uppercase tracking-widest mb-6">We carry all major brands</p>
            <div class="flex flex-wrap justify-center gap-8 md:gap-16 opacity-60 stagger-parent">
                <div class="text-xl md:text-2xl font-bold font-heading tracking-wider text-gray-500 hover:text-brand-red hover:opacity-100 transition-all cursor-default">MICHELIN</div>
                <div class="text-xl md:text-2xl font-bold font-heading tracking-wider text-gray-500 hover:text-brand-red hover:opacity-100 transition-all cursor-default">GOODYEAR</div>
                <div class="text-xl md:text-2xl font-bold font-heading tracking-wider text-gray-500 hover:text-brand-red hover:opacity-100 transition-all cursor-default">FIRESTONE</div>
                <div class="text-xl md:text-2xl font-bold font-heading tracking-wider text-gray-500 hover:text-brand-red hover:opacity-100 transition-all cursor-default">BRIDGESTONE</div>
                <div class="text-xl md:text-2xl font-bold font-heading tracking-wider text-gray-500 hover:text-brand-red hover:opacity-100 transition-all cursor-default">CONTINENTAL</div>
            </div>
        </div>
    </section>

    <!-- NEW VS USED (Hover effects) -->
    <section class="py-20 bg-brand-gray border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16 reveal">
                <h2 class="font-heading text-4xl md:text-5xl font-extrabold text-brand-black uppercase">THE DUNDALK ADVANTAGE</h2>
                <div class="w-20 h-1.5 bg-brand-red mx-auto mt-4 mb-6"></div>
                <p class="text-gray-600 max-w-2xl mx-auto text-lg">Whether you need maximum performance from top brands or budget-friendly reliability, we carry a massive inventory of both New and Used tires.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 stagger-parent reveal">
                <!-- New Tires -->
                <div class="bg-white p-8 md:p-10 rounded-sm shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gray-50 rounded-bl-full -z-10 group-hover:bg-red-50 group-hover:scale-150 transition-all duration-500"></div>
                    <h3 class="font-heading text-3xl font-bold text-brand-black mb-4 flex items-center gap-3">
                        <span class="w-3 h-8 bg-brand-red inline-block group-hover:h-10 transition-all"></span> NEW TIRES
                    </h3>
                    <ul class="space-y-3 text-gray-600 mb-8 font-medium">
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Top industry brands available</li>
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Full manufacturer warranties</li>
                    </ul>
                    <a href="#tires" class="text-brand-red font-bold uppercase tracking-wider group-hover:text-brand-redhover flex items-center gap-1 transition-colors">Shop New Tires <svg class="w-4 h-4 transform group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                </div>

                <!-- Used Tires -->
                <div class="bg-brand-black text-white p-8 md:p-10 rounded-sm shadow-xl relative overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gray-800 rounded-bl-full -z-10 group-hover:bg-brand-red group-hover:scale-150 transition-all duration-500"></div>
                    <h3 class="font-heading text-3xl font-bold mb-4 flex items-center gap-3">
                        <span class="w-3 h-8 bg-brand-red inline-block group-hover:h-10 transition-all"></span> USED TIRES
                    </h3>
                    <ul class="space-y-3 text-gray-300 mb-8 font-medium">
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-brand-red group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Cost-effective alternative</li>
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-brand-red group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Rigorously hand-inspected for safety</li>
                    </ul>
                    <a href="#tires" class="text-brand-red font-bold uppercase tracking-wider group-hover:text-white flex items-center gap-1 transition-colors">Check Availability <svg class="w-4 h-4 transform group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                </div>
            </div>
        </div>
    </section>

    <!-- INVENTORY (Product Cards Hover states) -->
    <section id="tires" class="py-24 bg-gray-50 reveal">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12">
                <div>
                    <h2 class="font-heading text-4xl md:text-5xl font-extrabold text-brand-black uppercase">CURRENT <span class="text-brand-red">INVENTORY</span></h2>
                    <div class="w-20 h-1.5 bg-brand-red mt-4"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 stagger-parent reveal">
                <!-- Tire Card -->
                <div class="bg-white border border-gray-200 p-6 rounded-md shadow-sm hover:shadow-2xl transition-shadow duration-300 group relative flex flex-col">
                    <div class="absolute top-6 left-6 bg-green-500 text-white text-xs font-bold px-3 py-1 uppercase tracking-wider rounded-sm z-10 transition-transform group-hover:scale-105">NEW IN STOCK</div>
                    <div class="aspect-square bg-gray-100 rounded-sm mb-6 flex items-center justify-center p-8 relative overflow-hidden">
                        <svg class="w-32 h-32 text-gray-300 group-hover:scale-110 group-hover:text-gray-400 transition-all duration-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12,2C6.48,2 2,6.48 2,12C2,17.52 6.48,22 12,22C17.52,22 22,17.52 22,12C22,6.48 17.52,2 12,2M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20Z"/></svg>
                    </div>
                    <div class="mb-2">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest group-hover:text-brand-red transition-colors">Michelin</span>
                    </div>
                    <h3 class="text-2xl font-heading font-bold text-gray-900 mb-1">Defender T+H</h3>
                    <p class="text-brand-red font-bold font-mono text-lg mb-4">215/55R17</p>
                    <div class="flex-grow"></div>
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div>
                            <span class="text-xs text-gray-400 block mb-1">Starting at</span>
                            <span class="text-2xl font-bold text-gray-900">$145.99</span>
                        </div>
                        <button class="btn-magnetic bg-brand-black text-white px-4 py-2 text-sm font-bold uppercase hover:bg-brand-red transition-colors rounded-sm">Get Quote</button>
                    </div>
                </div>

                <!-- Used Tire Card -->
                <div class="bg-white border border-gray-200 p-6 rounded-md shadow-sm hover:shadow-2xl transition-shadow duration-300 group relative flex flex-col">
                    <div class="absolute top-6 left-6 bg-brand-black text-white text-xs font-bold px-3 py-1 uppercase tracking-wider rounded-sm z-10 transition-transform group-hover:scale-105">USED (GRADE A)</div>
                    <div class="aspect-square bg-gray-100 rounded-sm mb-6 flex items-center justify-center p-8 relative overflow-hidden">
                        <svg class="w-32 h-32 text-gray-300 group-hover:scale-110 group-hover:text-gray-400 transition-all duration-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12,2C6.48,2 2,6.48 2,12C2,17.52 6.48,22 12,22C17.52,22 22,17.52 22,12C22,6.48 17.52,2 12,2M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20Z"/></svg>
                    </div>
                    <div class="mb-2">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest group-hover:text-brand-red transition-colors">Goodyear</span>
                    </div>
                    <h3 class="text-2xl font-heading font-bold text-gray-900 mb-1">Assurance All-Season</h3>
                    <p class="text-brand-red font-bold font-mono text-lg mb-4">205/60R16</p>
                    <div class="flex-grow"></div>
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div>
                            <span class="text-xs text-gray-400 block mb-1">Estimated at</span>
                            <span class="text-2xl font-bold text-gray-900">$45.00</span>
                        </div>
                        <a href="tel:6677273115" class="btn-magnetic bg-brand-black text-white px-4 py-2 text-sm font-bold uppercase hover:bg-brand-red transition-colors rounded-sm inline-block">Call Now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CORE SERVICES -->
    <section id="services" class="py-24 border-b border-gray-100 reveal">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12">
                <div>
                    <h2 class="font-heading text-4xl md:text-5xl font-extrabold text-brand-black uppercase">AUTO CARE & <span class="text-brand-red">SERVICES</span></h2>
                    <div class="w-20 h-1.5 bg-brand-red mt-4"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 stagger-parent reveal">
                <!-- Alignments -->
                <a href="#alignment" class="block border border-gray-200 p-8 rounded-sm hover:border-brand-red hover:shadow-xl transition-all duration-300 group flex flex-col justify-between h-full bg-white transform hover:-translate-y-1">
                    <div>
                        <div class="w-12 h-12 bg-red-50 text-brand-red rounded-full flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-brand-red group-hover:text-white transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <h3 class="font-heading text-2xl font-bold mb-2 text-gray-900">Wheel Alignment</h3>
                        <p class="text-gray-600 text-sm mb-6">Prevent uneven tire wear and steering pull with our precision computerized alignment.</p>
                    </div>
                    <span class="text-sm font-bold uppercase tracking-wider text-brand-red group-hover:text-brand-black transition-colors flex items-center gap-1">Learn More <svg class="w-4 h-4 transform group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
                </a>

                <!-- Mechanical -->
                <a href="#book" class="block bg-brand-black text-white p-8 rounded-sm hover:bg-gray-900 hover:shadow-xl transition-all duration-300 group flex flex-col justify-between h-full border-b-4 border-brand-red transform hover:-translate-y-1">
                    <div>
                        <div class="w-12 h-12 bg-gray-800 text-brand-red rounded-full flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-brand-red group-hover:text-white transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065z"/></svg>
                        </div>
                        <h3 class="font-heading text-2xl font-bold mb-2">Mechanical Services</h3>
                        <p class="text-gray-300 text-sm mb-6">Oil changes, brakes, diagnostics and full auto repair by experienced technicians.</p>
                    </div>
                    <span class="text-sm font-bold uppercase tracking-wider text-brand-red group-hover:text-white transition-colors flex items-center gap-1">Request Quote <svg class="w-4 h-4 transform group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
                </a>
            </div>
        </div>
    </section>

    <!-- TIRE EDUCATION (Highly Interactive Component) -->
    <section id="about" class="py-24 bg-brand-black text-white tire-diagram reveal">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div x-data="{ activePart: null }">
                    <h2 class="text-brand-red font-bold tracking-widest text-sm mb-2 uppercase">Interactive Guide</h2>
                    <h3 class="font-heading text-4xl md:text-5xl font-extrabold mb-8">HOW TO READ <br>YOUR TIRE SIZE</h3>
                    <p class="text-gray-400 mb-8 text-lg leading-relaxed">Hover or tap on the numbers below to understand what each part of your tire size means.</p>
                    
                    <div class="bg-brand-dark p-8 rounded-md border border-gray-800 shadow-2xl">
                        <div class="text-5xl md:text-6xl font-heading font-bold mb-10 text-center tracking-widest select-none flex justify-center items-center gap-1">
                            <span @mouseenter="activePart = 'width'" @mouseleave="activePart = null" @touchstart.passive="activePart = 'width'" :class="activePart === 'width' ? 'text-white scale-110 -translate-y-2' : 'text-gray-400'" class="interactive-tire-part">215</span>
                            <span class="text-gray-600 text-4xl">/</span>
                            <span @mouseenter="activePart = 'ratio'" @mouseleave="activePart = null" @touchstart.passive="activePart = 'ratio'" :class="activePart === 'ratio' ? 'text-brand-red scale-110 -translate-y-2' : 'text-gray-400'" class="interactive-tire-part">55</span>
                            <span @mouseenter="activePart = 'radial'" @mouseleave="activePart = null" @touchstart.passive="activePart = 'radial'" :class="activePart === 'radial' ? 'text-gray-200 scale-110 -translate-y-2' : 'text-gray-600'" class="interactive-tire-part">R</span>
                            <span @mouseenter="activePart = 'diam'" @mouseleave="activePart = null" @touchstart.passive="activePart = 'diam'" :class="activePart === 'diam' ? 'text-blue-400 scale-110 -translate-y-2' : 'text-gray-400'" class="interactive-tire-part">17</span>
                        </div>
                        <ul class="space-y-4 text-sm font-medium">
                            <li :class="activePart === 'width' ? 'bg-white/10 border-white pl-4' : 'border-transparent opacity-60'" class="flex justify-between border-l-4 py-2 px-2 transition-all duration-300 rounded-r-sm">
                                <span class="font-bold text-white">215</span>
                                <span class="text-gray-300">Tire Width (in millimeters)</span>
                            </li>
                            <li :class="activePart === 'ratio' ? 'bg-brand-red/10 border-brand-red pl-4' : 'border-transparent opacity-60'" class="flex justify-between border-l-4 py-2 px-2 transition-all duration-300 rounded-r-sm">
                                <span class="font-bold text-brand-red">55</span>
                                <span class="text-gray-300">Aspect Ratio (Profile height)</span>
                            </li>
                            <li :class="activePart === 'radial' ? 'bg-gray-700/50 border-gray-400 pl-4' : 'border-transparent opacity-60'" class="flex justify-between border-l-4 py-2 px-2 transition-all duration-300 rounded-r-sm">
                                <span class="font-bold text-gray-300">R</span>
                                <span class="text-gray-300">Radial Construction</span>
                            </li>
                            <li :class="activePart === 'diam' ? 'bg-blue-400/10 border-blue-400 pl-4' : 'border-transparent opacity-60'" class="flex justify-between border-l-4 py-2 px-2 transition-all duration-300 rounded-r-sm">
                                <span class="font-bold text-blue-400">17</span>
                                <span class="text-gray-300">Wheel Diameter (in inches)</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="relative reveal reveal-delay-2 group">
                    <img src="${facadeImg}" alt="Dundalk Tires Shop" class="w-full h-[500px] object-cover rounded-sm grayscale opacity-80 border-b-8 border-brand-red transition-all duration-700 group-hover:grayscale-0 group-hover:opacity-100">
                    <div class="absolute -bottom-8 -left-8 bg-brand-red p-8 hidden md:block shadow-2xl transform transition-transform duration-500 group-hover:translate-x-2 group-hover:-translate-y-2">
                        <div class="text-5xl font-heading font-extrabold mb-1">LOCAL & <br>TRUSTED</div>
                        <p class="text-white/90 font-medium">Serving the Dundalk community</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- BOOK APPOINTMENT (Multi-step interactive flow) -->
    <section id="book" class="py-24 bg-gray-50 relative reveal">
        <div class="max-w-7xl mx-auto px-6" x-data="{ 
            step: 1, 
            processing: false, 
            done: false,
            service: '',
            submit() {
                this.processing = true;
                setTimeout(() => {
                    this.processing = false;
                    this.done = true;
                }, 1200);
            }
        }">
            <div class="bg-white rounded-md shadow-2xl overflow-hidden flex flex-col lg:flex-row border border-gray-200">
                
                <!-- Left Info Panel -->
                <div class="lg:w-1/3 bg-brand-black text-white p-10 flex flex-col justify-between relative overflow-hidden">
                    <div class="absolute inset-0 bg-brand-red opacity-0 transition-opacity duration-1000" :class="{'opacity-100': done}"></div>
                    <div class="relative z-10">
                        <h2 class="font-heading text-4xl font-bold mb-6" x-text="done ? 'REQUEST RECEIVED!' : 'READY TO ROLL?'"></h2>
                        <p class="text-gray-300 mb-8 font-medium" x-show="!done">Request a quote or schedule your service. Fast response guaranteed during business hours.</p>
                        <p class="text-white mb-8 font-bold text-lg" x-show="done" x-cloak>We will contact you shortly to confirm your appointment.</p>
                        
                        <!-- Progress Steps -->
                        <div class="space-y-4" x-show="!done">
                            <div class="flex items-center gap-3 transition-opacity duration-300" :class="step >= 1 ? 'opacity-100' : 'opacity-40'">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm transition-colors" :class="step > 1 ? 'bg-green-500 text-white' : (step === 1 ? 'bg-brand-red text-white' : 'bg-gray-800 text-gray-500')">
                                    <span x-show="step <= 1">1</span>
                                    <svg x-show="step > 1" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="font-heading uppercase tracking-wider">Service & Vehicle</span>
                            </div>
                            <div class="flex items-center gap-3 transition-opacity duration-300" :class="step >= 2 ? 'opacity-100' : 'opacity-40'">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm transition-colors" :class="step > 2 ? 'bg-green-500 text-white' : (step === 2 ? 'bg-brand-red text-white' : 'bg-gray-800 text-gray-500')">2</div>
                                <span class="font-heading uppercase tracking-wider">Contact Details</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Form Panel -->
                <div class="lg:w-2/3 p-10 lg:p-16 relative overflow-hidden" style="min-height: 400px;">
                    
                    <!-- Success State -->
                    <div x-show="done" x-transition.opacity.duration.500ms class="absolute inset-0 flex flex-col items-center justify-center bg-white z-20" x-cloak>
                        <div class="w-20 h-20 bg-green-100 text-green-500 rounded-full flex items-center justify-center mb-6 scale-0 animate-[popIn_0.5s_ease-out_forwards]">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <h3 class="font-heading text-3xl font-bold text-brand-black mb-2">Request Sent Successfully</h3>
                        <p class="text-gray-500 text-center max-w-md">Our team has received your information. We will call you at the provided number to confirm your time slot.</p>
                        <button @click="done = false; step = 1; service = ''" class="mt-8 text-brand-red font-bold uppercase tracking-widest text-sm hover:text-brand-black transition-colors">Book Another Service</button>
                    </div>

                    <form class="relative" @submit.prevent="submit" x-show="!done">
                        <!-- STEP 1 -->
                        <div x-show="step === 1" x-transition:enter="form-step-enter-active" x-transition:enter-start="form-step-enter" x-transition:leave="absolute w-full opacity-0 -translate-x-5 transition-all duration-300" class="space-y-6">
                            <h3 class="font-heading text-2xl font-bold text-brand-black mb-6">What do you need help with?</h3>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wide">Service Needed</label>
                                <select x-model="service" class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-sm py-3 px-4 focus:outline-none focus:border-brand-red focus:bg-white cursor-pointer hover:border-gray-400">
                                    <option value="">Select a service...</option>
                                    <option value="Tire Installation">Tire Installation (New or Used)</option>
                                    <option value="Wheel Alignment">Wheel Alignment</option>
                                    <option value="Mechanical">General Auto Repair</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wide">Vehicle Make/Model</label>
                                    <input type="text" placeholder="e.g. Honda Civic" class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-sm py-3 px-4 focus:outline-none focus:border-brand-red focus:bg-white hover:border-gray-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wide">Tire Size <span class="lowercase text-gray-400 font-normal">(optional)</span></label>
                                    <input type="text" placeholder="e.g. 215/55R17" class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-sm py-3 px-4 focus:outline-none focus:border-brand-red focus:bg-white hover:border-gray-400">
                                </div>
                            </div>
                            <div class="pt-4 flex justify-end">
                                <button type="button" @click="step = 2" :disabled="!service" class="btn-magnetic bg-brand-black text-white px-8 py-3 font-heading text-lg font-bold rounded-sm shadow-md disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                    Next Step <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- STEP 2 -->
                        <div x-show="step === 2" x-cloak x-transition:enter="form-step-enter-active" x-transition:enter-start="form-step-enter" x-transition:leave="absolute w-full opacity-0 translate-x-5 transition-all duration-300" class="space-y-6">
                            <h3 class="font-heading text-2xl font-bold text-brand-black mb-6">Your Contact Details</h3>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wide">Your Name</label>
                                <input type="text" required placeholder="John Doe" class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-sm py-3 px-4 focus:outline-none focus:border-brand-red focus:bg-white hover:border-gray-400">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wide">Phone Number</label>
                                <input type="tel" required placeholder="(---) --- ----" class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-sm py-3 px-4 focus:outline-none focus:border-brand-red focus:bg-white hover:border-gray-400">
                            </div>
                            <div class="pt-4 flex justify-between items-center">
                                <button type="button" @click="step = 1" class="text-sm font-bold text-gray-500 hover:text-brand-black uppercase tracking-widest transition-colors flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg> Back
                                </button>
                                <button type="submit" :disabled="processing" class="btn-magnetic bg-brand-red text-white px-8 py-3 font-heading text-xl font-bold rounded-sm shadow-md hover:bg-brand-redhover flex items-center gap-2 transition-all">
                                    <span x-show="!processing">Submit Request</span>
                                    <span x-show="processing">Processing...</span>
                                    <span x-show="processing" class="loader-ring"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER (Keep Simple) -->
    <footer class="bg-brand-black text-white pt-20 pb-10 border-t-8 border-brand-red">
        <div class="max-w-7xl mx-auto px-6 text-center md:text-left">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                <!-- Brand -->
                <div>
                    <img src="${logoImg}" alt="Dundalk Tires" class="h-12 w-auto mb-6 mx-auto md:mx-0 filter brightness-0 invert">
                    <p class="text-gray-400 mb-6 leading-relaxed">Professional tire services, alignment, and auto repair in Dundalk, MD. Your safety is our priority.</p>
                </div>
                <!-- Contact Info -->
                <div>
                    <h4 class="font-heading text-xl font-bold mb-6">Contact Us</h4>
                    <ul class="space-y-4 text-gray-400">
                        <li class="flex items-start gap-3 justify-center md:justify-start">
                            <svg class="w-5 h-5 text-brand-red mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>7888 Dundalk Ave<br>Dundalk, MD 21222</span>
                        </li>
                        <li class="flex items-center gap-3 justify-center md:justify-start">
                            <svg class="w-5 h-5 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <a href="tel:6677273115" class="hover:text-white font-bold transition-colors">667-727-3115</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-sm text-gray-500 text-center">
                <p>&copy; 2026 Dundalk Tires LLC. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Fixed Mobile Bottom Bar -->
    <div class="lg:hidden fixed bottom-0 w-full bg-white border-t border-gray-200 grid grid-cols-3 z-50 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
        <a href="tel:6677273115" class="flex flex-col items-center justify-center py-3 text-gray-700 hover:text-brand-red transition-colors active:bg-gray-50">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            <span class="text-[10px] font-bold uppercase">Call Now</span>
        </a>
        <a href="https://maps.google.com" target="_blank" class="flex flex-col items-center justify-center py-3 text-gray-700 hover:text-brand-red border-l border-r border-gray-100 transition-colors active:bg-gray-50">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span class="text-[10px] font-bold uppercase">Directions</span>
        </a>
        <a href="#book" class="flex flex-col items-center justify-center py-3 bg-brand-red text-white active:bg-brand-redhover transition-colors">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span class="text-[10px] font-bold uppercase">Book</span>
        </a>
    </div>

    <!-- Scroll Reveal Logic -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const observerOptions = { root: null, rootMargin: '0px', threshold: 0.15 };
            const observer = new IntersectionObserver((entries, obs) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                        obs.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
            
            // Fix keyframes dynamically
            const style = document.createElement('style');
            style.textContent = \`@keyframes popIn { 0% { transform: scale(0); opacity: 0; } 80% { transform: scale(1.1); opacity: 1; } 100% { transform: scale(1); opacity: 1; } }\`;
            document.head.appendChild(style);
        });
    </script>
</body>
</html>`;

fs.writeFileSync(file, newHtml);
