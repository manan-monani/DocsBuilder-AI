<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ChevronLeft, Save, Star, Archive } from 'lucide-vue-next';
import { watch } from 'vue';
import admin from '@/routes/admin';
import { useToast } from '@/Services/toastService';
import SearchableSelect from '@/Components/Common/SearchableSelect.vue';

const props = defineProps({
    projects: Array,
    selected_project_id: [String, Number],
    version: {
        type: Object,
        default: () => ({
            name: '',
            slug: '',
            doc_project_id: '',
            is_default: false,
            is_archived: false,
        })
    },
    isEdit: Boolean
});

const form = useForm({
    name: props.isEdit ? props.version.name : '',
    slug: props.isEdit ? props.version.slug : '',
    doc_project_id: props.isEdit ? props.version.doc_project_id : (props.selected_project_id || ''),
    is_default: props.isEdit ? props.version.is_default : false,
    is_archived: props.isEdit ? props.version.is_archived : false,
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
        form.put(admin.docs.versions.update.url(props.version.id), options);
    } else {
        form.post(admin.docs.versions.store.url(), options);
    }
};

// Transform projects for SearchableSelect
const projectOptions = props.projects.map(project => ({
    id: project.id,
    label: project.name
}));
</script>

<template>
    <AdminLayout>
        <Head :title="isEdit ? 'Edit Version' : 'New Version'" />

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
                    {{ isEdit ? 'Edit Documentation Version' : 'Create New Version' }}
                </h1>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 space-y-6">
                        <!-- Project Selection -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 md:mt-2">
                                Project
                            </label>
                            <div class="md:col-span-3">
                                <SearchableSelect
                                    v-model="form.doc_project_id"
                                    :options="projectOptions"
                                    :disabled="isEdit || !!selected_project_id"
                                    label-key="label"
                                    value-key="id"
                                    placeholder="Select a project..."
                                />
                                <div v-if="form.errors.doc_project_id" class="mt-1 text-xs text-red-600">{{ form.errors.doc_project_id }}</div>
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
                            {{ isEdit ? 'Update Version' : 'Create Version' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
