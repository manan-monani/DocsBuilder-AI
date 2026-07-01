<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ChevronLeft, Save } from 'lucide-vue-next';
import { watch } from 'vue';
import admin from '@/routes/admin';
import { useToast } from '@/Services/toastService';
import SearchableSelect from '@/Components/Common/SearchableSelect.vue';

const props = defineProps({
    versions: Array,
    selected_version_id: [String, Number],
    category: {
        type: Object,
        default: () => ({
            name: '',
            slug: '',
            doc_version_id: '',
            order: 0,
            icon: 'folder',
        })
    },
    isEdit: Boolean
});

const form = useForm({
    name: props.isEdit ? props.category.name : '',
    slug: props.isEdit ? props.category.slug : '',
    doc_version_id: props.isEdit ? props.category.doc_version_id : (props.selected_version_id || ''),
    order: props.isEdit ? props.category.order : 0,
    icon: props.isEdit ? props.category.icon : 'folder',
});

watch(() => form.name, (name) => {
    if (!props.isEdit && name) {
        form.slug = name.toLowerCase()
            .replace(/[^\w ]+/g, '')
            .replace(/ +/g, '-');
    }
});

const { error: toastError } = useToast();

const submit = () => {
    const options = {
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
    };

    if (props.isEdit) {
        form.put(admin.docs.categories.update.url(props.category.id), options);
    } else {
        form.post(admin.docs.categories.store.url(), options);
    }
};

// Transform versions for SearchableSelect
const versionOptions = props.versions.map(version => ({
    id: version.id,
    label: `${version.project?.name} / ${version.name}`
}));
</script>

<template>
    <AdminLayout>
        <Head :title="isEdit ? 'Edit Category' : 'New Category'" />

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
                    {{ isEdit ? 'Edit Documentation Category' : 'Create New Category' }}
                </h1>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 space-y-6">
                        <!-- Version Selection -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 md:mt-2">
                                Version
                            </label>
                            <div class="md:col-span-3">
                                <SearchableSelect
                                    v-model="form.doc_version_id"
                                    :options="versionOptions"
                                    :disabled="isEdit || !!selected_version_id"
                                    label-key="label"
                                    value-key="id"
                                    placeholder="Select a version..."
                                />
                                <div v-if="form.errors.doc_version_id" class="mt-1 text-xs text-red-600">{{ form.errors.doc_version_id }}</div>
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
                                    placeholder="e.g. Getting Started, Tutorials, API Reference"
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
                                <p class="mt-1.5 text-xs text-gray-400">Lower numbers appear first in the sidebar navigation.</p>
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
                            {{ isEdit ? 'Update Category' : 'Create Category' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
