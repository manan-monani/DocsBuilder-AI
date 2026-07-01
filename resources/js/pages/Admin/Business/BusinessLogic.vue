<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { index, update } from '@/routes/admin/business/settings';
import { Settings, Globe, Search, Clock } from 'lucide-vue-next';

const props = defineProps<{
    settings: {
        timezone: string;
        language: string;
    };
}>();

const form = useForm({
    timezone: props.settings.timezone,
    language: props.settings.language,
});



const submit = () => {
    form.post(update.url(), {
        preserveScroll: true,
        onSuccess: () => {
            // Form will be reset with new values from server
        },
    });
};

// Search states
const timezoneSearch = ref('');

// Common timezones list with regions
const timezones = [
    'UTC',
    'America/New_York',
    'America/Chicago',
    'America/Denver',
    'America/Los_Angeles',
    'America/Toronto',
    'America/Mexico_City',
    'America/Sao_Paulo',
    'America/Argentina/Buenos_Aires',
    'Europe/London',
    'Europe/Paris',
    'Europe/Berlin',
    'Europe/Rome',
    'Europe/Moscow',
    'Europe/Istanbul',
    'Africa/Cairo',
    'Africa/Johannesburg',
    'Asia/Dubai',
    'Asia/Karachi',
    'Asia/Kolkata',
    'Asia/Dhaka',
    'Asia/Bangkok',
    'Asia/Singapore',
    'Asia/Hong_Kong',
    'Asia/Tokyo',
    'Asia/Seoul',
    'Asia/Shanghai',
    'Australia/Sydney',
    'Australia/Melbourne',
    'Pacific/Auckland',
    'Pacific/Fiji',
];

// Get current time and UTC offset for a timezone
const getTimezoneInfo = (timezone: string) => {
    try {
        const now = new Date();

        // Get formatted time
        const timeFormatter = new Intl.DateTimeFormat('en-US', {
            timeZone: timezone,
            hour: '2-digit',
            minute: '2-digit',
            hour12: true,
        });
        const time = timeFormatter.format(now);

        // Calculate UTC offset
        const utcDate = new Date(now.toLocaleString('en-US', { timeZone: 'UTC' }));
        const tzDate = new Date(now.toLocaleString('en-US', { timeZone: timezone }));
        const offsetMinutes = (tzDate.getTime() - utcDate.getTime()) / (1000 * 60);
        const offsetHours = offsetMinutes / 60;

        // Format offset as UTC+X or UTC-X
        const offsetSign = offsetHours >= 0 ? '+' : '';
        const offset = `UTC${offsetSign}${offsetHours}`;

        return { time, offset };
    } catch (e) {
        return { time: '', offset: 'UTC' };
    }
};

// Filtered timezones based on search
const filteredTimezones = computed(() => {
    if (!timezoneSearch.value) return timezones;
    const search = timezoneSearch.value.toLowerCase();
    return timezones.filter(tz => tz.toLowerCase().includes(search));
});




</script>

<template>
    <AdminLayout>
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
                        <Settings class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ __('Business Logic') }}
                    </h1>
                </div>
                <p class="text-gray-600 dark:text-gray-400">
                    {{ __('Configure your business currency, timezone, and location settings.') }}
                </p>
            </div>

            <!-- Settings Form -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <form @submit.prevent="submit" class="p-6 space-y-6">




                    <!-- Timezone with Search and Time Display -->
                    <div>
                        <label for="timezone" class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <Globe class="w-4 h-4" />
                            {{ __('Timezone') }}
                        </label>

                        <!-- Search Input -->
                        <div class="relative mb-3">
                            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                            <input
                                v-model="timezoneSearch"
                                type="text"
                                placeholder="Search timezone (e.g., Dhaka, New York, Tokyo)..."
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white text-sm"
                            />
                        </div>

                        <!-- Timezone List -->
                        <div class="max-h-80 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                            <div v-if="filteredTimezones.length > 0" class="divide-y divide-gray-200 dark:divide-gray-600">
                                <button
                                    v-for="tz in filteredTimezones"
                                    :key="tz"
                                    type="button"
                                    @click="form.timezone = tz; timezoneSearch = ''"
                                    class="w-full flex items-center justify-between px-4 py-3 text-left transition-colors"
                                    :class="form.timezone === tz
                                        ? 'bg-primary-50 dark:bg-primary-900/20 border-l-4 border-primary-500'
                                        : 'hover:bg-white dark:hover:bg-gray-800'"
                                >
                                    <div class="flex-1 min-w-0 pr-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ tz }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ getTimezoneInfo(tz).offset }}</div>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs font-mono text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 px-3 py-1.5 rounded-md border border-gray-200 dark:border-gray-600 whitespace-nowrap">
                                        <Clock class="w-3 h-3" />
                                        {{ getTimezoneInfo(tz).time }}
                                    </div>
                                </button>
                            </div>
                            <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                                <Search class="w-8 h-8 mx-auto mb-2 opacity-50" />
                                <p class="text-sm">No timezones found</p>
                            </div>
                        </div>

                        <p v-if="form.errors.timezone" class="mt-2 text-sm text-red-600 dark:text-red-400">
                            {{ form.errors.timezone }}
                        </p>
                    </div>



                    <!-- Submit Button -->
                    <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 disabled:bg-gray-400 text-white rounded-lg font-medium transition-colors shadow-lg shadow-primary-500/30 disabled:shadow-none flex items-center gap-2"
                        >
                            <Settings class="w-4 h-4" :class="{ 'animate-spin': form.processing }" />
                            {{ form.processing ? __('Saving...') : __('Save Settings') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
