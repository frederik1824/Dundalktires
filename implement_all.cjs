const fs = require('fs');
const file = '/Users/usuario/Downloads/Desarrollo/Dunkdalk/public/dundalk-landing.html';
let html = fs.readFileSync(file, 'utf8');

// ==========================================
// 1. A - FUNCTIONAL TIRE FINDER FILTERING
// ==========================================
// Update body x-data to include searchQuery
html = html.replace(
    /x-data="\{\s*mobileMenu:\s*false,\s*scrolled:\s*false\s*\}"/,
    'x-data="{ mobileMenu: false, scrolled: false, searchQuery: \'\' }"'
);

// Update "SHOW TIRES" button in Tire Finder (By Size)
html = html.replace(
    /<button class="w-full bg-brand-red text-white py-4 font-heading text-2xl font-bold rounded-sm hover:bg-brand-redhover transition-colors shadow-md">\s*SHOW TIRES\s*<\/button>/,
    `<button @click="searchQuery = document.getElementById('sizeW').value + '/' + document.getElementById('sizeR').value + 'R' + document.getElementById('sizeD').value; if(searchQuery === '//R') searchQuery = document.getElementById('manualSize').value; window.location.hash = '#tires';" class="w-full bg-brand-red text-white py-4 font-heading text-2xl font-bold rounded-sm hover:bg-brand-redhover transition-colors shadow-md">
        SHOW TIRES
    </button>`
);

// Add IDs to the selects so we can grab their values easily without complex Alpine binding in this static setup
html = html.replace(/<select class="w-full border border-gray-300 rounded-sm py-3 px-2 bg-white focus:outline-none focus:border-brand-red text-center">\s*<option value="">215<\/option>\s*<\/select>/,
    `<select id="sizeW" class="w-full border border-gray-300 rounded-sm py-3 px-2 bg-white focus:outline-none focus:border-brand-red text-center"><option value="">W</option><option value="205">205</option><option value="215">215</option><option value="225">225</option></select>`);
html = html.replace(/<select class="w-full border border-gray-300 rounded-sm py-3 px-2 bg-white focus:outline-none focus:border-brand-red text-center">\s*<option value="">55<\/option>\s*<\/select>/,
    `<select id="sizeR" class="w-full border border-gray-300 rounded-sm py-3 px-2 bg-white focus:outline-none focus:border-brand-red text-center"><option value="">R</option><option value="55">55</option><option value="60">60</option><option value="65">65</option></select>`);
html = html.replace(/<select class="w-full border border-gray-300 rounded-sm py-3 px-2 bg-white focus:outline-none focus:border-brand-red text-center">\s*<option value="">17<\/option>\s*<\/select>/,
    `<select id="sizeD" class="w-full border border-gray-300 rounded-sm py-3 px-2 bg-white focus:outline-none focus:border-brand-red text-center"><option value="">D</option><option value="16">16</option><option value="17">17</option><option value="18">18</option></select>`);
html = html.replace(/<input type="text" placeholder="e.g. 215\/55R17" class="w-full mt-2 border border-gray-300 rounded-sm py-3 px-4 focus:outline-none focus:border-brand-red text-center font-bold">/,
    `<input type="text" id="manualSize" placeholder="e.g. 215/55R17" class="w-full mt-2 border border-gray-300 rounded-sm py-3 px-4 focus:outline-none focus:border-brand-red text-center font-bold">`);


// Wrap Tire Cards in x-show logic
html = html.replace(/<!-- Tire Card 1 -->\s*<div class="bg-white border/, `<!-- Tire Card 1 -->\n<div x-show="searchQuery === '' || searchQuery === '215/55R17'" class="bg-white border`);
html = html.replace(/<!-- Tire Card 2 -->\s*<div class="bg-white border/, `<!-- Tire Card 2 -->\n<div x-show="searchQuery === '' || searchQuery === '205/60R16'" class="bg-white border`);
html = html.replace(/<!-- Tire Card 3 -->\s*<div class="bg-white border/, `<!-- Tire Card 3 -->\n<div x-show="searchQuery === '' || searchQuery === '225/65R17'" class="bg-white border`);

// Add Empty State to Catalog
html = html.replace(/<\/div>\s*<div class="mt-12 text-center bg-white border border-gray-200 p-6 rounded-md">/, 
`    <!-- Empty State -->
     <div x-show="searchQuery !== '' && searchQuery !== '215/55R17' && searchQuery !== '205/60R16' && searchQuery !== '225/65R17'" class="col-span-full py-16 text-center bg-gray-100 rounded-md border-2 border-dashed border-gray-300" style="display: none;">
         <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
         <h3 class="text-2xl font-heading font-bold text-gray-900 mb-2">Size <span x-text="searchQuery" class="text-brand-red"></span> not found online</h3>
         <p class="text-gray-500 mb-6">We have hundreds of tires in our local warehouse not listed here.</p>
         <div class="flex justify-center gap-4">
             <button @click="searchQuery = ''" class="px-6 py-2 border border-gray-300 font-bold uppercase text-sm hover:bg-gray-200 transition-colors">Clear Search</button>
             <a href="tel:6677273115" class="px-6 py-2 bg-brand-black text-white font-bold uppercase text-sm hover:bg-brand-red transition-colors">Call Us to Check</a>
         </div>
     </div>
</div>
<div class="mt-12 text-center bg-white border border-gray-200 p-6 rounded-md">`
);


// ==========================================
// 2. B - EXPAND SERVICES (Balancing & Repair)
// ==========================================
const extraServices = `
    <!-- WHEEL BALANCING & REPAIR -->
    <section id="balancing-repair" class="grid grid-cols-1 md:grid-cols-2">
        <!-- Balancing (Dark) -->
        <div class="bg-brand-black text-white py-20 px-6 md:px-16 flex flex-col justify-center">
            <h2 class="text-brand-red font-bold tracking-widest text-sm mb-2 uppercase">Dynamic Balancing</h2>
            <h3 class="font-heading text-4xl font-extrabold mb-6">ELIMINATE <br>VIBRATIONS</h3>
            <p class="text-gray-400 mb-6 leading-relaxed">If you feel a vibration in your steering wheel at highway speeds, your tires are likely out of balance. We use precision dynamic balancing machines to distribute weight perfectly across the wheel assembly.</p>
            <ul class="space-y-3 text-sm text-gray-300 mb-8 font-medium">
                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Smoother highway driving</li>
                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Prevents premature suspension wear</li>
            </ul>
        </div>
        <!-- Repair (Light) -->
        <div class="bg-brand-gray py-20 px-6 md:px-16 flex flex-col justify-center border-l border-gray-200">
            <h2 class="text-brand-red font-bold tracking-widest text-sm mb-2 uppercase">Safe Flat Repair</h2>
            <h3 class="font-heading text-4xl font-extrabold mb-6 text-brand-black">PROFESSIONAL <br>PATCHING</h3>
            <p class="text-gray-600 mb-6 leading-relaxed">Not all flat tires need to be replaced. If a puncture is within the central tread area, our experts can safely patch it from the inside out, strictly following industry safety standards to get you back on the road safely and cheaply.</p>
            <ul class="space-y-3 text-sm text-gray-600 mb-8 font-medium">
                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Fast 15-minute turnaround</li>
                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Safer than temporary plugs</li>
            </ul>
        </div>
    </section>
`;

html = html.replace(/<\/section>\s*<!-- SPECIAL OFFERS -->/, '</section>\n' + extraServices + '\n    <!-- SPECIAL OFFERS -->');


// ==========================================
// 3. C - FAQ SECTION
// ==========================================
const faqSection = `
    <!-- FAQ SECTION -->
    <section class="py-24 bg-white border-t border-gray-100">
        <div class="max-w-4xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="font-heading text-4xl font-extrabold text-brand-black uppercase">FREQUENTLY ASKED <span class="text-brand-red">QUESTIONS</span></h2>
                <div class="w-16 h-1.5 bg-brand-red mx-auto mt-4"></div>
            </div>

            <div class="space-y-4" x-data="{ active: null }">
                <!-- Q1 -->
                <div class="border border-gray-200 rounded-sm">
                    <button @click="active = active === 1 ? null : 1" class="w-full flex justify-between items-center p-6 text-left focus:outline-none hover:bg-gray-50 transition-colors">
                        <span class="font-bold text-gray-900 text-lg">How long does a tire installation take?</span>
                        <svg class="w-6 h-6 text-brand-red transform transition-transform" :class="active === 1 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="active === 1" x-collapse class="px-6 pb-6 text-gray-600" style="display: none;">
                        Usually, a full set of 4 tires takes about 45 to 60 minutes to mount, balance, and install on your vehicle. Single tire replacements or simple flat repairs are often completed in under 20 minutes!
                    </div>
                </div>
                <!-- Q2 -->
                <div class="border border-gray-200 rounded-sm">
                    <button @click="active = active === 2 ? null : 2" class="w-full flex justify-between items-center p-6 text-left focus:outline-none hover:bg-gray-50 transition-colors">
                        <span class="font-bold text-gray-900 text-lg">Do you offer warranties on used tires?</span>
                        <svg class="w-6 h-6 text-brand-red transform transition-transform" :class="active === 2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="active === 2" x-collapse class="px-6 pb-6 text-gray-600" style="display: none;">
                        Yes! All our used tires are rigorously hand-inspected for safety, tread depth, and structural integrity. We stand behind our used inventory and offer a 30-day guarantee against any structural defects.
                    </div>
                </div>
                <!-- Q3 -->
                <div class="border border-gray-200 rounded-sm">
                    <button @click="active = active === 3 ? null : 3" class="w-full flex justify-between items-center p-6 text-left focus:outline-none hover:bg-gray-50 transition-colors">
                        <span class="font-bold text-gray-900 text-lg">Do I need an appointment?</span>
                        <svg class="w-6 h-6 text-brand-red transform transition-transform" :class="active === 3 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="active === 3" x-collapse class="px-6 pb-6 text-gray-600" style="display: none;">
                        Walk-ins are always welcome! However, booking an appointment online or calling ahead guarantees your time slot and significantly reduces any potential waiting time, especially for wheel alignments.
                    </div>
                </div>
            </div>
        </div>
    </section>
`;

html = html.replace(/<!-- BOOK APPOINTMENT \/ GET QUOTE -->/, faqSection + '\n    <!-- BOOK APPOINTMENT / GET QUOTE -->');


// ==========================================
// 4. D - MAPS & TRUST BADGES IN FOOTER
// ==========================================
const footerTopHTML = `
    <!-- MAPS & BADGES PRE-FOOTER -->
    <div class="border-t border-gray-200">
        <div class="w-full h-80 bg-gray-200 relative">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3089.444265935398!2d-76.51608688463131!3d39.2555562795318!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c80f4a8cb2c619%3A0xc3b8a1c97a22ab8!2s7888%20Dundalk%20Ave%2C%20Dundalk%2C%20MD%2021222!5e0!3m2!1sen!2sus!4v1689871234567!5m2!1sen!2sus" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="absolute inset-0 grayscale contrast-125 opacity-80 hover:grayscale-0 hover:opacity-100 transition-all duration-700"></iframe>
        </div>
        <div class="bg-gray-50 py-6 border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex gap-8 items-center text-gray-400">
                    <div class="flex items-center gap-2 font-bold uppercase tracking-widest text-xs"><svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg> Satisfaction Guaranteed</div>
                    <div class="flex items-center gap-2 font-bold uppercase tracking-widest text-xs"><svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg> Locally Owned & Operated</div>
                </div>
                <div class="flex gap-4 opacity-60">
                    <!-- Text payment badges -->
                    <span class="px-3 py-1 bg-gray-200 text-gray-700 rounded text-xs font-bold font-sans">VISA</span>
                    <span class="px-3 py-1 bg-gray-200 text-gray-700 rounded text-xs font-bold font-sans">MASTERCARD</span>
                    <span class="px-3 py-1 bg-gray-200 text-gray-700 rounded text-xs font-bold font-sans">AMEX</span>
                    <span class="px-3 py-1 bg-gray-200 text-gray-700 rounded text-xs font-bold font-sans">APPLE PAY</span>
                </div>
            </div>
        </div>
    </div>
    <!-- FOOTER -->`;

html = html.replace(/<!-- FOOTER -->/, footerTopHTML);

fs.writeFileSync(file, html);
console.log("All Phase 3 features implemented!");
