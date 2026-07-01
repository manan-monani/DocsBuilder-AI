<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { login, register, contact } from '@/routes';
import { dashboard as customerDashboard } from '@/routes/customer';
import { dashboard as adminDashboard, login as adminLogin } from '@/routes/admin';
import { privacy, terms, about } from '@/routes/legal';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import Toast from '@/Components/Common/Toast.vue';
import { computed, onMounted, ref } from 'vue';
import { Menu, X, Facebook, Twitter, Instagram } from 'lucide-vue-next';

const props = defineProps<{
    fluid?: boolean;
}>();

const page = usePage();
const user = computed(() => (page.props as any).auth?.user);
const businessSettings = computed(() => (page.props.branding as any)?.business_settings || {});

const logoUrl = computed(() => {
    const logo = businessSettings.value.business_logo || businessSettings.value.logo_url;
    if (!logo) return null;
    return logo.startsWith('http') ? logo : '/storage/' + logo;
});

const dashboardUrl = computed(() => {
    if (!user.value) return '#';
    return user.value.type === 'admin' || user.value.type === 'super-admin'
        ? adminDashboard.url()
        : customerDashboard.url();
});

const isMobileMenuOpen = ref(false);

onMounted(() => {
    const locale = (page.props as any).locale || 'en';
    const direction = locale === 'ar' ? 'rtl' : 'ltr';
    document.documentElement.setAttribute('dir', direction);
    localStorage.setItem('direction', direction);
});
</script>

<template>
    <div class="min-h-screen flex flex-col bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
        <Toast />

        <!-- Header -->
        <header class="bg-white dark:bg-gray-800 shadow-sm sticky top-0 z-50 border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo/Brand -->
                    <div class="flex items-center gap-8">
                        <Link href="/" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
                            <img v-if="logoUrl" :src="logoUrl" class="h-10 w-auto object-contain" :alt="businessSettings.business_name || 'Logo'" />
                            <span class="text-xl font-bold text-gray-800 dark:text-white truncate max-w-[150px] md:max-w-none">
                                {{ businessSettings.business_name || 'Laravel Factory' }}
                            </span>
                        </Link>
                    </div>

                    <!-- Desktop Navigation (Right Side) -->
                    <div class="hidden lg:flex items-center gap-6">
                        <Link href="/" class="text-sm font-bold text-gray-500 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400 transition-colors">
                            {{ __('Home') }}
                        </Link>
                        <Link v-if="businessSettings.about_us" :href="about.url()" class="text-sm font-bold text-gray-500 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400 transition-colors">
                            {{ __('About Us') }}
                        </Link>
                        <Link :href="contact.url()" class="text-sm font-bold text-gray-500 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400 transition-colors">
                            {{ __('Contact Us') }}
                        </Link>

                        <div class="h-6 w-px bg-gray-200 dark:bg-gray-700"></div>
                        <ThemeToggle />
                    </div>

                    <!-- Mobile Menu Button -->
                    <div class="flex items-center lg:hidden gap-3">
                        <ThemeToggle />
                        <button @click="isMobileMenuOpen = !isMobileMenuOpen" class="text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400">
                            <Menu v-if="!isMobileMenuOpen" class="w-6 h-6" />
                            <X v-else class="w-6 h-6" />
                        </button>
                    </div>
                </div>

                <!-- Mobile Menu -->
                <div v-show="isMobileMenuOpen" class="lg:hidden py-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col gap-3">
                        <div class="space-y-2 mb-2">
                            <Link href="/" class="block text-base font-bold text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 py-2">
                                {{ __('Home') }}
                            </Link>
                            <Link v-if="businessSettings.about_us" :href="about.url()" class="block text-base font-bold text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 py-2">
                                {{ __('About Us') }}
                            </Link>
                            <Link :href="contact.url()" class="block text-base font-bold text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 py-2">
                                {{ __('Contact Us') }}
                            </Link>
                            <Link v-if="businessSettings.privacy_policy" :href="privacy.url()" class="block text-base font-bold text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 py-2">
                                {{ __('Privacy Policy') }}
                            </Link>
                            <Link v-if="businessSettings.terms_and_conditions" :href="terms.url()" class="block text-base font-bold text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 py-2">
                                {{ __('Terms') }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="flex-grow">
            <div :class="[fluid ? '' : 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12']">
                <slot />
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="text-sm text-gray-500 dark:text-gray-400 text-center md:text-left">
                        &copy; {{ new Date().getFullYear() }} {{ businessSettings.business_name || 'Company Name' }}. {{ __('All rights reserved.') }}
                    </div>

                    <div class="flex flex-wrap justify-center gap-6">
                        <Link v-if="businessSettings.privacy_policy" :href="privacy.url()" class="text-sm text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                            {{ __('Privacy Policy') }}
                        </Link>
                        <Link v-if="businessSettings.terms_and_conditions" :href="terms.url()" class="text-sm text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                            {{ __('Terms & Conditions') }}
                        </Link>
                    </div>
                </div>
            </div>
        </footer>


    </div>
</template>
