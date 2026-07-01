<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ChevronLeft, Save, Globe, FileText } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import admin from '@/routes/admin';

const props = defineProps({
    project: {
        type: Object,
        default: () => ({
            name: '',
            slug: '',
            description: '',
            is_active: true,
        })
    },
    isEdit: Boolean
});

const form = useForm({
    name: props.project.name,
    slug: props.project.slug,
    description: props.project.description,
    is_active: props.project.is_active,
    logo: null,
});

const previewUrl = ref(null);

watch(() => form.name, (name) => {
    if (!props.isEdit && name) {
        form.slug = name.toLowerCase()
            .replace(/[^\w ]+/g, '')
            .replace(/ +/g, '-');
    }
});

const handleLogoChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.logo = file;
        previewUrl.value = URL.createObjectURL(file);
    }
};

// ... imports
import { useToast } from '@/Services/toastService';

const toast = useToast();

// ...

const submit = () => {
    if (props.isEdit) {
        form.transform((data) => ({
            ...data,
            _method: 'PUT',
        })).post(admin.docs.projects.update.url(props.project.id), {
            onError: (errors) => {
                if (errors.name) {
                    toast.error(errors.name);
                }
                if (errors.slug) {
                    toast.error(errors.slug);
                }
            }
        });
    } else {
        form.post(admin.docs.projects.store.url(), {
            onError: (errors) => {
                if (errors.name) {
                    toast.error(errors.name);
                }
                if (errors.slug) {
                    toast.error(errors.slug);
                }
            }
        });
    }
};
</script>

<template>
    <AdminLayout>
        <Head :title="isEdit ? 'Edit Project' : 'New Project'" />

        <div class="max-w-4xl mx-auto p-6">
            <div class="mb-6">
                <Link
                    :href="admin.docs.projects.index.url()"
                    class="inline-flex items-center text-sm text-gray-500 hover:text-indigo-600 transition-colors"
                >
                    <ChevronLeft class="w-4 h-4 mr-1" />
                    Back to Projects
                </Link>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                    {{ isEdit ? 'Edit Documentation Project' : 'Create New Project' }}
                </h1>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 space-y-6">
                        <!-- Project Logo -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 md:mt-2">
                                Project Logo
                            </label>
                            <div class="md:col-span-3">
                                <div class="flex items-center space-x-6">
                                    <div v-if="previewUrl || props.project.logo" class="shrink-0">
                                        <img
                                            :src="previewUrl || '/storage/' + props.project.logo"
                                            class="h-16 w-16 object-cover rounded-lg border border-gray-200 dark:border-gray-700"
                                            alt="Project Logo"
                                        />
                                    </div>
                                    <div class="flex-1">
                                        <input
                                            type="file"
                                            @change="handleLogoChange"
                                            accept="image/*"
                                            class="block w-full text-sm text-slate-500
                                                file:mr-4 file:py-2 file:px-4
                                                file:rounded-full file:border-0
                                                file:text-sm file:font-semibold
                                                file:bg-indigo-50 file:text-indigo-700
                                                hover:file:bg-indigo-100
                                                dark:file:bg-indigo-900/30 dark:file:text-indigo-400
                                            "
                                        />
                                        <p class="mt-1 text-xs text-gray-500">SVG, PNG, JPG or GIF (max. 2MB)</p>
                                        <div v-if="form.errors.logo" class="mt-1 text-xs text-red-600">{{ form.errors.logo }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Project Name -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 md:mt-2">
                                Project Name
                            </label>
                            <div class="md:col-span-3">
                                <input
                                    v-model="form.name"
                                    type="text"
                                    class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all"
                                    placeholder="e.g. API Documentation"
                                    required
                                >
                                <div v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</div>
                            </div>
                        </div>

                        <!-- Slug -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 md:mt-2">
                                URL Slug
                            </label>
                            <div class="md:col-span-3">
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">/docs/</span>
                                    <input
                                        v-model="form.slug"
                                        type="text"
                                        class="w-full pl-[52px] pr-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-mono"
                                        placeholder="api-docs"
                                        required
                                    >
                                </div>
                                <div v-if="form.errors.slug" class="mt-1 text-xs text-red-600">{{ form.errors.slug }}</div>
                                <p class="mt-1.5 text-xs text-gray-400">Unique identifier used in the documentation URL.</p>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 md:mt-2">
                                Description
                            </label>
                            <div class="md:col-span-3">
                                <textarea
                                    v-model="form.description"
                                    rows="4"
                                    class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all resize-none"
                                    placeholder="Describe what this documentation covers..."
                                ></textarea>
                                <div v-if="form.errors.description" class="mt-1 text-xs text-red-600">{{ form.errors.description }}</div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Active Status
                            </label>
                            <div class="md:col-span-3">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" v-model="form.is_active" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ form.is_active ? 'Publicly Accessible' : 'Hidden from Public' }}
                                    </span>
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
                            {{ isEdit ? 'Update Project' : 'Create Project' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
