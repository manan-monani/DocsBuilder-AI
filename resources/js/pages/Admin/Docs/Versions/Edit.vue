<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ChevronLeft, Save, Star, Archive } from 'lucide-vue-next';
import admin from '@/routes/admin';
import { useToast } from '@/Services/toastService';

const props = defineProps({
    projects: Array,
    version: Object
});

const form = useForm({
    name: props.version.name,
    slug: props.version.slug,
    doc_project_id: props.version.doc_project_id,
    is_default: props.version.is_default,
    is_archived: props.version.is_archived,
});

const { error: toastError } = useToast();

const submit = () => {
    form.put(admin.docs.versions.update.url(props.version.id), {
        onError: (errors) => {
            // Show all error messages from the response
            const errorMessages = Object.values(errors);

            if (errorMessages.length > 0) {
                // Use Set to ensure unique messages
                [...new Set(errorMessages)].forEach(message => {
                    toastError(message);
                });
            } else {
                toastError('There are errors in the form. Please check the fields.');
            }
        }
    });
};
</script>

<template>
    <AdminLayout>
        <Head title="Edit Version" />

        <div class="max-w-4xl mx-auto p-6">
            <div class="mb-6">
                <Link
                    :href="admin.docs.versions.index.url({ query: { doc_project_id: form.doc_project_id } })"
                    class="inline-flex items-center text-sm text-gray-500 hover:text-indigo-600 transition-colors"
                >
                    <ChevronLeft class="w-4 h-4 mr-1" />
                    Back to Versions
                </Link>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                    Edit Documentation Version
                </h1>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 space-y-6">
                        <!-- Project Selection (Read-only) -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 md:mt-2">
                                Project
                            </label>
                            <div class="md:col-span-3">
                                <input
                                    :value="projects.find(p => p.id === form.doc_project_id)?.name"
                                    type="text"
                                    class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-500 outline-none cursor-not-allowed"
                                    disabled
                                >
                            </div>
                        </div>

                        <!-- Version Name -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 md:mt-2">
                                Version Name
                            </label>
                            <div class="md:col-span-3">
                                <input
                                    v-model="form.name"
                                    type="text"
                                    class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all"
                                    placeholder="e.g. v1.0, 2.x, Beta"
                                    required
                                >
                                <div v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</div>
                            </div>
                        </div>

                        <!-- Slug -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 md:mt-2">
                                Version Slug
                            </label>
                            <div class="md:col-span-3">
                                <input
                                    v-model="form.slug"
                                    type="text"
                                    class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-mono"
                                    placeholder="v1.0"
                                    required
                                >
                                <div v-if="form.errors.slug" class="mt-1 text-xs text-red-600">{{ form.errors.slug }}</div>
                            </div>
                        </div>

                        <!-- Settings -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Settings
                            </label>
                            <div class="md:col-span-3 space-y-4">
                                <label class="flex items-center cursor-pointer group">
                                    <div class="relative inline-flex items-center">
                                        <input type="checkbox" v-model="form.is_default" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-yellow-500"></div>
                                    </div>
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center">
                                            Default version
                                            <Star class="w-3 h-3 ml-1.5 text-yellow-500" />
                                        </span>
                                        <p class="text-xs text-gray-400">Users will be redirected here when viewing the project root.</p>
                                    </div>
                                </label>

                                <label class="flex items-center cursor-pointer group">
                                    <div class="relative inline-flex items-center">
                                        <input type="checkbox" v-model="form.is_archived" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-gray-600"></div>
                                    </div>
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center">
                                            Archived version
                                            <Archive class="w-3 h-3 ml-1.5 text-gray-500" />
                                        </span>
                                        <p class="text-xs text-gray-400">Archived versions are hidden from version switchers by default.</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="text-white px-4 py-2 rounded-xl font-bold text-sm shadow-lg transition-all flex items-center gap-2 hover:opacity-90 disabled:opacity-70"
                            :style="{ backgroundColor: 'var(--brand-primary)', boxShadow: '0 10px 15px -3px var(--brand-primary-transparent, rgba(0,0,0,0.1))' }"
                        >
                            <Save :size="18" />
                            Update Version
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
