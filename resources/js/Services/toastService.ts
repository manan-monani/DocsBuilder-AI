import { ref } from 'vue';

export interface Toast {
    id: string;
    message: string;
    type: 'success' | 'error' | 'warning';
}

const toasts = ref<Toast[]>([]);

export const useToast = () => {
    const add = (message: string, type: 'success' | 'error' | 'warning' = 'success') => {
        const id = Date.now().toString() + Math.random();
        toasts.value.push({ id, message, type });

        setTimeout(() => remove(id), 5000);
    };

    const remove = (id: string) => {
        const index = toasts.value.findIndex(t => t.id === id);
        if (index > -1) {
            toasts.value.splice(index, 1);
        }
    };

    const success = (message: string) => add(message, 'success');
    const error = (message: string) => add(message, 'error');
    const warning = (message: string) => add(message, 'warning');

    return {
        toasts,
        add,
        remove,
        success,
        error,
        warning
    };
};
