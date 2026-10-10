import { reactive } from 'vue';

// Module-level singleton so all callers share the same queue
const toasts = reactive([]);
let nextId = 1;

function add(type, message, duration) {
    const id = nextId++;
    const d = duration ?? (type === 'error' ? 5000 : 3500);
    toasts.push({ id, type, message });
    setTimeout(() => remove(id), d);
    return id;
}

function remove(id) {
    const i = toasts.findIndex(t => t.id === id);
    if (i !== -1) toasts.splice(i, 1);
}

export function useToast() {
    return {
        toasts,
        success: (msg, dur) => add('success', msg, dur),
        error:   (msg, dur) => add('error',   msg, dur),
        warning: (msg, dur) => add('warning',  msg, dur),
        info:    (msg, dur) => add('info',     msg, dur),
        remove,
    };
}
