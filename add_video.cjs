const fs = require('fs');

const file = '/Users/usuario/Downloads/Desarrollo/Dunkdalk/public/dundalk-landing.html';
let html = fs.readFileSync(file, 'utf8');

const regex = /<div class="relative">\s*<img src="[^"]+" alt="Dundalk Tires Shop" class="w-full h-\[500px\] object-cover rounded-sm grayscale opacity-80 border-b-8 border-brand-red">\s*<div class="absolute -bottom-8 -left-8 bg-brand-red p-8 hidden md:block shadow-2xl">\s*<div class="text-5xl font-heading font-extrabold mb-1">LOCAL & <br>TRUSTED<\/div>\s*<p class="text-white\/90 font-medium">Serving the Dundalk community<\/p>\s*<\/div>\s*<\/div>/;

const replacement = `<div class="relative shadow-xl rounded-sm">
                    <video 
                        src="/negocio/Dundalk-Corporate-Commercial-30s.mp4" 
                        controls 
                        preload="metadata"
                        class="w-full h-[500px] object-cover rounded-sm border-b-8 border-brand-red bg-gray-900"
                    ></video>
                    <div class="absolute -bottom-8 -left-8 bg-brand-red p-8 hidden md:block shadow-2xl pointer-events-none z-10">
                        <div class="text-5xl font-heading font-extrabold mb-1">LOCAL & <br>TRUSTED</div>
                        <p class="text-white/90 font-medium">Serving the Dundalk community</p>
                    </div>
                </div>`;

if (regex.test(html)) {
    html = html.replace(regex, replacement);
    fs.writeFileSync(file, html);
    console.log("Video added successfully!");
} else {
    console.log("Could not find the target HTML block.");
}
