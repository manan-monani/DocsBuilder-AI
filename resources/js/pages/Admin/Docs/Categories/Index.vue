<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Plus,
    ChevronLeft,
    Edit,
    Trash2,
    Folder,
    FileText,
    GripVertical,
    Search
} from 'lucide-vue-next';
import Pagination from '@/Components/Common/Pagination.vue';
import admin from '@/routes/admin';
import SearchableSelect from '@/Components/Common/SearchableSelect.vue';

const props = defineProps({
    version: Object,
    categories: Object,
    allProjects: Array,
    allVersions: Array,
    filters: Object,
});

import ConfirmationModal from '@/Components/Common/ConfirmationModal.vue';
import { ref, watch, computed } from 'vue';

const showDeleteModal = ref(false);
const categoryToDeleteId = ref(null);
const isDeleting = ref(false);

const deleteCategory = (id) => {
    categoryToDeleteId.value = id;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    if (!categoryToDeleteId.value) return;

    isDeleting.value = true;
    router.delete(admin.docs.categories.destroy.url(categoryToDeleteId.value), {
        preserveScroll: true,
        onFinish: () => {
            isDeleting.value = false;
            showDeleteModal.value = false;
            categoryToDeleteId.value = null;
        }
    });
};

const search = ref(props.filters?.search || '');
const selectedProject = ref(props.filters?.doc_project_id || null);
const selectedVersion = ref(props.filters?.doc_version_id || null);

// Options for filters
const projectOptions = props.allProjects?.map(p => ({ id: p.id, label: p.name })) || [];

// Filtered versions based on selected project
const versionOptions = computed(() => {
    const versions = props.allVersions || [];
    return versions.map(v => ({ id: v.id, label: v.name }));
});

// Apply filters
const applyFilters = () => {
    router.get(admin.docs.categories.index.url(), {
        search: search.value,
        doc_project_id: selectedProject.value,
        doc_version_id: selectedVersion.value
    }, {
        preserveState: true,
        replace: true,
    });
};

watch(search, applyFilters);
watch(selectedVersion, applyFilters);

// Reset version when project changes
watch(selectedProject, (value) => {
    if (value) {
        // Reset version selection when project changes
        selectedVersion.value = null;
    }
    applyFilters();
});
</script>

<template>
    <AdminLayout>
        <Head :title="version ? `Categories - ${version.name}` : 'All Categories'" />

        <div class="p-6">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <Link
                        v-if="version"
                        :href="admin.docs.versions.index.url({ query: { doc_project_id: version.doc_project_id } })"
                        class="inline-flex items-center text-sm text-gray-500 hover:text-indigo-600 transition-colors mb-2"
                    >
                        <ChevronLeft class="w-4 h-4 mr-1" />
                        Back to Versions
                    </Link>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        <template v-if="version">
                            {{ version?.project?.name }} <span class="text-gray-400 font-normal">/</span> {{ version?.name }} <span class="text-gray-400 font-normal ml-2">Categories</span>
                        </template>
                        <template v-else>All Documentation Categories</template>
                    </h1>
                </div>
                <Link
                    :href="admin.docs.categories.create.url(version ? { query: { doc_version_id: version.id } } : {})"
                    class="text-white px-4 py-2 rounded-xl font-bold text-sm shadow-lg transition-all flex items-center gap-2 hover:opacity-90 disabled:opacity-70"
                    :style="{ backgroundColor: 'var(--brand-primary)', boxShadow: '0 10px 15px -3px var(--brand-primary-transparent, rgba(0,0,0,0.1))' }"
                >
                    <Plus :size="18" />
                    New Category
                </Link>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Project Filter -->
                    <div class="w-full md:w-56">
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
                    <div class="w-full md:w-56">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Version</label>
                        <SearchableSelect
                            v-model="selectedVersion"
                            :options="versionOptions"
                            label-key="label"
                            value-key="id"
                            placeholder="All Versions"
                        />
                    </div>
                    <!-- Search -->
                    <div class="flex-1 max-w-md">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Search</label>
                        <div class="relative">
                            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Search by name or slug..."
                                class="w-full pl-10 pr-4 py-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 transition-all outline-none"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <div
                    v-for="category in categories.data"
                    :key="category.id"
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 flex items-center justify-between hover:border-indigo-300 dark:hover:border-indigo-900 transition-colors"
                >
                    <div class="flex items-center">
                        <GripVertical class="w-4 h-4 text-gray-300 mr-4 cursor-move" />
                        <div class="p-2 bg-gray-50 dark:bg-gray-900 rounded-lg text-gray-500 mr-4">
                            <component :is="Folder" class="w-5 h-5" />
                        </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">{{ category.name }}</h3>
                                <div class="flex items-center text-xs text-gray-400 mt-0.5">
                                    <template v-if="!version">
                                        <span class="text-indigo-500 font-medium">{{ category.version?.project?.name }} / {{ category.version?.name }}</span>
                                        <span class="mx-2">•</span>
                                    </template>
                                    <code>/{{ category.slug }}</code>
                                    <span class="mx-2">•</span>
                                    {{ category.pages_count }} Pages
                                </div>
                            </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <Link
                            :href="admin.docs.pages.index.url({ query: { doc_category_id: category.id } })"
                            class="inline-flex items-center px-3 py-1.5 bg-gray-50 hover:bg-gray-100 dark:bg-gray-900 dark:hover:bg-gray-800 text-gray-600 dark:text-gray-400 text-xs font-medium rounded-lg transition-colors"
                        >
                            <FileText class="w-3.5 h-3.5 mr-1.5 opacity-60" />
                            Manage Pages
                        </Link>
                        <Link
                            :href="admin.docs.categories.edit.url(category.id)"
                            class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 rounded-lg transition-colors"
                        >
                            <Edit class="w-4 h-4" />
                        </Link>
                        <button
                            @click="deleteCategory(category.id)"
                            class="p-2 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/30 text-gray-500 rounded-lg transition-colors"
                        >
                            <Trash2 class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                <div
                    v-if="!categories.data || categories.data.length === 0"
                    class="py-20 bg-gray-50 dark:bg-gray-900/40 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-800 flex flex-col items-center justify-center text-center px-6"
                >
                    <Folder class="w-10 h-10 text-gray-300 mb-4 opacity-50" />
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">No categories found</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-xs mx-auto mt-2">
                        Categories help organize your documentation pages into logical sections.
                    </p>
                    <Link
                        v-if="version"
                        :href="admin.docs.categories.create.url({ query: { doc_version_id: version.id } })"
                        class="mt-6 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors"
                    >
                        Create Category
                    </Link>
                </div>

                <!-- Pagination -->
                <div class="mt-6" v-if="categories.links">
                    <Pagination :links="categories.links" />
                </div>
            </div>
        </div>

        <ConfirmationModal
            :show="showDeleteModal"
            title="Delete Category"
            message="Are you sure you want to delete this category? All pages within it will be permanently deleted. This action cannot be undone."
            confirm-text="Delete Category"
            :processing="isDeleting"
            @close="showDeleteModal = false"
            @confirm="confirmDelete"
        />
    </AdminLayout>
</template>
