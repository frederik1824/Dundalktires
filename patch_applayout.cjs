const fs = require('fs');
let file = 'resources/js/Layouts/AppLayout.vue';
let content = fs.readFileSync(file, 'utf8');

// 1. Remove the inline NavIcon component block completely
content = content.replace(/<script>\n\/\/ Inline icon component\nconst NavIcon[\s\S]*?<\/script>\n/, '');

// 2. Add padding to main to account for bottom nav on mobile
content = content.replace(/<main class="flex-1 overflow-y-auto p-4 md:p-6">/, '<main class="flex-1 overflow-y-auto p-4 pb-20 md:p-6 md:pb-6">');

// 3. Add Bottom Navigation before </template>
let bottomNav = `
        <!-- Bottom Navigation Bar (Mobile Only) -->
        <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-border z-40 flex justify-around items-center h-16 select-none shadow-[0_-4px_10px_rgba(0,0,0,0.02)] pb-[env(safe-area-inset-bottom)]">
            <Link href="/dashboard" class="flex flex-col items-center justify-center w-full h-full text-slate-400 active:scale-95 transition-transform" :class="{'text-brand-600 font-semibold': isActive('/dashboard')}">
                <Icon name="grid" class="w-6 h-6 mb-1" />
                <span class="text-[10px]">Inicio</span>
            </Link>
            <Link href="/pos" class="flex flex-col items-center justify-center w-full h-full text-slate-400 active:scale-95 transition-transform" :class="{'text-brand-600 font-semibold': isActive('/pos')}">
                <Icon name="revenue" class="w-6 h-6 mb-1" />
                <span class="text-[10px]">POS</span>
            </Link>
            <Link href="/work-orders" class="flex flex-col items-center justify-center w-full h-full text-slate-400 active:scale-95 transition-transform" :class="{'text-brand-600 font-semibold': isActive('/work-orders')}">
                <Icon name="clipboard" class="w-6 h-6 mb-1" />
                <span class="text-[10px]">Órdenes</span>
            </Link>
            <Link href="/customers" class="flex flex-col items-center justify-center w-full h-full text-slate-400 active:scale-95 transition-transform" :class="{'text-brand-600 font-semibold': isActive('/customers')}">
                <Icon name="users" class="w-6 h-6 mb-1" />
                <span class="text-[10px]">Clientes</span>
            </Link>
            <button @click="sidebarOpen = true" class="flex flex-col items-center justify-center w-full h-full text-slate-400 active:scale-95 transition-transform">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <span class="text-[10px]">Menú</span>
            </button>
        </nav>
    </div>
    <FlashMessage />
</template>
`;
content = content.replace(/<\/div>\n\s*<FlashMessage \/>\n<\/template>/, bottomNav);

fs.writeFileSync(file, content);
