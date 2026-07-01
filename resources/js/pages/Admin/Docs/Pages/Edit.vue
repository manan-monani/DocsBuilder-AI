<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ChevronLeft, Save, Eye } from 'lucide-vue-next';
import { watch } from 'vue';
import admin from '@/routes/admin';
import RichTextEditor from '@/Components/Common/RichTextEditor.vue';
import SearchableSelect from '@/Components/Common/SearchableSelect.vue';
import { useToast } from '@/Services/toastService';

const props = defineProps({
    categories: Array,
    page: Object
});

const form = useForm({
    title: props.page.title,
    slug: props.page.slug,
    doc_category_id: props.page.doc_category_id,
    content_html: props.page.content_html || '',
    content_json: props.page.content_json || '{}',
    order: props.page.order ?? 0,
    status: props.page.status,

});

const { error: toastError } = useToast();

const submit = () => {
    form.put(admin.docs.pages.update.url(props.page.id), {
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

// Transform categories for select
const categoryOptions = props.categories.map(cat => ({
    id: cat.id,
    label: `${cat.version?.project?.name} / ${cat.version?.name} / ${cat.name}`
}));
</script>

<template>
    <AdminLayout>
        <Head title="Edit Page" />

        <div class="p-6">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <Link
                            :href="admin.docs.pages.index.url({ query: { doc_category_id: form.doc_category_id } })"
                            class="inline-flex items-center text-sm text-gray-500 hover:text-indigo-600 transition-colors mb-2"
                        >
                            <ChevronLeft class="w-4 h-4 mr-1" />
                            Back to Pages
                        </Link>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Edit Documentation Page
                        </h1>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="flex bg-white dark:bg-gray-800 rounded-lg p-1 border border-gray-200 dark:border-gray-700 shadow-sm">
                            <button
                                type="button"
                                @click="form.status = 'draft'"
                                :class="[form.status === 'draft' ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-500']"
                                class="px-3 py-1.5 text-xs font-bold uppercase tracking-wider rounded-md transition-all"
                            >
                                Draft
                            </button>
                            <button
                                type="button"
                                @click="form.status = 'published'"
                                :class="[form.status === 'published' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'text-gray-500']"
                                class="px-3 py-1.5 text-xs font-bold uppercase tracking-wider rounded-md transition-all"
                            >
                                Published
                            </button>
                        </div>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="text-white px-4 py-2 rounded-xl font-bold text-sm shadow-lg transition-all flex items-center gap-2 hover:opacity-90 disabled:opacity-70"
                            :style="{ backgroundColor: 'var(--brand-primary)', boxShadow: '0 10px 15px -3px var(--brand-primary-transparent, rgba(0,0,0,0.1))' }"
                        >
                            <Save :size="18" />
                            Save Changes
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Title & Slug Section -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Page Title</label>
                            <input
                                v-model="form.title"
                                type="text"
                                class="w-full text-3xl font-bold bg-transparent border-none focus:ring-0 p-0 placeholder-gray-300 dark:placeholder-gray-700 text-gray-900 dark:text-white"
                                placeholder="Enter page title here..."
                                required
                            >
                            <div v-if="form.errors.title" class="mt-1 text-xs text-red-600">{{ form.errors.title }}</div>

                            <div class="flex items-center mt-6 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-100 dark:border-gray-700">
                                <span class="text-xs font-mono text-gray-400 mr-2 select-none">slug:</span>
                                <input
                                    v-model="form.slug"
                                    type="text"
                                    class="w-full bg-transparent border-none text-sm font-mono text-gray-600 dark:text-gray-300 focus:ring-0 p-0"
                                    placeholder="page-slug"
                                    required
                                >
                            </div>
                            <div v-if="form.errors.slug" class="mt-1 text-xs text-red-600">{{ form.errors.slug }}</div>
                        </div>

                        <!-- Content Editor -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col min-h-[600px] overflow-hidden">
                            <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Page Content</label>
                            </div>
                             <!-- Added dedicated bg-white to wrapper -->
                            <div class="flex-1 bg-white dark:bg-gray-800">
                                <RichTextEditor v-model="form.content_html" class="flex-1 border-none min-h-[500px]" />
                            </div>
                            <div v-if="form.errors.content_html" class="p-4 text-xs text-red-600 border-t border-gray-100 dark:border-gray-700">{{ form.errors.content_html }}</div>
                        </div>
                    </div>

                    <!-- Sidebar Area -->
                    <div class="space-y-6">
                        <!-- Configuration -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-4">
                            <h3 class="font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2 mb-4">Configuration</h3>

                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Category</label>
                                <SearchableSelect
                                    v-model="form.doc_category_id"
                                    :options="categoryOptions"
                                    :disabled="false"
                                    label-key="label"
                                    value-key="id"
                                    placeholder="Select a Category..."
                                />
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Order</label>
                                <input
                                    v-model="form.order"
                                    type="number"
                                    class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                                    required
                                >
                            </div>
                        </div>


                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
