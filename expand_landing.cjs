const fs = require('fs');

const file = '/Users/usuario/Downloads/Desarrollo/Dunkdalk/public/dundalk-landing.html';
let html = fs.readFileSync(file, 'utf8');

// 1. TIRE BRANDS SECTION
const brandsSection = `
    <!-- TIRE BRANDS -->
    <section class="py-12 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6">
            <p class="text-center text-sm font-bold text-gray-400 uppercase tracking-widest mb-8">We carry all major brands</p>
            <div class="flex flex-wrap justify-center gap-8 md:gap-16 opacity-60">
                <!-- Brand typographic placeholders (until real logos are uploaded) -->
                <div class="text-2xl font-bold font-heading tracking-wider text-gray-500 hover:text-brand-red hover:opacity-100 transition-all cursor-default">MICHELIN</div>
                <div class="text-2xl font-bold font-heading tracking-wider text-gray-500 hover:text-brand-red hover:opacity-100 transition-all cursor-default">GOODYEAR</div>
                <div class="text-2xl font-bold font-heading tracking-wider text-gray-500 hover:text-brand-red hover:opacity-100 transition-all cursor-default">FIRESTONE</div>
                <div class="text-2xl font-bold font-heading tracking-wider text-gray-500 hover:text-brand-red hover:opacity-100 transition-all cursor-default">BRIDGESTONE</div>
                <div class="text-2xl font-bold font-heading tracking-wider text-gray-500 hover:text-brand-red hover:opacity-100 transition-all cursor-default">CONTINENTAL</div>
                <div class="text-2xl font-bold font-heading tracking-wider text-gray-500 hover:text-brand-red hover:opacity-100 transition-all cursor-default">PIRELLI</div>
            </div>
        </div>
    </section>
`;

// Insert brands right after the Hero
html = html.replace(/<\/header>/, '</header>\n' + brandsSection);

// 2. FEATURED TIRES (Search Results Preview)
const featuredTiresSection = `
    <!-- FEATURED INVENTORY -->
    <section id="tires" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12">
                <div>
                    <h2 class="font-heading text-4xl md:text-5xl font-extrabold text-brand-black uppercase">CURRENT <span class="text-brand-red">INVENTORY</span></h2>
                    <div class="w-20 h-1.5 bg-brand-red mt-4"></div>
                </div>
                <div class="mt-6 md:mt-0 flex gap-4">
                    <button class="px-6 py-2 border-2 border-brand-black text-brand-black font-bold uppercase text-sm hover:bg-brand-black hover:text-white transition-colors">View All New</button>
                    <button class="px-6 py-2 border-2 border-brand-black text-brand-black font-bold uppercase text-sm hover:bg-brand-black hover:text-white transition-colors">View All Used</button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Tire Card 1 -->
                <div class="bg-white border border-gray-200 p-6 rounded-md shadow-sm hover:shadow-xl transition-all group relative">
                    <div class="absolute top-6 left-6 bg-green-500 text-white text-xs font-bold px-3 py-1 uppercase tracking-wider rounded-sm z-10">NEW IN STOCK</div>
                    
                    <div class="aspect-square bg-gray-100 rounded-sm mb-6 flex items-center justify-center p-8 relative overflow-hidden">
                        <!-- Abstract Tire SVG Placeholder -->
                        <svg class="w-32 h-32 text-gray-300 group-hover:scale-110 transition-transform duration-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12,2C6.48,2 2,6.48 2,12C2,17.52 6.48,22 12,22C17.52,22 22,17.52 22,12C22,6.48 17.52,2 12,2M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,6A6,6 0 0,0 6,12A6,6 0 0,0 12,18A6,6 0 0,0 18,12A6,6 0 0,0 12,6M12,16A4,4 0 0,1 8,12A4,4 0 0,1 12,8A4,4 0 0,1 16,12A4,4 0 0,1 12,16M12,9.5A2.5,2.5 0 0,0 9.5,12A2.5,2.5 0 0,0 12,14.5A2.5,2.5 0 0,0 14.5,12A2.5,2.5 0 0,0 12,9.5Z" /></svg>
                    </div>
                    
                    <div class="mb-2">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Michelin</span>
                    </div>
                    <h3 class="text-2xl font-heading font-bold text-gray-900 mb-1">Defender T+H</h3>
                    <p class="text-brand-red font-bold font-mono text-lg mb-4">215/55R17</p>
                    
                    <ul class="text-sm text-gray-500 mb-6 space-y-2">
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> All-Season Passenger</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> 80,000 Mile Warranty</li>
                    </ul>
                    
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div>
                            <span class="text-xs text-gray-400 block mb-1">Starting at</span>
                            <span class="text-2xl font-bold text-gray-900">$145.99</span>
                        </div>
                        <button class="bg-brand-black text-white px-4 py-2 text-sm font-bold uppercase hover:bg-brand-red transition-colors rounded-sm">Get Quote</button>
                    </div>
                </div>

                <!-- Tire Card 2 -->
                <div class="bg-white border border-gray-200 p-6 rounded-md shadow-sm hover:shadow-xl transition-all group relative">
                    <div class="absolute top-6 left-6 bg-brand-black text-white text-xs font-bold px-3 py-1 uppercase tracking-wider rounded-sm z-10">USED (GRADE A)</div>
                    
                    <div class="aspect-square bg-gray-100 rounded-sm mb-6 flex items-center justify-center p-8 relative overflow-hidden">
                        <svg class="w-32 h-32 text-gray-300 group-hover:scale-110 transition-transform duration-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12,2C6.48,2 2,6.48 2,12C2,17.52 6.48,22 12,22C17.52,22 22,17.52 22,12C22,6.48 17.52,2 12,2M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,6A6,6 0 0,0 6,12A6,6 0 0,0 12,18A6,6 0 0,0 18,12A6,6 0 0,0 12,6M12,16A4,4 0 0,1 8,12A4,4 0 0,1 12,8A4,4 0 0,1 16,12A4,4 0 0,1 12,16M12,9.5A2.5,2.5 0 0,0 9.5,12A2.5,2.5 0 0,0 12,14.5A2.5,2.5 0 0,0 14.5,12A2.5,2.5 0 0,0 12,9.5Z" /></svg>
                    </div>
                    
                    <div class="mb-2">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Goodyear</span>
                    </div>
                    <h3 class="text-2xl font-heading font-bold text-gray-900 mb-1">Assurance All-Season</h3>
                    <p class="text-brand-red font-bold font-mono text-lg mb-4">205/60R16</p>
                    
                    <ul class="text-sm text-gray-500 mb-6 space-y-2">
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Limited Availability</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> 70%+ Tread Remaining</li>
                    </ul>
                    
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div>
                            <span class="text-xs text-gray-400 block mb-1">Estimated at</span>
                            <span class="text-2xl font-bold text-gray-900">$45.00</span>
                        </div>
                        <button class="bg-brand-black text-white px-4 py-2 text-sm font-bold uppercase hover:bg-brand-red transition-colors rounded-sm">Call Now</button>
                    </div>
                </div>

                <!-- Tire Card 3 -->
                <div class="bg-white border border-gray-200 p-6 rounded-md shadow-sm hover:shadow-xl transition-all group relative">
                    <div class="absolute top-6 left-6 bg-green-500 text-white text-xs font-bold px-3 py-1 uppercase tracking-wider rounded-sm z-10">NEW IN STOCK</div>
                    
                    <div class="aspect-square bg-gray-100 rounded-sm mb-6 flex items-center justify-center p-8 relative overflow-hidden">
                        <svg class="w-32 h-32 text-gray-300 group-hover:scale-110 transition-transform duration-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12,2C6.48,2 2,6.48 2,12C2,17.52 6.48,22 12,22C17.52,22 22,17.52 22,12C22,6.48 17.52,2 12,2M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,6A6,6 0 0,0 6,12A6,6 0 0,0 12,18A6,6 0 0,0 18,12A6,6 0 0,0 12,6M12,16A4,4 0 0,1 8,12A4,4 0 0,1 12,8A4,4 0 0,1 16,12A4,4 0 0,1 12,16M12,9.5A2.5,2.5 0 0,0 9.5,12A2.5,2.5 0 0,0 12,14.5A2.5,2.5 0 0,0 14.5,12A2.5,2.5 0 0,0 12,9.5Z" /></svg>
                    </div>
                    
                    <div class="mb-2">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Firestone</span>
                    </div>
                    <h3 class="text-2xl font-heading font-bold text-gray-900 mb-1">Destination LE3</h3>
                    <p class="text-brand-red font-bold font-mono text-lg mb-4">225/65R17</p>
                    
                    <ul class="text-sm text-gray-500 mb-6 space-y-2">
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> SUV / Crossover Highway</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> 70,000 Mile Warranty</li>
                    </ul>
                    
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div>
                            <span class="text-xs text-gray-400 block mb-1">Starting at</span>
                            <span class="text-2xl font-bold text-gray-900">$165.99</span>
                        </div>
                        <button class="bg-brand-black text-white px-4 py-2 text-sm font-bold uppercase hover:bg-brand-red transition-colors rounded-sm">Get Quote</button>
                    </div>
                </div>
            </div>
            
            <div class="mt-12 text-center bg-white border border-gray-200 p-6 rounded-md">
                <p class="text-gray-600 font-medium">Looking for a specific tire? <a href="tel:6677273115" class="text-brand-red font-bold ml-2 underline decoration-2 underline-offset-4 hover:text-brand-black transition-colors">Call us to check live stock</a></p>
            </div>
        </div>
    </section>
`;

// Insert Featured Tires after the Advantage section
html = html.replace(/<\/section>\s*<!-- CORE SERVICES -->/, '</section>\n' + featuredTiresSection + '\n    <!-- CORE SERVICES -->');


// 3. PROMOTIONS & OFFERS SECTION
const promosSection = `
    <!-- SPECIAL OFFERS -->
    <section class="py-20 bg-brand-red text-white relative overflow-hidden">
        <!-- Background graphic -->
        <div class="absolute right-0 top-0 opacity-10 pointer-events-none transform translate-x-1/4 -translate-y-1/4">
            <svg class="w-96 h-96" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        </div>

        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="text-center mb-12">
                <h2 class="font-heading text-4xl md:text-5xl font-extrabold uppercase mb-4">CURRENT <span class="text-brand-black">OFFERS</span></h2>
                <p class="text-white/80 font-medium text-lg">Save more on your next visit to Dundalk Tires.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Promo 1 -->
                <div class="bg-white/10 backdrop-blur-sm border border-white/20 p-8 md:p-10 rounded-sm hover:bg-white/20 transition-colors">
                    <div class="bg-brand-black text-white text-xs font-bold uppercase tracking-widest px-3 py-1 inline-block mb-4 rounded-sm">SERVICE DEAL</div>
                    <h3 class="text-3xl font-heading font-bold mb-4">FREE Alignment Check</h3>
                    <p class="text-white/90 mb-8 font-light leading-relaxed">Get a free computerized wheel alignment check when you purchase any set of 4 new tires. Ensure your new investment lasts longer.</p>
                    <a href="#book" class="inline-block border-2 border-white text-white font-bold uppercase tracking-widest text-sm px-8 py-3 hover:bg-white hover:text-brand-red transition-colors rounded-sm">Claim Offer</a>
                </div>

                <!-- Promo 2 -->
                <div class="bg-white/10 backdrop-blur-sm border border-white/20 p-8 md:p-10 rounded-sm hover:bg-white/20 transition-colors">
                    <div class="bg-brand-black text-white text-xs font-bold uppercase tracking-widest px-3 py-1 inline-block mb-4 rounded-sm">USED TIRE SPECIAL</div>
                    <h3 class="text-3xl font-heading font-bold mb-4">$15 OFF Any Used Pair</h3>
                    <p class="text-white/90 mb-8 font-light leading-relaxed">Need a quick replacement? Buy any 2 used tires and get $15 off the total price. Includes mounting and standard balancing.</p>
                    <a href="#book" class="inline-block border-2 border-white text-white font-bold uppercase tracking-widest text-sm px-8 py-3 hover:bg-white hover:text-brand-red transition-colors rounded-sm">Claim Offer</a>
                </div>
            </div>
        </div>
    </section>
`;

// Insert Promos after Core Services
html = html.replace(/<\/section>\s*<!-- TIRE EDUCATION & TRUST -->/, '</section>\n' + promosSection + '\n    <!-- TIRE EDUCATION & TRUST -->');


// 4. REVIEWS & TRUST SECTION
const reviewsSection = `
    <!-- GOOGLE REVIEWS -->
    <section class="py-24 bg-gray-50 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <div class="flex justify-center items-center gap-2 mb-4">
                    <svg class="w-8 h-8 text-yellow-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                    <svg class="w-8 h-8 text-yellow-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                    <svg class="w-8 h-8 text-yellow-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                    <svg class="w-8 h-8 text-yellow-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                    <svg class="w-8 h-8 text-yellow-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                </div>
                <h2 class="font-heading text-4xl md:text-5xl font-extrabold text-brand-black uppercase">WHAT OUR <span class="text-brand-red">CUSTOMERS SAY</span></h2>
                <div class="w-20 h-1.5 bg-brand-red mx-auto mt-4"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 border border-gray-200 rounded-sm shadow-sm relative">
                    <svg class="w-10 h-10 text-gray-200 absolute top-6 right-6" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                    <div class="flex gap-1 text-yellow-400 mb-4">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                    </div>
                    <p class="text-gray-600 mb-6 italic leading-relaxed relative z-10">"Got a flat on my way to work. They patched it up in under 15 minutes. Very professional, no upselling, just honest mechanics. Highly recommend this local shop."</p>
                    <div class="font-bold text-gray-900">- Mark D.</div>
                </div>

                <div class="bg-white p-8 border border-gray-200 rounded-sm shadow-sm relative">
                    <svg class="w-10 h-10 text-gray-200 absolute top-6 right-6" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                    <div class="flex gap-1 text-yellow-400 mb-4">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                    </div>
                    <p class="text-gray-600 mb-6 italic leading-relaxed relative z-10">"Bought a pair of used tires for my Honda. They looked practically brand new and the price was unbeatable. Installation was fast and they balanced everything perfectly."</p>
                    <div class="font-bold text-gray-900">- Sarah T.</div>
                </div>

                <div class="bg-white p-8 border border-gray-200 rounded-sm shadow-sm relative">
                    <svg class="w-10 h-10 text-gray-200 absolute top-6 right-6" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                    <div class="flex gap-1 text-yellow-400 mb-4">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                    </div>
                    <p class="text-gray-600 mb-6 italic leading-relaxed relative z-10">"Best wheel alignment in Dundalk. The car drives straight as an arrow now. The facility is clean and the guys at the front desk are super friendly."</p>
                    <div class="font-bold text-gray-900">- Kevin R.</div>
                </div>
            </div>
        </div>
    </section>
`;

// Insert Reviews after Education
html = html.replace(/<\/section>\s*<!-- FOOTER -->/, '</section>\n' + reviewsSection + '\n    <!-- FOOTER -->');


// 5. APPOINTMENT / QUOTE SECTION
const appointmentSection = `
    <!-- BOOK APPOINTMENT / GET QUOTE -->
    <section id="book" class="py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-6">
            <div class="bg-brand-black rounded-sm shadow-2xl overflow-hidden flex flex-col lg:flex-row">
                
                <!-- Left Info Panel -->
                <div class="lg:w-1/3 bg-brand-red text-white p-10 flex flex-col justify-between relative overflow-hidden">
                    <div class="absolute right-0 top-0 opacity-10 pointer-events-none transform translate-x-1/4 -translate-y-1/4">
                        <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24"><path d="M12,2C6.48,2 2,6.48 2,12C2,17.52 6.48,22 12,22C17.52,22 22,17.52 22,12C22,6.48 17.52,2 12,2M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20Z"/></svg>
                    </div>
                    
                    <div class="relative z-10">
                        <h2 class="font-heading text-4xl font-bold mb-6">READY TO ROLL?</h2>
                        <p class="text-white/90 mb-8 font-medium">Request a quote or schedule your service. Fast response guaranteed during business hours.</p>
                        
                        <div class="space-y-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <div class="text-xs uppercase tracking-widest opacity-80 font-bold mb-1">Fast Service</div>
                                    <div class="font-medium">Same-day availability for most repairs</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <div class="text-xs uppercase tracking-widest opacity-80 font-bold mb-1">Quality Guaranteed</div>
                                    <div class="font-medium">Inspected & warranted products</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Form Panel -->
                <div class="lg:w-2/3 p-10 lg:p-16">
                    <h3 class="font-heading text-3xl font-bold text-white mb-8">Request an Appointment or Quote</h3>
                    
                    <form class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Service Needed -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wide">Service Needed</label>
                            <select class="w-full bg-brand-dark border border-gray-700 text-white rounded-sm py-3 px-4 focus:outline-none focus:border-brand-red">
                                <option>Tire Installation (New or Used)</option>
                                <option>Wheel Alignment</option>
                                <option>Wheel Balancing</option>
                                <option>Tire Repair (Flat)</option>
                                <option>General Auto Repair / Oil Change</option>
                            </select>
                        </div>
                        
                        <!-- Vehicle Details -->
                        <div>
                            <label class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wide">Vehicle (Year/Make/Model)</label>
                            <input type="text" placeholder="e.g. 2024 Honda Civic" class="w-full bg-brand-dark border border-gray-700 text-white rounded-sm py-3 px-4 focus:outline-none focus:border-brand-red">
                        </div>
                        
                        <!-- Tire Size -->
                        <div>
                            <label class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wide">Tire Size (if known)</label>
                            <input type="text" placeholder="e.g. 215/55R17" class="w-full bg-brand-dark border border-gray-700 text-white rounded-sm py-3 px-4 focus:outline-none focus:border-brand-red">
                        </div>

                        <!-- Name -->
                        <div>
                            <label class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wide">Your Name</label>
                            <input type="text" placeholder="John Doe" class="w-full bg-brand-dark border border-gray-700 text-white rounded-sm py-3 px-4 focus:outline-none focus:border-brand-red">
                        </div>
                        
                        <!-- Phone -->
                        <div>
                            <label class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wide">Phone Number</label>
                            <input type="tel" placeholder="(---) --- ----" class="w-full bg-brand-dark border border-gray-700 text-white rounded-sm py-3 px-4 focus:outline-none focus:border-brand-red">
                        </div>

                        <!-- Submit -->
                        <div class="md:col-span-2 mt-4">
                            <button type="button" class="bg-brand-red text-white px-8 py-4 font-heading text-xl font-bold rounded-sm w-full md:w-auto hover:bg-brand-redhover transition-colors shadow-lg">Submit Request</button>
                            <p class="text-gray-500 text-sm mt-4">For immediate assistance, please call us directly at <a href="tel:6677273115" class="text-brand-red">667-727-3115</a>.</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
`;

// Insert Appointment before Footer
html = html.replace(/<\/section>\s*<!-- FOOTER -->/, '</section>\n' + appointmentSection + '\n    <!-- FOOTER -->');

fs.writeFileSync(file, html);
