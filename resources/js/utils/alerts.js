import { useToast } from '@/composables/useToast.js';

let cachedSwal = null;
async function getSwal() {
    if (!cachedSwal) {
        const { default: Swal } = await import('sweetalert2');
        cachedSwal = Swal;
    }
    return cachedSwal;
}

export const confirmDelete = async (title, text = '') => {
    const Swal = await getSwal();
    const result = await Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition-colors bg-red-600 text-white hover:bg-red-700 mx-2',
            cancelButton: 'inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition-colors bg-transparent text-slate-500 hover:bg-slate-100 border border-slate-200 mx-2',
        },
        buttonsStyling: false
    });
    return result.isConfirmed;
};

export const confirmAction = async (title, text = '', confirmText = 'Sí, continuar') => {
    const Swal = await getSwal();
    const result = await Swal.fire({
        title: title,
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: 'Cancelar',
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition-colors bg-brand-600 text-white hover:bg-brand-700 mx-2',
            cancelButton: 'inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition-colors bg-transparent text-slate-500 hover:bg-slate-100 border border-slate-200 mx-2',
        },
        buttonsStyling: false
    });
    return result.isConfirmed;
};

export const showSuccess = (message) => {
    useToast().success(message);
};

export const showError = (message) => {
    useToast().error(message);
};

export const showWarning = (message) => {
    useToast().warning(message);
};

export const showInfo = (message) => {
    useToast().info(message);
};
