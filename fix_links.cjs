const fs = require('fs');

const file = '/Users/usuario/Downloads/Desarrollo/Dunkdalk/public/dundalk-landing.html';
let html = fs.readFileSync(file, 'utf8');

// 1. Add id="about" to the Help Center / Local Trusted section
html = html.replace(
    /<section class="py-24 bg-brand-black text-white tire-diagram">/,
    '<section id="about" class="py-24 bg-brand-black text-white tire-diagram">'
);

// 2. Add the Wheel Alignment section after Core Services
const alignmentSection = `
    <!-- WHEEL ALIGNMENT SPECIALTY SECTION -->
    <section id="alignment" class="py-24 bg-white border-b border-gray-100 overflow-hidden">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-center">
                <!-- Text Content -->
                <div>
                    <h2 class="text-brand-red font-bold tracking-widest text-sm mb-2 uppercase">Specialized Service</h2>
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
                    
                    <div class="flex gap-4">
                        <a href="#book" class="bg-brand-red text-white px-8 py-3.5 font-heading text-xl font-bold rounded-sm shadow-md hover:bg-brand-redhover transition-colors">Book Alignment</a>
                    </div>
                </div>
                
                <!-- Visual / Image -->
                <div class="relative mt-8 lg:mt-0">
                    <!-- Base64 extracted from existing facade img in the document to reuse it -->
                    <div class="absolute inset-0 bg-brand-red transform translate-x-4 translate-y-4 rounded-sm"></div>
                    <div class="w-full h-[450px] bg-gray-200 relative z-10 rounded-sm border-4 border-white shadow-lg overflow-hidden bg-cover bg-center" style="background-image: url('data:image/jpeg;base64,');" id="alignment-bg-placeholder">
                        <div class="absolute inset-0 bg-brand-black/30 hover:bg-transparent transition-colors duration-500"></div>
                    </div>
                    
                    <!-- Data Tag -->
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
`;

// Insert the alignment section just after Core Services
html = html.replace(/<\/section>\s*<!-- SPECIAL OFFERS -->/, '</section>\n' + alignmentSection + '\n    <!-- SPECIAL OFFERS -->');

// Replace the placeholder background with the actual base64 image used for facade
const facadeMatch = html.match(/background-image: linear-gradient[^,]+,\s*url\('([^']+)'\)/); // Hero bg
const fallbackMatch = html.match(/<img src="([^"]+)" alt="Dundalk Tires Shop"/);
const finalImg = fallbackMatch ? fallbackMatch[1] : '';

if (finalImg) {
    html = html.replace(/id="alignment-bg-placeholder"/, '');
    html = html.replace(/style="background-image: url\('data:image\/jpeg;base64,'\);"/, `style="background-image: url('${finalImg}');"`);
}

// Ensure the Mobile Menu has About Us link as well
html = html.replace(
    /<a href="#alignment" class="block py-2 border-b border-gray-50">Wheel Alignment<\/a>/,
    '<a href="#alignment" class="block py-2 border-b border-gray-50">Wheel Alignment</a>\n                <a href="#about" class="block py-2 border-b border-gray-50">About Us</a>'
);

fs.writeFileSync(file, html);
