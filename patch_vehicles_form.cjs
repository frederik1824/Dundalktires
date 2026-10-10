const fs = require('fs');
const file = 'resources/js/Pages/Vehicles/Form.vue';
let content = fs.readFileSync(file, 'utf8');

// 1. Add props and logic to script setup
content = content.replace(
    /const props = defineProps\({[\s\S]*?}\);/,
    `const props = defineProps({\n    customer: Object,\n    vehicle: Object,\n    vehicleTypes: Object,\n    catalog: { type: Array, default: () => [] },\n});`
);

content = content.replace(
    /const currentYear = new Date\(\)\.getFullYear\(\);/,
    `const currentYear = new Date().getFullYear();

import { ref, computed } from 'vue';
const searchCatalog = ref('');
const showCatalog = ref(false);

const filteredCatalog = computed(() => {
    if (!searchCatalog.value) return [];
    const q = searchCatalog.value.toLowerCase();
    return props.catalog.filter(v => 
        (v.make + ' ' + v.model + ' ' + v.year).toLowerCase().includes(q)
    ).slice(0, 5);
});

function selectFromCatalog(v) {
    form.make = v.make;
    form.model = v.model;
    form.year = v.year;
    if (v.type) form.type = v.type;
    if (v.tire_size) form.tire_size = v.tire_size;
    
    showCatalog.value = false;
    searchCatalog.value = '';
}`
);

// 2. Add the combobox to the template right above "Make / Model"
const searchHtml = `
                <!-- Buscador de catálogo -->
                <div class="mb-4 relative" v-if="catalog.length > 0">
                    <label class="label text-brand-700 flex items-center gap-1.5 mb-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Autocompletar desde el historial
                    </label>
                    <input 
                        v-model="searchCatalog" 
                        @focus="showCatalog = true" 
                        type="text" 
                        placeholder="Ej. Toyota Corolla 2018..." 
                        class="input bg-brand-50/30 border-brand-200 focus:border-brand-500 w-full focus:ring-brand-500/20"
                    />
                    
                    <div v-if="showCatalog && searchCatalog" class="absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-xl overflow-hidden">
                        <button v-if="filteredCatalog.length === 0" type="button" class="w-full text-left px-4 py-3 text-sm text-slate-500 italic cursor-default">
                            No se encontraron coincidencias en el historial.
                        </button>
                        <button 
                            v-for="(v, i) in filteredCatalog" :key="i"
                            type="button"
                            @click="selectFromCatalog(v)"
                            class="w-full text-left px-4 py-2 hover:bg-brand-50 focus:bg-brand-50 text-sm border-b border-slate-100 last:border-0 transition-colors"
                        >
                            <span class="font-bold text-slate-800">{{ v.make }} {{ v.model }}</span> 
                            <span class="text-slate-500 ml-1">({{ v.year }})</span>
                            <span v-if="v.tire_size" class="inline-block mt-1 sm:mt-0 sm:ml-2 text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-slate-100 text-slate-600">Llantas: {{ v.tire_size }}</span>
                        </button>
                    </div>
                </div>

                <!-- Fondo para cerrar el dropdown al hacer click afuera -->
                <div v-if="showCatalog" @click="showCatalog = false" class="fixed inset-0 z-10"></div>
`;

content = content.replace(
    /<!-- Make \/ Model -->/,
    searchHtml + '\n                <div class="relative z-0">\n                <!-- Make / Model -->'
);

content = content.replace(
    /<\/form>\n        <\/div>/,
    '</div>\n            </form>\n        </div>' // Close the z-0 div
);

fs.writeFileSync(file, content);
