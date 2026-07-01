<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ChevronLeft,
    History,
    RotateCcw,
    User,
    Calendar,
    CheckCircle2
} from 'lucide-vue-next';
import admin from '@/routes/admin';

const props = defineProps({
    page: Object,
    revisions: Array,
});

const restoreRevision = (id) => {
    if (confirm('Are you sure you want to restore this revision? Current content will be saved as a new revision.')) {
        router.post(admin.docs.revisions.restore.url(id));
    }
};
</script>

<template>
    <AdminLayout>
        <Head :title="`Revision History - ${page?.title}`" />

        <div class="max-w-4xl mx-auto p-6">
            <div class="mb-6">
                <Link
                    :href="admin.docs.pages.edit.url(page?.id)"
                    class="inline-flex items-center text-sm text-gray-500 hover:text-indigo-600 transition-colors mb-2"
                >
                    <ChevronLeft class="w-4 h-4 mr-1" />
                    Back to Editor
                </Link>
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Revision History
                    </h1>
                    <span class="text-sm font-mono text-gray-400 bg-gray-50 dark:bg-gray-900 px-3 py-1 rounded-full border border-gray-200 dark:border-gray-700">
                        {{ page?.title }}
                    </span>
                </div>
            </div>

            <div class="relative">
                <!-- Timeline line -->
                <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"></div>

                <div class="space-y-8 relative">
                    <div
                        v-for="(revision, index) in revisions"
                        :key="revision.id"
                        class="flex group"
                    >
                        <!-- Timeline Point -->
                        <div
                            :class="[
                                index === 0 ? 'bg-indigo-600 ring-4 ring-indigo-100 dark:ring-indigo-900/30' : 'bg-gray-300 dark:bg-gray-600',
                                'w-3 h-3 rounded-full mt-6 relative z-10 transition-all group-hover:scale-125 ml-[19px]'
                            ]"
                        ></div>

                        <!-- Content Card -->
                        <div class="flex-1 ml-8">
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition-shadow">
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex items-center text-xs text-gray-500">
                                            <Calendar class="w-3.5 h-3.5 mr-1.5 opacity-60" />
                                            {{ new Date(revision.created_at).toLocaleString() }}
                                        </div>
                                        <div class="flex items-center text-xs text-gray-500">
                                            <User class="w-3.5 h-3.5 mr-1.5 opacity-60" />
                                            {{ revision.user?.name || 'System' }}
                                        </div>
                                        <span v-if="revision.is_snapshot" class="px-2 py-0.5 bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 text-[10px] font-bold uppercase rounded">Snapshot</span>
                                        <span v-if="index === 0" class="px-2 py-0.5 bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-400 text-[10px] font-bold uppercase rounded flex items-center">
                                            <CheckCircle2 class="w-2.5 h-2.5 mr-1" />
                                            Current
                                        </span>
                                    </div>
                                    <button
                                        v-if="index !== 0"
                                        @click="restoreRevision(revision.id)"
                                        class="inline-flex items-center px-3 py-1.5 bg-gray-50 hover:bg-indigo-600 hover:text-white dark:bg-gray-900 text-gray-600 dark:text-gray-400 text-xs font-bold rounded-lg transition-all border border-gray-200 dark:border-gray-700"
                                    >
                                        <RotateCcw class="w-3 h-3 mr-1.5" />
                                        Restore this version
                                    </button>
                                </div>
                                <div class="p-3 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-100 dark:border-gray-700">
                                    <p class="text-sm text-gray-700 dark:text-gray-300 italic">
                                        {{ revision.change_summary || 'No summary provided.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
