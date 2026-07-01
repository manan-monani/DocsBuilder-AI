<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { login, register } from '@/routes';
import { login as adminLogin } from '@/routes/admin';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import DirectionToggle from '@/Components/DirectionToggle.vue';
import Toast from '@/Components/Common/Toast.vue';
import { ref, computed } from 'vue';

defineProps<{
    status?: string;
    canResetPassword?: boolean;
}>();

const page = usePage();
const appName = computed(() => page.props.branding?.business_settings?.business_name || page.props.name || 'Laravel');

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);
const isAuthenticating = ref(false);

const submit = () => {
    isAuthenticating.value = true;
    form.post(login.url(), {
        onFinish: () => {
             form.reset('password');
             isAuthenticating.value = false;
        },
        onError: () => {
            isAuthenticating.value = false;
        }
    });
};

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};
</script>

<template>
    <Head :title="`${appName} | Login`" />
    <Toast />

    <div class="min-h-screen flex flex-col lg:flex-row bg-white dark:bg-gray-900 font-sans text-slate-700 dark:text-slate-300 transition-colors duration-300">
        <!-- Left Side: System Branding/Information -->
        <div class="lg:w-1/2 branded-bg relative flex flex-col items-center justify-center p-12 text-white overflow-hidden">
            <!-- Decorative Shapes -->
            <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
                <div class="absolute -top-24 -left-24 w-96 h-96 bg-white rounded-full mix-blend-overlay filter blur-3xl animate-float"></div>
                <div class="absolute bottom-1/4 right-0 w-64 h-64 bg-white rounded-full mix-blend-overlay filter blur-3xl animate-float" style="animation-delay: 2s;"></div>
            </div>

            <div class="relative z-10 max-w-lg text-center lg:text-left">
                <Link href="/" class="inline-flex items-center justify-center w-20 h-20 bg-white/10 backdrop-blur-md rounded-3xl mb-8 text-white text-3xl border border-white/20 overflow-hidden">
                    <img v-if="$page.props.branding.business_settings?.logo_url" :src="$page.props.branding.business_settings.logo_url.startsWith('http') ? $page.props.branding.business_settings.logo_url : '/storage/' + $page.props.branding.business_settings.logo_url" class="w-full h-full object-contain p-2" />
                    <svg v-else class="w-8 h-8" fill="currentColor" viewBox="0 0 320 512"><path d="M296 160H180.6l42.6-129.8C227.2 15 215.7 0 200 0H56C44 0 33.8 8.9 32.2 20.8l-32 240C-1.7 275.2 25.2 288 40 288h115.4l-42.6 129.8C108.8 434.9 120.3 448 136 448h144c12 0 22.2-8.9 23.8-20.8l32-240c1.9-14.3-9.2-27.2-24-27.2zM250.7 220.3l-32 240c-.6 4.3-4.1 7.7-8.7 7.7h-144c-4.1 0-7.7-2.9-8.4-7L100.2 332H40c-4.1 0-7.7-2.9-8.4-7L-11.4 69.8c-.8-5.8 3.7-10.9 9.5-10.9h144c4.1 0 7.7 2.9 8.4 7L167.8 196H228c4.1 0 7.7 2.9 8.4 7l24.3 15.3z"/></svg>
                </Link>
                <h1 class="text-4xl lg:text-6xl font-bold mb-6 leading-tight">{{ $page.props.branding.business_settings?.business_name || __($page.props.branding.login?.headline) }}</h1>
                <p class="text-primary-100 text-lg lg:text-xl mb-12 font-light leading-relaxed">
                    {{ $page.props.branding.business_settings?.tagline || __($page.props.branding.login?.subheadline) }}
                </p>

                <!-- Feature Checklist -->
                <div class="space-y-4">
                    <div v-for="(feature, index) in $page.props.branding.login?.features" :key="index" class="flex items-center space-x-3">
                        <div class="w-6 h-6 rounded-full bg-primary-400/20 flex items-center justify-center text-primary-200">
                             <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 512 512"><path d="M470.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L192 338.7 425.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"/></svg>
                        </div>
                        <span class="text-primary-50 font-medium">{{ __(feature) }}</span>
                    </div>
                </div>
            </div>

            <!-- Footer Copyright (Left) -->
            <div class="absolute bottom-8 left-12 hidden lg:block text-primary-200/50 text-sm">
                &copy; {{ new Date().getFullYear() }} {{ appName }} Inc. All rights reserved.
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="lg:w-1/2 flex items-center justify-center p-6 md:p-8 bg-white dark:bg-gray-900 transition-colors duration-300 relative">
            
            <div class="absolute top-4 right-4 flex items-center space-x-2">
                <DirectionToggle :show-label="false" />
                <LanguageSwitcher />
                <ThemeToggle />
            </div>

            <div class="w-full max-w-md">
                <!-- Mobile Branding (Only visible on small screens) -->
                <div class="lg:hidden text-center mb-10">
                    <Link href="/" class="inline-flex items-center justify-center w-12 h-12 bg-primary-600 rounded-xl mb-4 text-white">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 320 512"><path d="M296 160H180.6l42.6-129.8C227.2 15 215.7 0 200 0H56C44 0 33.8 8.9 32.2 20.8l-32 240C-1.7 275.2 25.2 288 40 288h115.4l-42.6 129.8C108.8 434.9 120.3 448 136 448h144c12 0 22.2-8.9 23.8-20.8l32-240c1.9-14.3-9.2-27.2-24-27.2zM250.7 220.3l-32 240c-.6 4.3-4.1 7.7-8.7 7.7h-144c-4.1 0-7.7-2.9-8.4-7L100.2 332H40c-4.1 0-7.7-2.9-8.4-7L-11.4 69.8c-.8-5.8 3.7-10.9 9.5-10.9h144c4.1 0 7.7 2.9 8.4 7L167.8 196H228c4.1 0 7.7 2.9 8.4 7l24.3 15.3z"/></svg>
                    </Link>
                    <h2 class="text-2xl font-bold text-slate-800 dark:text-white">{{ __($page.props.branding.login?.welcome_back) }}</h2>
                </div>

                <!-- Header -->
                <div class="mb-10 hidden lg:block">
                    <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-2">{{ __($page.props.branding.login?.form_title) }}</h2>
                    <p class="text-slate-500 dark:text-slate-400">{{ __($page.props.branding.login?.form_subtitle) }}</p>
                </div>

                 <div v-if="status" class="mb-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-600 dark:text-green-400 rounded-2xl p-4 text-sm font-medium">
                    {{ status }}
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 ml-1" for="email">{{ __('Email Address') }}</label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 group-focus-within:text-primary-600 dark:group-focus-within:text-primary-400 transition-colors duration-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </span>
                            <input
                                id="email"
                                type="email"
                                class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-xl outline-none text-slate-700 dark:text-gray-200 placeholder:text-slate-400 font-medium transition-all duration-300 focus:bg-white dark:focus:bg-gray-800 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:focus:ring-primary-500/20"
                                :placeholder="__('name@company.com')"
                                v-model="form.email"
                                required
                                autofocus
                            />
                        </div>
                        <div v-if="form.errors.email" class="mt-2 text-sm text-red-600 ml-1">{{ form.errors.email }}</div>
                    </div>

                    <div>
                        <div class="flex justify-between mb-2 px-1">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300" for="password">{{ __('Password') }}</label>
                            <a v-if="canResetPassword" href="#" class="text-xs font-bold text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 transition-colors">{{ __('Forgot?') }}</a>
                        </div>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 group-focus-within:text-primary-600 dark:group-focus-within:text-primary-400 transition-colors duration-300">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 448 512"><path d="M144 144v48H304V144c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192V144C80 64.5 144.5 0 224 0s144 64.5 144 144v48h16c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V256c0-35.3 28.7-64 64-64H80z"/></svg>
                            </span>
                            <input
                                :type="showPassword ? 'text' : 'password'"
                                id="password"
                                class="w-full pl-11 pr-12 py-3 bg-slate-50 dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-xl outline-none text-slate-700 dark:text-gray-200 placeholder:text-slate-400 font-medium transition-all duration-300 focus:bg-white dark:focus:bg-gray-800 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:focus:ring-primary-500/20"
                                :placeholder="__('••••••••')"
                                v-model="form.password"
                                required
                            />
                            <button
                                type="button"
                                @click="togglePassword"
                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 focus:outline-none transition-colors"
                            >
                                <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                            </button>
                        </div>
                        <div v-if="form.errors.password" class="mt-2 text-sm text-red-600 ml-1">{{ form.errors.password }}</div>
                    </div>

                    <div class="flex items-center px-1">
                        <input
                            type="checkbox"
                            id="remember"
                            v-model="form.remember"
                            class="w-4 h-4 rounded text-primary-600 border-slate-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 highlight-primary focus:ring-primary-500 cursor-pointer transition-all"
                        />
                        <label for="remember" class="ml-2 text-sm text-slate-600 dark:text-slate-400 cursor-pointer select-none font-medium">{{ __('Stay signed in') }}</label>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing || isAuthenticating"
                        :class="{'btn-loading': form.processing || isAuthenticating}"
                        class="w-full py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-2xl shadow-xl shadow-primary-200 dark:shadow-none transform transition-all active:scale-95 flex items-center justify-center space-x-2 disabled:opacity-75 disabled:cursor-not-allowed"
                    >
                        <span v-if="form.processing || isAuthenticating">{{ __('Logging in...') }}</span>
                        <span v-else>{{ __('Login to Platform') }}</span>
                        <svg v-if="!(form.processing || isAuthenticating)" class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        <svg v-else class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </button>
                </form>

                <!-- Social Login -->
                <div class="relative my-10">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-100 dark:border-slate-800"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white dark:bg-gray-900 text-slate-400 font-medium">{{ __('Or log in with') }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <button class="flex items-center justify-center px-4 py-3 border border-slate-200 dark:border-slate-700 rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-all transform active:scale-95">
                        <svg class="h-5 w-5 mr-2" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                        <span class="text-sm font-semibold text-slate-700 dark:text-gray-200">Google</span>
                    </button>
                    <button class="flex items-center justify-center px-4 py-3 border border-slate-200 dark:border-slate-700 rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-all transform active:scale-95">
                         <svg class="h-6 w-6 mr-2 text-slate-900 dark:text-white" fill="currentColor" viewBox="0 0 384 512"><path d="M318.7 268.7c-.2-36.7 16.4-64.4 50-84.8-18.8-26.9-47.2-41.7-84.7-44.6-35.5-2.8-74.3 20.7-88.5 20.7-15 0-49.4-19.7-76.4-19.7C63.3 141.2 4 184.8 4 273.5q0 39.3 14.4 81.2c12.8 36.7 59 126.7 107.2 125.2 25.2-.6 43-17.9 75.8-17.9 31.8 0 48.3 17.9 76.4 17.9 48.6-.7 90.4-82.5 102.6-119.3-65.2-30.7-71.7-84.9-72.2-111.9zM209.6 64.3c27.6-40.8 21.4-97.7 1.9-123.7-27.6 1.4-52 14.8-67.6 35.4-16.7 21.6-28.7 58.7-26.1 82.3 27.6 2.8 65.5-12.7 91.8-18.3z"/></svg>
                        <span class="text-sm font-semibold text-slate-700 dark:text-gray-200">Apple</span>
                    </button>
                </div>

                <!-- Footer -->
                <p class="text-center mt-12 text-slate-500 dark:text-slate-400 text-sm">
                    {{ __('New to our platform?') }} 
                    <Link :href="register.url()" class="font-bold text-primary-600 hover:text-primary-700 dark:hover:text-primary-400">{{ __('Create an account') }}</Link>
                </p>

                <div class="mt-6 text-center">
                    <a :href="adminLogin.url()" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition-colors">
                        {{ __($page.props.branding.login?.login_as_admin) }}
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

.branded-bg {
    background: linear-gradient(135deg, var(--color-primary-900) 0%, var(--color-primary-600) 100%);
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
}

.input-focus {
    transition: all 0.3s ease;
}

.input-focus:focus {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.1);
}

.btn-loading {
    pointer-events: none;
    opacity: 0.8;
}
</style>
