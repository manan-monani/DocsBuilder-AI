<script setup lang="ts">
import { Link, usePage, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import { logout } from '@/routes';
import admin from '@/routes/admin';
import ThemeToggle from '@/Components/ThemeToggle.vue';


const emit = defineEmits(['toggleSidebar', 'toggleSidebarSize']);
const page = usePage();

const isUserDropdownOpen = ref(false);
const dropdownRef = ref<HTMLElement | null>(null);

const toggleUserDropdown = () => {
    isUserDropdownOpen.value = !isUserDropdownOpen.value;
};

const closeDropdown = (e: MouseEvent) => {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target as Node)) {
        isUserDropdownOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', closeDropdown);
    // Theme Init
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDark.value = true;
    }
});

onUnmounted(() => {
    document.removeEventListener('click', closeDropdown);
});

// Branding helper
const t = (key: string) => {
    return page.props.branding?.admin?.header?.[key] || key;
}

// Theme Logic
const isDark = ref(false);
const toggleTheme = () => {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
};



</script>

<template>
    <header class="h-16 flex items-center justify-between px-4 lg:px-6 bg-admin-header dark:bg-admin-header-dark backdrop-blur-md border-b border-admin-sidebar-border dark:border-admin-sidebar-border-dark sticky top-0 z-30 transition-colors duration-300">
        <div class="flex items-center gap-4">
            <!-- Mobile Sidebar Toggle -->
            <button @click="emit('toggleSidebar')" class="lg:hidden w-9 h-9 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                <svg class="w-5 h-5 rtl:scale-x-[-1]" fill="currentColor" viewBox="0 0 448 512"><path d="M0 96C0 78.3 14.3 64 32 64H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32C14.3 128 0 113.7 0 96zM0 256c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM448 416c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32z"/></svg>
            </button>

            <!-- Desktop Collapse Toggle -->
            <button @click="emit('toggleSidebarSize')" class="hidden lg:flex w-9 h-9 items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-sky-600 transition-all">
                <svg class="w-5 h-5 rtl:scale-x-[-1]" fill="currentColor" viewBox="0 0 448 512"><path d="M0 96C0 78.3 14.3 64 32 64H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32C14.3 128 0 113.7 0 96zM0 256c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM448 416c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32z"/></svg>
            </button>

            <div class="text-start">
                <h1 class="text-sm md:text-base font-extrabold text-slate-900 dark:text-white text-nowrap">{{ __(t('title')) }}</h1>
                <p class="text-[10px] text-slate-400 font-medium text-nowrap">{{ __(t('subtitle')) }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2 md:gap-4 relative">


            <div class="hidden md:block">
                <ThemeToggle />
            </div>

            <div class="relative inline-block text-start ms-2" ref="dropdownRef">
                <button @click.stop="toggleUserDropdown" class="w-9 h-9 rounded-full bg-primary-50 dark:bg-primary-900/50 flex items-center justify-center text-primary-600 dark:text-primary-400 border border-primary-200 dark:border-primary-800 shadow-sm hover:bg-primary-100 dark:hover:bg-primary-900 transition-all overflow-hidden">
                    <img
                        v-if="$page.props.auth.user.profile_image"
                        :src="'/storage/' + $page.props.auth.user.profile_image"
                        alt="User"
                        class="w-full h-full object-cover"
                    >
                    <img
                        v-else
                        :src="`https://ui-avatars.com/api/?name=${encodeURIComponent($page.props.auth.user.name)}&background=random&color=fff`"
                        alt="User"
                        class="w-full h-full object-cover"
                    >
                </button>

                <!-- Dropdown -->
                <div v-show="isUserDropdownOpen" class="absolute end-0 mt-3 w-56 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-800 py-2 z-50 dropdown-animate origin-top-right">
                    <div class="px-4 py-3 border-b border-slate-50 dark:border-slate-800 text-start">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ __(t('signed_in_as')) }}</p>
                        <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $page.props.auth.user.name }}</p>
                    </div>

                    <!-- Mobile Actions (Visible inside dropdown on small screens) -->
                    <div class="p-1 border-b border-slate-50 dark:border-slate-800 md:hidden">

                        <button @click="toggleTheme" class="w-full flex items-center space-x-3 rtl:space-x-reverse px-3 py-2 text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-primary-600 dark:hover:text-primary-400 rounded-xl transition-all font-medium text-start">
                             <svg v-if="isDark" class="w-4 h-4 text-center" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                             <svg v-else class="w-4 h-4 text-center" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                             <span>{{ isDark ? __('Light Mode') : __('Dark Mode') }}</span>
                        </button>
                    </div>

                    <div class="p-1">
                        <Link :href="admin.profile.edit.url()" class="w-full flex items-center space-x-3 rtl:space-x-reverse px-3 py-2 text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-primary-600 dark:hover:text-primary-400 rounded-xl transition-all font-medium text-start">
                             <svg class="w-4 h-4 text-center" fill="currentColor" viewBox="0 0 512 512"><path d="M495.9 166.6c3.2 8.7 .5 18.4-6.4 24.6l-43.3 39.4c1.1 8.3 1.7 16.8 1.7 25.4s-.6 17.1-1.7 25.4l43.3 39.4c6.9 6.2 9.6 15.9 6.4 24.6c-4.4 11.9-9.7 23.3-15.8 34.3l-4.7 8.1c-6.6 11-14 21.4-22.1 31.2c-5.9 7.2-15.7 9.6-24.5 6.8l-55.7-17.7c-13.4 10.3-28.2 18.9-44 25.4l-12.5 57.1c-2 9.1-9 16.3-18.2 17.8c-13.8 2.3-28 3.5-42.5 3.5s-28.7-1.2-42.5-3.5c-9.2-1.5-16.2-8.7-18.2-17.8l-12.5-57.1c-15.8-6.5-30.6-15.1-44-25.4L83.1 425.9c-8.8 2.8-18.6 .3-24.5-6.8c-8.1-9.8-15.5-20.2-22.1-31.2l-4.7-8.1c-6.1-11-11.4-22.4-15.8-34.3c-3.2-8.7-.5-18.4 6.4-24.6l43.3-39.4C64.6 273.1 64 264.6 64 256s.6-17.1 1.7-25.4L22.4 191.2c-6.9-6.2-9.6-15.9-6.4-24.6c4.4-11.9 9.7-23.3 15.8-34.3l4.7-8.1c6.6-11 14-21.4 22.1-31.2c5.9-7.2 15.7-9.6 24.5-6.8l55.7 17.7c13.4-10.3 28.2-18.9 44-25.4l12.5-57.1c2-9.1 9-16.3 18.2-17.8C227.3 1.2 241.7 0 256 0s28.7 1.2 42.5 3.5c9.2 1.5 16.2 8.7 18.2 17.8l12.5 57.1c15.8 6.5 30.6 15.1 44 25.4l55.7-17.7c8.8-2.8 18.6-.3 24.5 6.8c8.1 9.8 15.5 20.2 22.1 31.2l4.7 8.1c6.1 11 11.4 22.4 15.8 34.3zM256 336a80 80 0 1 0 0-160 80 80 0 1 0 0 160z"/></svg>
                            <span>{{ __(t('settings')) }}</span>
                        </Link>
                        <Link :href="logout.url()" method="post" as="button" class="w-full flex items-center space-x-3 rtl:space-x-reverse px-3 py-2 text-sm text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded-xl transition-all font-bold text-start mt-1">
                            <svg class="w-4 h-4 text-center rtl:scale-x-[-1]" fill="currentColor" viewBox="0 0 512 512"><path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"/></svg>
                            <span>{{ __(t('logout')) }}</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<style scoped>
.dropdown-animate {
    transform-origin: top right;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
[dir="rtl"] .dropdown-animate {
    transform-origin: top left;
}
</style>
