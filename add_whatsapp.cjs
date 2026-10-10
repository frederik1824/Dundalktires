const fs = require('fs');

const file = '/Users/usuario/Downloads/Desarrollo/Dunkdalk/public/dundalk-landing.html';
let html = fs.readFileSync(file, 'utf8');

const whatsappWidget = `
    <!-- FLOATING WHATSAPP BUTTON -->
    <div class="fixed z-50 bottom-24 right-4 md:bottom-8 md:right-8 flex flex-col items-end group">
        <!-- Tooltip Bubble -->
        <div class="bg-white text-gray-800 text-sm font-bold py-2.5 px-4 rounded-xl shadow-[0_10px_25px_rgba(0,0,0,0.15)] mb-3 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 hidden md:block border border-gray-100">
            Need Tires? Chat with us!
            <!-- Triangle pointer -->
            <div class="absolute -bottom-2 right-6 w-0 h-0 border-l-[8px] border-l-transparent border-t-[10px] border-t-white border-r-[8px] border-r-transparent"></div>
        </div>
        
        <!-- Button -->
        <a href="https://wa.me/16677273115?text=Hello!%20I%20saw%20your%20website%20and%20I'm%20interested%20in%20tires%20or%20services." 
           target="_blank" 
           rel="noopener noreferrer" 
           class="bg-[#25D366] text-white w-14 h-14 md:w-16 md:h-16 rounded-full flex items-center justify-center shadow-2xl hover:scale-110 transition-transform duration-300 relative">
            
            <!-- WhatsApp SVG Icon -->
            <svg class="w-8 h-8 md:w-9 md:h-9" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12.031 0C5.383 0 0 5.383 0 12.031c0 2.64.674 5.21 1.956 7.481L.067 24l4.63-1.218A11.964 11.964 0 0012.031 24c6.648 0 12.031-5.383 12.031-12.031S18.679 0 12.031 0zm0 22.015c-2.228 0-4.417-.597-6.33-1.73l-.454-.27-3.44.904.918-3.35-.296-.47a10.038 10.038 0 01-1.536-5.362C.893 5.485 5.485.893 12.031.893c6.545 0 11.138 4.593 11.138 11.138 0 6.546-4.593 11.138-11.138 11.138zm6.059-8.232c-.332-.167-1.966-.97-2.27-.108-.303-.083-.541-.167-.775.167-.234.333-.873 1.082-1.07 1.303-.197.221-.393.25-.726.083-2.023-1.01-3.666-2.585-4.57-4.63-.167-.379.255-.333.916-1.64.12-.24.06-.45-.03-.618-.088-.168-.775-1.87-1.062-2.562-.28-.671-.563-.58-.775-.59-.204-.01-.44-.01-.673-.01-.234 0-.613.088-.933.435-.32.347-1.22 1.193-1.22 2.91 0 1.717 1.25 3.376 1.42 3.606.17.23 2.457 3.751 5.952 5.258 2.052.887 2.89.96 3.99.816 1.22-.16 3.237-1.32 3.687-2.597.45-1.278.45-2.373.317-2.597-.133-.224-.48-.358-.813-.525z"/>
            </svg>
            
            <!-- Pulse ring effect -->
            <span class="absolute w-full h-full bg-[#25D366] rounded-full opacity-60 animate-ping -z-10" style="animation-duration: 2s;"></span>
        </a>
    </div>

</body>`;

if (!html.includes('FLOATING WHATSAPP BUTTON')) {
    html = html.replace(/<\/body>/, whatsappWidget);
    fs.writeFileSync(file, html);
    console.log("WhatsApp button injected successfully!");
} else {
    console.log("WhatsApp button already exists.");
}
