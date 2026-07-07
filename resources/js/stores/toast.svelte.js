export const toasts = $state([]);

export function addToast({ title = '', message = '', type = 'info', duration = 5000 }) {
    const id = Date.now() + Math.random().toString(36).slice(2, 9);
    toasts.push({ id, title, message, type });
    setTimeout(() => removeToast(id), duration);
}

export function removeToast(id) {
    const index = toasts.findIndex((toast) => toast.id === id);
    if (index !== -1) toasts.splice(index, 1);
}
