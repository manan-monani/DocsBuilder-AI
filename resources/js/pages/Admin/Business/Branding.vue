<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
import { update } from '@/actions/App/Http/Controllers/Admin/BusinessSettingController';
import { Palette, Save, Camera, Info } from 'lucide-vue-next';

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({}),
    },
    default_colors: {
        type: Object,
        default: () => ({
            primary: '#179753ff',
            primary_light: '#ecfdf5',
            primary_dark_text: '#6ee7b7',
            secondary: '#64748b',
            admin: {
                sidebar_rail_bg: '#064e3b',
                sidebar_rail_bg_dark: '#022c22',
                sidebar_icon_color: '#ffffffff',
                sidebar_icon_color_dark: '#ffffffff',
            }
        }),
    },
});

const form = useForm({
    business_name: props.settings.business_name || 'Nexus Global Corp',


    logo_url: props.settings.logo_url || 'https://api.dicebear.com/7.x/initials/svg?seed=Nexus&backgroundColor=0284c7',
    cover_url: props.settings.cover_url || 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&q=80&w=2000',

    primary: props.settings.primary || props.default_colors.primary,
    primary_light: props.settings.primary_light || props.default_colors.primary_light,
    primary_dark_text: props.settings.primary_dark_text || props.default_colors.primary_dark_text,

    secondary: props.settings.secondary || props.default_colors.secondary,

    sidebar_rail_bg: props.settings.sidebar_rail_bg || props.default_colors.admin.sidebar_rail_bg,
    sidebar_rail_bg_dark: props.settings.sidebar_rail_bg_dark || props.default_colors.admin.sidebar_rail_bg_dark,
    sidebar_icon_color: props.settings.sidebar_icon_color || props.default_colors.admin.sidebar_icon_color,
    sidebar_icon_color_dark: props.settings.sidebar_icon_color_dark || props.default_colors.admin.sidebar_icon_color_dark,
});

// File inputs handling
const logoInput = ref<HTMLInputElement | null>(null);
const coverInput = ref<HTMLInputElement | null>(null);

const handleFileChange = (event: Event, key: keyof typeof form) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        const url = URL.createObjectURL(file);

        form[key] = file as any;
        previews.value[key] = url;
    }
};

const previews = ref<Record<string, string>>({
    logo_url: props.settings.logo_url || form.logo_url,
    cover_url: props.settings.cover_url || form.cover_url,
});

    watch(() => props.settings, (newSettings) => {
    form.business_name = newSettings.business_name || 'Nexus Global Corp';

    form.logo_url = newSettings.logo_url || 'https://api.dicebear.com/7.x/initials/svg?seed=Nexus&backgroundColor=0284c7';
    form.cover_url = newSettings.cover_url || 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&q=80&w=2000';

    form.primary = newSettings.primary || props.default_colors.primary;
    form.primary_light = newSettings.primary_light || props.default_colors.primary_light;
    form.primary_dark_text = newSettings.primary_dark_text || props.default_colors.primary_dark_text;

    form.secondary = newSettings.secondary || props.default_colors.secondary;

    form.sidebar_rail_bg = newSettings.sidebar_rail_bg || props.default_colors.admin.sidebar_rail_bg;
    form.sidebar_rail_bg_dark = newSettings.sidebar_rail_bg_dark || props.default_colors.admin.sidebar_rail_bg_dark;
    form.sidebar_icon_color = newSettings.sidebar_icon_color || props.default_colors.admin.sidebar_icon_color;
    form.sidebar_icon_color_dark = newSettings.sidebar_icon_color_dark || props.default_colors.admin.sidebar_icon_color_dark;

    previews.value = {
        logo_url: newSettings.logo_url || form.logo_url,
        cover_url: newSettings.cover_url || form.cover_url,
    };
}, { deep: true });

const submit = () => {
    form.post(update.url(), {
        preserveScroll: true,
        onSuccess: () => {
            // Toast handled by backend flash usually, but we can do local success too
        },
    });
};

// UI Helpers
const triggerInput = (id: string) => {
    document.getElementById(id)?.click();
};

const triggerFileInput = (refName: 'logoInput' | 'coverInput') => {
    if (refName === 'logoInput') logoInput.value?.click();
    if (refName === 'coverInput') coverInput.value?.click();
};

</script>

<template>
    <Head :title="__('Business Branding')" />

    <AdminLayout>
        <div class="h-full">

            <!-- BUSINESS PANE -->
            <div class="space-y-6 max-w-6xl mx-auto animate-fade-in">

                <!-- Header -->
                 <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div>
                        <h1 class="text-xl font-extrabold text-slate-900 dark:text-white">{{ __('business_branding') }}</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('Manage your organization\'s visual identity and public profile.') }}</p>
                    </div>
                    <div class="flex gap-3">
                         <button @click="submit" :disabled="form.processing" class="text-white px-4 py-2 rounded-xl font-bold text-sm shadow-lg transition-all flex items-center gap-2 hover:opacity-90 disabled:opacity-70" :style="{ backgroundColor: 'var(--brand-primary)', boxShadow: '0 10px 15px -3px var(--brand-primary-transparent, rgba(0,0,0,0.1))' }">
                            <Save v-if="!form.processing" :size="18" />
                            <span v-if="form.processing">{{ __('Saving...') }}</span>
                            <span v-else>{{ __('Save Changes') }}</span>
                        </button>
                    </div>
                </div>

                <!-- 1. HEADER & COVER SECTION -->
                <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden transition-all duration-300">
                    <div class="relative h-64 bg-slate-200 dark:bg-slate-800 group">
                        <img :src="previews.cover_url" class="w-full h-full object-cover transition-opacity duration-500">
                        <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <button @click="triggerFileInput('coverInput')" class="bg-white/20 backdrop-blur-md text-white px-6 py-2 rounded-full font-bold border border-white/30 hover:bg-white/40 transition-all flex items-center gap-2">
                                <Camera :size="18" /> {{ __('Change Cover') }}
                            </button>
                            <input type="file" ref="coverInput" class="hidden" accept="image/*" @change="(e) => handleFileChange(e, 'cover_url')">
                        </div>
                        <div class="absolute -bottom-12 inset-x-10 flex items-end space-x-6 rtl:space-x-reverse">
                            <div class="relative group/logo">
                                <img :src="previews.logo_url" class="w-32 h-32 rounded-[2rem] border-4 border-white dark:border-slate-900 shadow-2xl bg-white dark:bg-slate-800 object-contain p-2">
                                <button @click="triggerFileInput('logoInput')" class="absolute inset-0 flex items-center justify-center bg-black/40 rounded-[2rem] opacity-0 group-hover/logo:opacity-100 transition-all text-white text-xs font-bold cursor-pointer">
                                    {{ __('Edit') }}
                                </button>
                                <input type="file" ref="logoInput" class="hidden" accept="image/*" @change="(e) => handleFileChange(e, 'logo_url')">
                            </div>
                            <div class="mb-14 text-start">
                                <h3 class="text-2xl font-black text-white drop-shadow-xl">{{ form.business_name }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="pt-20 pb-5 px-5">
                        <div class="grid grid-cols-1 gap-8">
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ps-1 mb-2 block text-start">{{ __('Business Name') }}</label>
                                <input v-model="form.business_name" type="text" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl font-medium outline-none text-slate-900 dark:text-white focus:ring-2 focus:opacity-100 transition-all" :style="{ '--tw-ring-color': 'var(--brand-primary)' }">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-8">

                    <!-- Colors -->
                    <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm transition-all duration-300 p-5 space-y-5">
                        <h4 class="text-sm font-black uppercase tracking-widest text-slate-400 mb-6 flex items-center">
                            <Palette class="me-3" :size="16" :style="{ color: 'var(--brand-primary)' }" /> {{ __('Brand Colors') }}
                        </h4>
                        <div class="space-y-6">
                            <!-- Helper Text -->
                            <div class="p-3 bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-900/30 rounded-xl">
                                <p class="text-[11px] text-blue-600 dark:text-blue-400 flex items-start gap-2">
                                    <Info :size="14" class="mt-0.5 shrink-0" />
                                    <span>{{ __('Customize your admin panel theme. These colors affect navigation, buttons, and text contrast.') }}</span>
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Primary Palette -->
                                <div class="space-y-4">
                                    <h5 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-800 pb-2 text-start">{{ __('Primary Palette') }}</h5>

                                    <div class="space-y-3">
                                        <!-- Primary -->
                                        <div>
                                            <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 block mb-1 text-start">{{ __('Primary Color') }}</label>
                                            <div class="flex items-center gap-3">
                                                <input v-model="form.primary" type="color" class="h-8 w-14 rounded cursor-pointer border-0 bg-transparent p-0">
                                                <input v-model="form.primary" type="text" class="w-full text-xs font-mono bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded px-2 py-1.5 outline-none focus:ring-1 focus:ring-primary">
                                            </div>
                                        </div>
                                        <!-- Primary Light -->
                                        <div>
                                            <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 block mb-1 text-start">{{ __('Primary Light (soft bg)') }}</label>
                                            <div class="flex items-center gap-3">
                                                <input v-model="form.primary_light" type="color" class="h-8 w-14 rounded cursor-pointer border-0 bg-transparent p-0">
                                                <input v-model="form.primary_light" type="text" class="w-full text-xs font-mono bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded px-2 py-1.5 outline-none focus:ring-1 focus:ring-primary">
                                            </div>
                                        </div>
                                        <!-- Primary Dark Text -->
                                        <div>
                                            <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 block mb-1 text-start">{{ __('Primary Dark Text (for dark mode)') }}</label>
                                            <div class="flex items-center gap-3">
                                                <input v-model="form.primary_dark_text" type="color" class="h-8 w-14 rounded cursor-pointer border-0 bg-transparent p-0">
                                                <input v-model="form.primary_dark_text" type="text" class="w-full text-xs font-mono bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded px-2 py-1.5 outline-none focus:ring-1 focus:ring-primary">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Secondary & Sidebar -->
                                <div class="space-y-4">
                                     <h5 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-800 pb-2 text-start">{{ __('Secondary & Sidebar') }}</h5>

                                     <div class="space-y-3">
                                        <!-- Secondary -->
                                        <div>
                                            <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 block mb-1 text-start">{{ __('Secondary Color') }}</label>
                                            <div class="flex items-center gap-3">
                                                <input v-model="form.secondary" type="color" class="h-8 w-14 rounded cursor-pointer border-0 bg-transparent p-0">
                                                <input v-model="form.secondary" type="text" class="w-full text-xs font-mono bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded px-2 py-1.5 outline-none focus:ring-1 focus:ring-primary">
                                            </div>
                                        </div>

                                        <!-- Sidebar Rail -->
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="text-[9px] font-bold text-slate-500 dark:text-slate-400 block mb-1 text-start">{{ __('Sidebar Rail BG') }}</label>
                                                <div class="flex items-center gap-2">
                                                    <input v-model="form.sidebar_rail_bg" type="color" class="h-6 w-8 rounded cursor-pointer border-0 bg-transparent p-0">
                                                    <input v-model="form.sidebar_rail_bg" type="text" class="w-full text-[10px] font-mono bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded px-2 py-1 outline-none focus:ring-1 focus:ring-primary">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-bold text-slate-500 dark:text-slate-400 block mb-1 text-start">{{ __('Rail BG (Dark)') }}</label>
                                                <div class="flex items-center gap-2">
                                                    <input v-model="form.sidebar_rail_bg_dark" type="color" class="h-6 w-8 rounded cursor-pointer border-0 bg-transparent p-0">
                                                    <input v-model="form.sidebar_rail_bg_dark" type="text" class="w-full text-[10px] font-mono bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded px-2 py-1 outline-none focus:ring-1 focus:ring-primary">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Sidebar Icons -->
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="text-[9px] font-bold text-slate-500 dark:text-slate-400 block mb-1 text-start">{{ __('Sidebar Icon') }}</label>
                                                <div class="flex items-center gap-2">
                                                    <input v-model="form.sidebar_icon_color" type="color" class="h-6 w-8 rounded cursor-pointer border-0 bg-transparent p-0">
                                                    <input v-model="form.sidebar_icon_color" type="text" class="w-full text-[10px] font-mono bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded px-2 py-1 outline-none focus:ring-1 focus:ring-primary">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-bold text-slate-500 dark:text-slate-400 block mb-1 text-start">{{ __('Icon (Dark)') }}</label>
                                                <div class="flex items-center gap-2">
                                                    <input v-model="form.sidebar_icon_color_dark" type="color" class="h-6 w-8 rounded cursor-pointer border-0 bg-transparent p-0">
                                                    <input v-model="form.sidebar_icon_color_dark" type="text" class="w-full text-[10px] font-mono bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded px-2 py-1 outline-none focus:ring-1 focus:ring-primary">
                                                </div>
                                            </div>
                                        </div>
                                     </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- End Colors -->

            </div>
        </div>
    </AdminLayout>
</template>
