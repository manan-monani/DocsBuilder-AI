<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Plus,
    ChevronLeft,
    Edit,
    Trash2,
    FileText,
    Eye,
    History,
    MoreVertical,
    GripVertical,
    Search
} from 'lucide-vue-next';
import admin from '@/routes/admin';
import Pagination from '@/Components/Common/Pagination.vue';
import SearchableSelect from '@/Components/Common/SearchableSelect.vue';

const props = defineProps({
    category: Object,
    pages: Object,
    allProjects: Array,
    allVersions: Array,
    allCategories: Array,
    filters: Object,
});

import ConfirmationModal from '@/Components/Common/ConfirmationModal.vue';
import { ref, watch, computed } from 'vue';

const showDeleteModal = ref(false);
const pageToDeleteId = ref(null);
const isDeleting = ref(false);

const deletePage = (id) => {
    pageToDeleteId.value = id;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    if (!pageToDeleteId.value) return;

    isDeleting.value = true;
    router.delete(admin.docs.pages.destroy.url(pageToDeleteId.value), {
        preserveScroll: true,
        onFinish: () => {
            isDeleting.value = false;
            showDeleteModal.value = false;
            pageToDeleteId.value = null;
        }
    });
};

const getStatusClass = (status) => {
    switch (status) {
        case 'published': return 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
        case 'draft': return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400';
        case 'archived': return 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400';
        default: return 'bg-gray-100 text-gray-700';
    }
};

const search = ref(props.filters?.search || '');
const selectedProject = ref(props.filters?.doc_project_id || null);
const selectedVersion = ref(props.filters?.doc_version_id || null);
const selectedCategory = ref(props.filters?.doc_category_id || null);

// Options for filters
const projectOptions = props.allProjects?.map(p => ({ id: p.id, label: p.name })) || [];

// Versions are filtered from backend based on project
const versionOptions = computed(() => {
    const versions = props.allVersions || [];
    return versions.map(v => ({ id: v.id, label: v.name }));
});

// Categories are filtered from backend based on version/project
const categoryOptions = computed(() => {
    const categories = props.allCategories || [];
    return categories.map(c => ({ id: c.id, label: c.name }));
});

// Apply filters
const applyFilters = () => {
    router.get(admin.docs.pages.index.url(), {
        search: search.value,
        doc_project_id: selectedProject.value,
        doc_version_id: selectedVersion.value,
        doc_category_id: selectedCategory.value
    }, {
        preserveState: true,
        replace: true,
    });
};

watch(search, applyFilters);
watch(selectedCategory, applyFilters);

// Reset version and category when project changes
watch(selectedProject, (value) => {
    if (value) {
        selectedVersion.value = null;
        selectedCategory.value = null;
    }
    applyFilters();
});

// Reset category when version changes
watch(selectedVersion, (value) => {
    if (value) {
        selectedCategory.value = null;
    }
    applyFilters();
});
</script>

<template>
    <AdminLayout>
        <Head :title="category ? `Pages - ${category.name}` : 'All Pages'" />

        <div class="p-6">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <Link
                        v-if="category"
                        :href="admin.docs.categories.index.url({ query: { doc_version_id: category.doc_version_id } })"
                        class="inline-flex items-center text-sm text-gray-500 hover:text-indigo-600 transition-colors mb-2"
                    >
                        <ChevronLeft class="w-4 h-4 mr-1" />
                        Back to Categories
                    </Link>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        <template v-if="category">
                            <span class="text-gray-400 font-normal">{{ category?.version?.name }} /</span> {{ category?.name }} <span class="text-gray-400 font-normal ml-2">Pages</span>
                        </template>
                        <template v-else>All Documentation Pages</template>
                    </h1>
                </div>
                <Link
                    :href="admin.docs.pages.create.url(category ? { query: { doc_category_id: category.id } } : {})"
                    class="text-white px-4 py-2 rounded-xl font-bold text-sm shadow-lg transition-all flex items-center gap-2 hover:opacity-90 disabled:opacity-70"
                    :style="{ backgroundColor: 'var(--brand-primary)', boxShadow: '0 10px 15px -3px var(--brand-primary-transparent, rgba(0,0,0,0.1))' }"
                >
                    <Plus :size="18" />
                    New Page
                </Link>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Project Filter -->
                    <div class="w-full md:w-48">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Project</label>
                        <SearchableSelect
                            v-model="selectedProject"
                            :options="projectOptions"
                            label-key="label"
                            value-key="id"
                            placeholder="All Projects"
                        />
                    </div>
                    <!-- Version Filter -->
                    <div class="w-full md:w-48">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Version</label>
                        <SearchableSelect
                            v-model="selectedVersion"
                            :options="versionOptions"
                            label-key="label"
                            value-key="id"
                            placeholder="All Versions"
                        />
                    </div>
                    <!-- Category Filter -->
                    <div class="w-full md:w-48">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Category</label>
                        <SearchableSelect
                            v-model="selectedCategory"
                            :options="categoryOptions"
                            label-key="label"
                            value-key="id"
                            placeholder="All Categories"
                        />
                    </div>
                    <!-- Search -->
                    <div class="flex-1 max-w-sm">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Search</label>
                        <div class="relative">
                            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Search by title or slug..."
                                class="w-full pl-10 pr-4 py-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 transition-all outline-none"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider uppercase">Title</th>
                            <th v-if="!category" class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider uppercase">Category</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider uppercase">Slug</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider uppercase">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider uppercase">Last Updated</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider uppercase text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="page in pages.data" :key="page.id" class="hover:bg-gray-50 dark:hover:bg-gray-900/40 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <GripVertical class="w-3.5 h-3.5 text-gray-300 mr-3 cursor-move" />
                                    <FileText class="w-4 h-4 text-gray-400 mr-3" />
                                    <span class="font-medium text-gray-900 dark:text-white">{{ page.title }}</span>
                                </div>
                            </td>
                            <td v-if="!category" class="px-6 py-4">
                                <span class="text-xs text-indigo-500 font-medium">
                                    {{ page.category?.version?.project?.name }} / {{ page.category?.version?.name }} / {{ page.category?.name }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <code class="text-xs text-gray-500">{{ page.slug }}</code>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="[getStatusClass(page.status), 'px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider']">
                                    {{ page.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ new Date(page.updated_at).toLocaleDateString() }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <Link
                                        :href="`/admin/docs/pages/${page.id}/edit`"
                                        class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 rounded-lg transition-colors"
                                        title="Edit Page"
                                    >
                                        <Edit class="w-4 h-4" />
                                    </Link>

                                    <a
                                        :href="'/docs/' + (page.category?.version?.project?.slug || 'undefined') + '/' + (page.category?.version?.slug || 'undefined') + '/' + (page.category?.slug || 'undefined') + '/' + page.slug"
                                        target="_blank"
                                        class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 rounded-lg transition-colors"
                                        title="View Publicly"
                                    >
                                        <Eye class="w-4 h-4" />
                                    </a>
                                    <button
                                        @click="deletePage(page.id)"
                                        class="p-2 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/30 text-gray-500 rounded-lg transition-colors"
                                        title="Delete Page"
                                    >
                                        <Trash2 class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!pages.data || pages.data.length === 0">
                            <td colspan="5" class="px-6 py-20 text-center text-gray-500">
                                <FileText class="w-10 h-10 mx-auto text-gray-300 mb-4 opacity-50" />
                                <p class="text-sm">No pages found in this category.</p>
                                <Link
                                    v-if="category"
                                    :href="admin.docs.pages.create.url({ query: { doc_category_id: category.id } })"
                                    class="text-indigo-600 hover:text-indigo-700 font-medium mt-2 inline-block"
                                >
                                    Create one now
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6" v-if="pages.links">
                <Pagination :links="pages.links" />
            </div>
        </div>

        <ConfirmationModal
            :show="showDeleteModal"
            title="Delete Page"
            message="Are you sure you want to delete this page? This action cannot be undone."
            confirm-text="Delete Page"
            :processing="isDeleting"
            @close="showDeleteModal = false"
            @confirm="confirmDelete"
        />
    </AdminLayout>
</template>
