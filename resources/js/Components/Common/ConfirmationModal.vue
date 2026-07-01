<script setup lang="ts">
import { PropType } from 'vue';
import { AlertTriangle, AlertCircle, Info, Loader2 } from 'lucide-vue-next';

const props = defineProps({
    show: Boolean,
    title: {
        type: String,
        default: 'Are you sure?',
    },
    message: {
        type: String,
        default: 'This action cannot be undone.',
    },
    confirmText: {
        type: String,
        default: 'Confirm',
    },
    cancelText: {
        type: String,
        default: 'Cancel',
    },
    type: {
        type: String as PropType<'danger' | 'warning' | 'info'>,
        default: 'danger',
    },
    processing: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close', 'confirm']);

const colors = {
    danger: {
        iconBg: 'bg-red-100 dark:bg-red-900/20',
        iconText: 'text-red-600 dark:text-red-400',
        button: 'bg-red-600 hover:bg-red-700 text-white shadow-red-600/20',
        icon: AlertTriangle,
    },
    warning: {
        iconBg: 'bg-amber-100 dark:bg-amber-900/20',
        iconText: 'text-amber-600 dark:text-amber-400',
        button: 'bg-amber-600 hover:bg-amber-700 text-white shadow-amber-600/20',
        icon: AlertCircle,
    },
    info: {
        iconBg: 'bg-blue-100 dark:bg-blue-900/20',
        iconText: 'text-blue-600 dark:text-blue-400',
        button: 'bg-blue-600 hover:bg-blue-700 text-white shadow-blue-600/20',
        icon: Info,
    },
};
</script>

<template>
    <div v-show="show" class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <Transition
            enter-active-class="ease-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-show="show" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="$emit('close')"></div>
        </Transition>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <!-- Modal Panel -->
                <Transition
                    enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                >
                    <div v-show="show" class="relative transform overflow-hidden rounded-[2rem] bg-white dark:bg-slate-900 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg ring-1 ring-slate-900/5 dark:ring-white/10">
                        <div class="p-8 text-center sm:p-10">
                            <!-- Icon -->
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full mb-6 animate-bounce-short" :class="colors[type].iconBg">
                                <component :is="colors[type].icon" class="h-8 w-8" :class="colors[type].iconText" aria-hidden="true" />
                            </div>

                            <!-- Text -->
                            <div class="space-y-2 mb-8">
                                <h3 class="text-xl font-bold leading-6 text-slate-900 dark:text-white" id="modal-title">{{ title }}</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed max-w-xs mx-auto">{{ message }}</p>
                            </div>

                            <!-- Actions -->
                            <div class="grid grid-cols-2 gap-4">
                                <button
                                    type="button"
                                    class="inline-flex w-full justify-center rounded-xl bg-white dark:bg-slate-800 px-3 py-3 text-sm font-bold text-slate-900 dark:text-slate-200 shadow-sm ring-1 ring-inset ring-slate-200 dark:ring-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all"
                                    @click="$emit('close')"
                                    ref="cancelButtonRef"
                                >
                                    {{ cancelText }}
                                </button>
                                <button
                                    type="button"
                                    class="inline-flex w-full justify-center rounded-xl px-3 py-3 text-sm font-bold text-white shadow-xl shadow-indigo-500/20 transition-all disabled:opacity-70 disabled:cursor-not-allowed items-center"
                                    :class="colors[type].button"
                                    :disabled="processing"
                                    @click="$emit('confirm')"
                                >
                                    <Loader2 v-if="processing" class="animate-spin -ml-1 mr-2 h-4 w-4" />
                                    {{ confirmText }}
                                </button>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </div>
    </div>
</template>

<style scoped>
.animate-bounce-short {
    animation: bounce 0.5s infinite alternate;
}
@keyframes bounce {
    from { transform: translateY(0); }
    to { transform: translateY(-5px); }
}
</style>
