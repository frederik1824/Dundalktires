const fs = require('fs');

const file = '/Users/usuario/Downloads/Desarrollo/Dunkdalk/public/dundalk-landing.html';
let html = fs.readFileSync(file, 'utf8');

// Extract the base64 images so we can reuse them
const heroMatch = html.match(/url\('([^']+)'\).*?min-height: 400px/);
const facadeMatch = html.match(/url\('([^']+)'\).*?border-l-8/);
const aboutMatch = html.match(/<img src="([^"]+)" alt="Dundalk Tires Exterior"/);
const logoMatch = html.match(/<img src="([^"]+)" alt="Dundalk Tires Logo"/);

const heroImg = heroMatch ? heroMatch[1] : '';
const facadeImg = facadeMatch ? facadeMatch[1] : '';
const aboutImg = aboutMatch ? aboutMatch[1] : '';
const logoImg = logoMatch ? logoMatch[1] : '';

const newHtml = `<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dundalk Used & New Tires | Premium Automotive Service</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            red: '#dc2626',
                            black: '#0a0f1c',      // Darker, more premium black
                            dark: '#111827',       // Sleek dark gray
                            gray: '#f3f4f6',       // Off-white for sections
                            white: '#ffffff',
                        }
                    },
                    fontFamily: {
                        heading: ['"Barlow Condensed"', 'sans-serif'],
                        body: ['"Inter"', 'sans-serif'],
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: 0, transform: 'translateY(30px)' },
                            '100%': { opacity: 1, transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Barlow Condensed', sans-serif; text-transform: uppercase; letter-spacing: 0.02em; }
        
        .reveal { opacity: 0; transform: translateY(30px); transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
        .reveal.active { opacity: 1; transform: translateY(0); }
        
        /* Smooth, non-generic hover effects */
        .service-row { transition: all 0.4s ease; border-bottom: 1px solid #374151; }
        .service-row:hover { background-color: #111827; padding-left: 1rem; border-color: #dc2626; }
        .service-row:hover h3 { color: #dc2626; }
        
        .hero-split { display: flex; flex-direction: column; min-height: 100vh; }
        @media (min-width: 1024px) {
            .hero-split { flex-direction: row; }
            .hero-left { width: 55%; }
            .hero-right { width: 45%; }
        }
        
        .glass-nav { background: rgba(10, 15, 28, 0.95); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-brand-black text-brand-white">

    <!-- Premium Navigation -->
    <nav class="glass-nav fixed w-full top-0 z-50 border-b border-white/10 transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            <a href="#" class="flex items-center gap-3 group">
                <img src="${logoImg}" alt="Dundalk Tires Logo" class="h-10 w-auto object-contain bg-white p-1 rounded-sm opacity-90 group-hover:opacity-100 transition-opacity">
            </a>
            <div class="hidden md:flex gap-10 font-heading text-lg font-medium tracking-widest text-gray-400">
                <a href="#services" class="hover:text-white transition-colors">EXPERTISE</a>
                <a href="#about" class="hover:text-white transition-colors">OUR SHOP</a>
                <a href="#contact" class="hover:text-white transition-colors">CONNECT</a>
            </div>
            <a href="tel:6677273115" class="hidden md:inline-flex items-center gap-2 text-sm font-semibold tracking-wider border border-white/20 px-6 py-2.5 rounded-full hover:bg-white hover:text-brand-black transition-all">
                <span class="w-2 h-2 rounded-full bg-brand-red animate-pulse"></span>
                667.727.3115
            </a>
        </div>
    </nav>

    <!-- Elevated Hero Section -->
    <header class="w-full hero-split relative pt-20 lg:pt-0">
        <!-- Text & primary imagery -->
        <div class="hero-left relative flex flex-col justify-center px-6 md:px-16 lg:px-24 py-20 lg:py-0 bg-cover bg-center z-10" style="background-image: url('${heroImg}');">
            <div class="absolute inset-0 bg-brand-black/85 backdrop-blur-sm lg:backdrop-blur-none lg:bg-gradient-to-r lg:from-brand-black/95 lg:to-brand-black/60"></div>
            
            <div class="relative z-10 max-w-xl animate-fade-in-up">
                <div class="flex items-center gap-4 mb-6">
                    <div class="h-[1px] w-12 bg-brand-red"></div>
                    <span class="text-brand-red font-heading tracking-widest font-semibold">AUTOMOTIVE INFRASTRUCTURE</span>
                </div>
                <h1 class="text-5xl md:text-7xl lg:text-[5.5rem] font-bold text-white mb-6 leading-[0.95]">
                    KEEPING YOUR <br>BUSINESS <span class="text-transparent bg-clip-text bg-gradient-to-r from-white to-gray-500">ROLLING.</span>
                </h1>
                <p class="text-lg md:text-xl text-gray-400 font-body font-light mb-10 leading-relaxed max-w-lg">
                    We deliver precise, high-performance tire solutions and vehicle support. Zero compromises on safety, absolute dedication to quality.
                </p>
                <div class="flex flex-wrap items-center gap-6">
                    <a href="#services" class="bg-brand-red text-white px-8 py-4 font-heading text-xl font-semibold tracking-wider hover:bg-red-700 transition-colors">
                        EXPLORE SERVICES
                    </a>
                    <a href="tel:6677273115" class="text-white font-heading text-xl font-medium tracking-widest hover:text-brand-red transition-colors flex items-center gap-2">
                        REQUEST QUOTE 
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Architectural / Secondary imagery -->
        <div class="hero-right hidden lg:block relative bg-cover bg-center grayscale-[20%]" style="background-image: url('${facadeImg}');">
            <div class="absolute inset-0 bg-brand-red/10 mix-blend-multiply"></div>
            <!-- Decorative accent -->
            <div class="absolute bottom-0 left-0 w-full h-32 bg-gradient-to-t from-brand-black to-transparent"></div>
            <div class="absolute top-1/2 -left-[1px] w-[2px] h-32 bg-brand-red -translate-y-1/2 z-20"></div>
        </div>
    </header>

    <!-- Services Section (Corporate & Clean) -->
    <section id="services" class="py-32 bg-brand-black relative">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
                
                <div class="lg:col-span-4 reveal">
                    <h2 class="text-sm text-brand-red font-heading tracking-widest font-semibold mb-4">OUR EXPERTISE</h2>
                    <h3 class="text-4xl md:text-5xl font-bold mb-6">ENGINEERED<br>FOR THE ROAD.</h3>
                    <p class="text-gray-400 font-light leading-relaxed mb-8">
                        We don't just change tires; we provide comprehensive wheel infrastructure and support to ensure optimal vehicle performance.
                    </p>
                </div>
                
                <div class="lg:col-span-8 reveal">
                    <!-- Custom Service List instead of generic cards -->
                    <div class="flex flex-col border-t border-gray-800">
                        <div class="service-row py-8 flex flex-col md:flex-row md:items-start gap-4 md:gap-12 cursor-pointer group">
                            <span class="font-mono text-gray-600 text-sm md:w-12 pt-1">01</span>
                            <div>
                                <h3 class="text-3xl font-bold mb-2 text-white transition-colors">Tire Plug & Patch</h3>
                                <p class="text-gray-400 font-light">Industry-standard puncture repair to restore structural integrity.</p>
                            </div>
                        </div>
                        
                        <div class="service-row py-8 flex flex-col md:flex-row md:items-start gap-4 md:gap-12 cursor-pointer group">
                            <span class="font-mono text-gray-600 text-sm md:w-12 pt-1">02</span>
                            <div>
                                <h3 class="text-3xl font-bold mb-2 text-white transition-colors">Mounting & Rotation</h3>
                                <p class="text-gray-400 font-light">Strategic lifecycle management to maximize tire longevity.</p>
                            </div>
                        </div>

                        <div class="service-row py-8 flex flex-col md:flex-row md:items-start gap-4 md:gap-12 cursor-pointer group">
                            <span class="font-mono text-gray-600 text-sm md:w-12 pt-1">03</span>
                            <div>
                                <h3 class="text-3xl font-bold mb-2 text-white transition-colors">Precision Balancing</h3>
                                <p class="text-gray-400 font-light">Computerized calibration eliminating vibration for high-speed stability.</p>
                            </div>
                        </div>

                        <div class="service-row py-8 flex flex-col md:flex-row md:items-start gap-4 md:gap-12 cursor-pointer group">
                            <span class="font-mono text-brand-red text-sm md:w-12 pt-1">04</span>
                            <div>
                                <h3 class="text-3xl font-bold mb-2 text-white transition-colors">New & Certified Used Tires</h3>
                                <p class="text-gray-400 font-light">Premium inventory sourcing for fleets and individual high-demand clients.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </section>

    <!-- About / Infrastructure Section -->
    <section id="about" class="py-32 bg-brand-dark">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-center">
                <div class="relative reveal">
                    <!-- Clean, borderless image presentation -->
                    <div class="aspect-[4/5] overflow-hidden relative">
                        <img src="${aboutImg}" alt="Dundalk Tires Infrastructure" class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all duration-700">
                        <div class="absolute inset-0 bg-brand-black/20"></div>
                    </div>
                    <!-- Stats overlay -->
                    <div class="absolute -bottom-8 -right-8 bg-brand-red p-8 hidden md:block">
                        <div class="text-6xl font-bold text-white mb-1">15+</div>
                        <div class="text-white/80 font-heading tracking-widest text-sm">YEARS COMBINED EXP.</div>
                    </div>
                </div>
                
                <div class="reveal">
                    <h2 class="text-sm text-brand-red font-heading tracking-widest font-semibold mb-4">THE FACILITY</h2>
                    <h3 class="text-4xl md:text-5xl font-bold mb-8 text-white leading-tight">ROBUST INFRASTRUCTURE. <br>LOCAL COMMITMENT.</h3>
                    
                    <div class="space-y-6 text-gray-400 font-light text-lg">
                        <p>
                            Dundalk Used & New Tires operates as a high-efficiency service center. We eliminate the friction of traditional repair shops by focusing on speed, transparency, and technical precision.
                        </p>
                        <p>
                            Our facility is equipped to handle everything from personal sedans to heavy-duty commercial demands. We don't upsell; we diagnose, implement, and deploy solutions that keep you moving.
                        </p>
                    </div>
                    
                    <div class="mt-12 pt-12 border-t border-gray-800 grid grid-cols-2 gap-8">
                        <div>
                            <div class="text-3xl font-bold text-white mb-2">100%</div>
                            <div class="text-sm text-gray-500 font-heading tracking-widest">DIAGNOSTIC TRANSPARENCY</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-white mb-2">24/7</div>
                            <div class="text-sm text-gray-500 font-heading tracking-widest">DEDICATION TO SAFETY</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer / Minimalist Contact -->
    <footer id="contact" class="bg-brand-black pt-32 pb-12 border-t border-white/5 relative overflow-hidden">
        <!-- Abstract tech decorative element -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-brand-red/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
        
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-12 lg:gap-8 mb-24">
                
                <div class="md:col-span-5">
                    <h2 class="text-5xl md:text-6xl font-bold text-white mb-8">LET'S START <br>A PROJECT.</h2>
                    <a href="mailto:info@dundalktires.com" class="text-2xl font-light text-gray-400 hover:text-brand-red transition-colors block mb-2">info@dundalktires.com</a>
                    <a href="tel:6677273115" class="text-2xl font-light text-gray-400 hover:text-brand-red transition-colors block">667-727-3115</a>
                </div>
                
                <div class="md:col-span-3 md:col-start-7">
                    <h4 class="text-white font-heading tracking-widest mb-6 text-sm">HEADQUARTERS</h4>
                    <address class="not-italic text-gray-500 font-light leading-loose">
                        7888 Dundalk Ave<br>
                        Dundalk, MD 21222<br>
                        United States
                    </address>
                </div>
                
                <div class="md:col-span-3">
                    <h4 class="text-white font-heading tracking-widest mb-6 text-sm">NAVIGATION</h4>
                    <ul class="space-y-3 text-gray-500 font-light">
                        <li><a href="#services" class="hover:text-white transition-colors">Expertise</a></li>
                        <li><a href="#about" class="hover:text-white transition-colors">Facility</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="flex flex-col md:flex-row justify-between items-center pt-8 border-t border-white/10 text-gray-600 text-sm font-light">
                <img src="${logoImg}" alt="Logo" class="h-8 w-auto opacity-50 mb-4 md:mb-0 grayscale">
                <p>&copy; 2026 Dundalk Tires LLC. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Interactive Reveal Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const reveals = document.querySelectorAll('.reveal');
            
            const revealOnScroll = () => {
                const windowHeight = window.innerHeight;
                const elementVisible = 100;
                
                reveals.forEach((reveal) => {
                    const elementTop = reveal.getBoundingClientRect().top;
                    if (elementTop < windowHeight - elementVisible) {
                        reveal.classList.add('active');
                    }
                });
            };
            
            window.addEventListener('scroll', revealOnScroll);
            revealOnScroll(); // Trigger once on load
            
            // Subtle navbar background effect on scroll
            const navbar = document.getElementById('navbar');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    navbar.style.borderBottomColor = 'rgba(255,255,255,0.1)';
                    navbar.style.background = 'rgba(10, 15, 28, 0.98)';
                } else {
                    navbar.style.borderBottomColor = 'transparent';
                    navbar.style.background = 'transparent';
                }
            });
        });
    </script>
</body>
</html>`;

fs.writeFileSync(file, newHtml);
