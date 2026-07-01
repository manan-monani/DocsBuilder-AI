<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Plus,
    ChevronLeft,
    MoreVertical,
    Edit,
    Trash2,
    Layers,
    Star,
    Archive,
    Search,
    ChevronRight,
    Folder
} from 'lucide-vue-next';
import { ref, watch } from 'vue';
import Pagination from '@/Components/Common/Pagination.vue';
import admin from '@/routes/admin';
import SearchableSelect from '@/Components/Common/SearchableSelect.vue';

const props = defineProps({
    project: Object,
    versions: Object,
    allProjects: Array,
    filters: Object,
});

import ConfirmationModal from '@/Components/Common/ConfirmationModal.vue';

// ... existing code ...

const showDeleteModal = ref(false);
const versionToDeleteId = ref(null);
const isDeleting = ref(false);

const deleteVersion = (id) => {
    versionToDeleteId.value = id;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    if (!versionToDeleteId.value) return;

    isDeleting.value = true;
    router.delete(admin.docs.versions.destroy.url(versionToDeleteId.value), {
        preserveScroll: true,
        onFinish: () => {
            isDeleting.value = false;
            showDeleteModal.value = false;
            versionToDeleteId.value = null;
        }
    });
};

const search = ref(props.filters?.search || '');
const selectedProject = ref(props.filters?.doc_project_id || null);

// Project options for filter
const projectOptions = props.allProjects?.map(p => ({ id: p.id, label: p.name })) || [];

watch(search, (value) => {
    router.get(admin.docs.versions.index.url(), {
        search: value,
        doc_project_id: selectedProject.value
    }, {
        preserveState: true,
        replace: true,
    });
});

watch(selectedProject, (value) => {
    router.get(admin.docs.versions.index.url(), {
        search: search.value,
        doc_project_id: value
    }, {
        preserveState: true,
        replace: true,
    });
});
</script>

<template>
    <AdminLayout>
        <Head :title="project ? `Versions - ${project.name}` : 'All Versions'" />

        <div class="p-6">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <Link
                        :href="admin.docs.projects.index.url()"
                        class="inline-flex items-center text-sm text-gray-500 hover:text-indigo-600 transition-colors mb-2"
                    >
                        <ChevronLeft class="w-4 h-4 mr-1" />
                        Back to Projects
                    </Link>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        <template v-if="project">{{ project.name }} <span class="text-gray-400 font-normal ml-2">Versions</span></template>
                        <template v-else>All Documentation Versions</template>
                    </h1>
                </div>
                <Link
                    :href="admin.docs.versions.create.url(project ? { query: { doc_project_id: project.id } } : {})"
                    class="text-white px-4 py-2 rounded-xl font-bold text-sm shadow-lg transition-all flex items-center gap-2 hover:opacity-90 disabled:opacity-70"
                    :style="{ backgroundColor: 'var(--brand-primary)', boxShadow: '0 10px 15px -3px var(--brand-primary-transparent, rgba(0,0,0,0.1))' }"
                >
                    <Plus :size="18" />
                    New Version
                </Link>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Project Filter -->
                    <div class="w-full md:w-64">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Project</label>
                        <SearchableSelect
                            v-model="selectedProject"
                            :options="projectOptions"
                            label-key="label"
                            value-key="id"
                            placeholder="All Projects"
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

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider uppercase">Name</th>
                            <th v-if="!project" class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider uppercase">Project</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider uppercase">Slug</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider uppercase">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider uppercase text-center">Categories</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider uppercase text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="version in versions.data" :key="version.id" class="hover:bg-gray-50 dark:hover:bg-gray-900/40 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <Layers class="w-4 h-4 text-gray-400 mr-3" />
                                    <span class="font-medium text-gray-900 dark:text-white">{{ version.name }}</span>
                                    <Star v-if="version.is_default" class="w-3 h-3 text-yellow-400 ml-2 fill-yellow-400" />
                                </div>
                            </td>
                            <td v-if="!project" class="px-6 py-4">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ version.project?.name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <code class="text-xs text-gray-500 px-1.5 py-0.5 bg-gray-100 dark:bg-gray-900 rounded">{{ version.slug }}</code>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <span v-if="version.is_default" class="px-2 py-0.5 bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 text-[10px] font-bold uppercase rounded">Default</span>
                                    <span v-if="version.is_archived" class="px-2 py-0.5 bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400 text-[10px] font-bold uppercase rounded">Archived</span>
                                    <span v-if="!version.is_archived && !version.is_default" class="px-2 py-0.5 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 text-[10px] font-bold uppercase rounded">Active</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2 py-1 bg-gray-50 dark:bg-gray-900 text-xs text-gray-600 dark:text-gray-400 rounded-lg">
                                    <Folder class="w-3 h-3 mr-1.5 opacity-50" />
                                    {{ version.categories_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-3">
                                    <Link
                                        :href="admin.docs.categories.index.url({ query: { doc_version_id: version.id } })"
                                        class="inline-flex items-center px-3 py-1.5 bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white dark:bg-indigo-900/20 dark:text-indigo-400 dark:hover:text-white text-xs font-bold rounded-lg transition-all"
                                    >
                                        <Folder class="w-3.5 h-3.5 mr-2" />
                                        Manage Categories
                                    </Link>
                                    <div class="flex items-center space-x-1 border-l border-gray-100 dark:border-gray-700 pl-3">
                                        <Link
                                            :href="admin.docs.versions.edit.url(version.id)"
                                            class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 rounded-lg transition-colors"
                                            title="Edit Version"
                                        >
                                            <Edit class="w-4 h-4" />
                                        </Link>
                                        <button
                                            @click="deleteVersion(version.id)"
                                            class="p-2 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/30 text-gray-500 rounded-lg transition-colors"
                                            title="Delete Version"
                                        >
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!versions.data || versions.data.length === 0">
                            <td colspan="5" class="px-6 py-20 text-center text-gray-500">
                                <Layers class="w-10 h-10 mx-auto text-gray-300 mb-4 opacity-50" />
                                <p class="text-sm">No versions found{{ project ? ' for this project' : '' }}.</p>
                                <Link
                                    v-if="project"
                                    :href="admin.docs.versions.create.url({ query: { doc_project_id: project.id } })"
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
            <div class="mt-6" v-if="versions.links">
                <Pagination :links="versions.links" />
            </div>
        </div>

        <ConfirmationModal
            :show="showDeleteModal"
            title="Delete Version"
            message="Are you sure you want to delete this version? All categories and pages within it will be permanently deleted. This action cannot be undone."
            confirm-text="Delete Version"
            :processing="isDeleting"
            @close="showDeleteModal = false"
            @confirm="confirmDelete"
        />
    </AdminLayout>
</template>
