const fs = require('fs');

const file = '/Users/usuario/Downloads/Desarrollo/Dunkdalk/public/dundalk-landing.html';
let html = fs.readFileSync(file, 'utf8');

// 1. Add icons to Services cards and improve their styling
const servicesSection = `
    <!-- Services Section -->
    <section id="services" class="py-24 bg-gray-50 relative overflow-hidden">
        <!-- Subtle background pattern -->
        <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 24px 24px;"></div>
        
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-extrabold text-brand-black mb-4 tracking-wide">EXPERT <span class="text-brand-red">SERVICES</span></h2>
                <div class="w-24 h-1.5 bg-brand-red mx-auto rounded-full"></div>
                <p class="text-gray-500 mt-6 max-w-2xl mx-auto text-lg">We offer comprehensive tire care and wheel services to keep you safe on the road. Fast turnaround, no hidden fees.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Service 1 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl border-b-4 border-brand-black hover:border-brand-red transition-all duration-300 transform hover:-translate-y-1">
                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center text-brand-black mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"/></svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-3 text-gray-900">Tire Plug & Patch</h3>
                    <p class="text-gray-600 leading-relaxed">Quick and safe flat tire repairs using professional plug and patch methods to get you back driving.</p>
                </div>
                
                <!-- Service 2 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl border-b-4 border-brand-black hover:border-brand-red transition-all duration-300 transform hover:-translate-y-1">
                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center text-brand-black mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-3 text-gray-900">Mounting & Rotation</h3>
                    <p class="text-gray-600 leading-relaxed">Professional tire mounting and regular rotation to extend your tire's lifespan and ensure even wear.</p>
                </div>
                
                <!-- Service 3 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl border-b-4 border-brand-black hover:border-brand-red transition-all duration-300 transform hover:-translate-y-1">
                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center text-brand-black mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-3 text-gray-900">Wheel Balancing</h3>
                    <p class="text-gray-600 leading-relaxed">Precision computer balancing for a smooth, vibration-free ride at all highway speeds.</p>
                </div>
                
                <!-- Service 4 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl border-b-4 border-brand-black hover:border-brand-red transition-all duration-300 transform hover:-translate-y-1">
                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center text-brand-black mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-3 text-gray-900">Rims & Stems</h3>
                    <p class="text-gray-600 leading-relaxed">Valve stem replacement, rim cleaning, sealing leaks, and basic wheel maintenance services.</p>
                </div>
                
                <!-- Service 5 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg border-b-4 border-brand-red bg-gradient-to-br from-white to-red-50 hover:to-red-100 transition-all duration-300 transform hover:-translate-y-1 relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 text-brand-red opacity-10">
                        <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
                    </div>
                    <div class="w-14 h-14 bg-brand-red rounded-full flex items-center justify-center text-white mb-6 relative z-10">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-3 text-brand-red relative z-10">New Tires</h3>
                    <p class="text-gray-800 font-medium leading-relaxed relative z-10">Top brands and sizes available in stock. Unbeatable prices on brand new sets with warranty.</p>
                </div>
                
                <!-- Service 6 -->
                <div class="bg-brand-black p-8 rounded-2xl shadow-lg border-b-4 border-brand-red text-white hover:bg-gray-900 transition-all duration-300 transform hover:-translate-y-1">
                    <div class="w-14 h-14 bg-gray-800 rounded-full flex items-center justify-center text-brand-red mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-3 text-brand-red">Used Tires</h3>
                    <p class="text-gray-300 leading-relaxed">High-quality, hand-inspected used tires with plenty of tread left. The best value for any budget.</p>
                </div>
            </div>
        </div>
    </section>`;

// Replace old services section
html = html.replace(/<!-- Services Section -->[\s\S]*?<!-- About Section -->/, servicesSection + '\n\n    <!-- About Section -->');

// 2. Polish About Section layout
const aboutSection = `
    <!-- About Section -->
    <section id="about" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div class="relative group">
                    <!-- Decorative background block -->
                    <div class="absolute inset-0 bg-brand-red rounded-2xl transform translate-x-4 translate-y-4 transition-transform group-hover:translate-x-6 group-hover:translate-y-6"></div>
                    <div class="rounded-2xl overflow-hidden shadow-xl relative z-10 bg-white">
                        <img src="$1" alt="Dundalk Tires Exterior" class="w-full h-auto object-cover transform group-hover:scale-105 transition-transform duration-700">
                    </div>
                </div>
                <div class="lg:pl-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-1 bg-brand-red"></div>
                        <span class="text-brand-red font-bold tracking-wider uppercase text-sm">About Our Shop</span>
                    </div>
                    <h2 class="text-4xl md:text-5xl font-extrabold text-brand-black mb-6 leading-tight">LOCAL, HONEST & <span class="text-brand-red">TRUSTED</span></h2>
                    <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                        Dundalk Used & New Tires is your neighborhood shop dedicated to keeping you safe on the road. We know how important your vehicle is, which is why we provide fast, honest, and high-quality tire services without the upselling.
                    </p>
                    <p class="text-lg text-gray-600 mb-10 leading-relaxed">
                        Whether you need a quick patch for a flat, or a whole new set of wheels, our experienced technicians have you covered. Stop by and experience the best service in MD!
                    </p>
                    <a href="tel:6677273115" class="inline-flex items-center gap-2 bg-brand-black text-brand-white px-8 py-4 font-heading text-xl font-bold rounded-lg shadow-lg hover:bg-gray-800 hover:-translate-y-1 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        CALL US TODAY
                    </a>
                </div>
            </div>
        </div>
    </section>`;
html = html.replace(/<!-- About Section -->[\s\S]*?<!-- Footer \/ Contact -->/, aboutSection + '\n\n    <!-- Footer / Contact -->');

// 3. Extract About Image from previous code to inject it in the new regex
const aboutImgMatch = html.match(/<img src="([^"]+)" alt="Dundalk Tires Exterior"/);
if (aboutImgMatch) {
    html = html.replace(/\$1/, aboutImgMatch[1]);
}

// 4. Polish Navigation Buttons
html = html.replace(
    /class="bg-brand-red text-brand-white px-5 py-2 font-heading text-xl font-bold rounded shadow hover:bg-red-700 transition-colors"/,
    'class="bg-brand-red text-brand-white px-6 py-2.5 font-heading text-xl font-bold rounded-lg shadow-lg hover:bg-red-700 hover:shadow-red-500/30 hover:-translate-y-0.5 transition-all"'
);

// 5. Polish Hero Button
html = html.replace(
    /class="bg-brand-red text-brand-white px-8 py-3 font-heading text-2xl font-bold rounded shadow-lg hover:bg-red-700 transition-transform transform hover:-translate-y-1"/,
    'class="inline-block bg-brand-red text-brand-white px-10 py-4 font-heading text-2xl font-bold rounded-lg shadow-lg shadow-red-500/20 hover:bg-red-700 transition-all transform hover:-translate-y-1"'
);

// 6. Make Hero Text a bit sharper
html = html.replace(
    /<h1 class="text-5xl md:text-7xl font-extrabold text-brand-white mb-4 leading-tight tracking-wide">/,
    '<h1 class="text-6xl md:text-8xl font-extrabold text-brand-white mb-4 leading-[1.1] tracking-wide">'
);
html = html.replace(
    /<p class="text-xl md:text-2xl text-gray-200 font-body mb-8 max-w-xl">/,
    '<p class="text-xl md:text-2xl text-gray-200 font-body mb-10 max-w-xl leading-relaxed">'
);

fs.writeFileSync(file, html);
