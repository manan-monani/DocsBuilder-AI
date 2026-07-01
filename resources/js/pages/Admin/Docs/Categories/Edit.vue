<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ChevronLeft, Save } from 'lucide-vue-next';
import admin from '@/routes/admin';
import { useToast } from '@/Services/toastService';

const props = defineProps({
    versions: Array,
    category: Object
});

const form = useForm({
    name: props.category.name,
    slug: props.category.slug,
    doc_version_id: props.category.doc_version_id,
    order: props.category.order,
    icon: props.category.icon,
});

const { error: toastError } = useToast();

const submit = () => {
    form.put(admin.docs.categories.update.url(props.category.id), {
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
        <Head title="Edit Category" />

        <div class="max-w-4xl mx-auto p-6">
            <div class="mb-6">
                <Link
                    :href="admin.docs.categories.index.url({ query: { doc_version_id: form.doc_version_id } })"
                    class="inline-flex items-center text-sm text-gray-500 hover:text-indigo-600 transition-colors"
                >
                    <ChevronLeft class="w-4 h-4 mr-1" />
                    Back to Categories
                </Link>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                    Edit Documentation Category
                </h1>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 space-y-6">
                        <!-- Version Selection (Read-only) -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 md:mt-2">
                                Version
                            </label>
                            <div class="md:col-span-3">
                                <input
                                    :value="versions.find(v => v.id === form.doc_version_id)?.project?.name + ' / ' + versions.find(v => v.id === form.doc_version_id)?.name"
                                    type="text"
                                    class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-500 outline-none cursor-not-allowed"
                                    disabled
                                >
                            </div>
                        </div>

                        <!-- Category Name -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 md:mt-2">
                                Category Name
                            </label>
                            <div class="md:col-span-3">
                                <input
                                    v-model="form.name"
                                    type="text"
                                    class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all"
                                    placeholder="e.g. Getting Started"
                                    required
                                >
                                <div v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</div>
                            </div>
                        </div>

                        <!-- Slug -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 md:mt-2">
                                Category Slug
                            </label>
                            <div class="md:col-span-3">
                                <input
                                    v-model="form.slug"
                                    type="text"
                                    class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-mono"
                                    placeholder="getting-started"
                                    required
                                >
                                <div v-if="form.errors.slug" class="mt-1 text-xs text-red-600">{{ form.errors.slug }}</div>
                            </div>
                        </div>

                        <!-- Order -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 md:mt-2">
                                Display Order
                            </label>
                            <div class="md:col-span-3">
                                <input
                                    v-model="form.order"
                                    type="number"
                                    class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all"
                                    required
                                >
                                <div v-if="form.errors.order" class="mt-1 text-xs text-red-600">{{ form.errors.order }}</div>
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
                            Update Category
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
