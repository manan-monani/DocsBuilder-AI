<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Plus,
    Search,
    MoreVertical,
    Edit,
    Trash2,
    Book,
    Layers,
    ExternalLink,
    ChevronRight
} from 'lucide-vue-next';
import { ref, watch } from 'vue';
import Pagination from '@/Components/Common/Pagination.vue';
import ConfirmationModal from '@/Components/Common/ConfirmationModal.vue';
import admin from '@/routes/admin';

const props = defineProps({
    projects: Object,
});

const search = ref('');

watch(search, (value) => {
    router.get(admin.docs.projects.index.url(), { search: value }, {
        preserveState: true,
        replace: true,
    });
});

const showDeleteModal = ref(false);
const projectToDeleteId = ref(null);
const isDeleting = ref(false);

const deleteProject = (id) => {
    projectToDeleteId.value = id;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    if (!projectToDeleteId.value) return;

    isDeleting.value = true;
    router.delete(admin.docs.projects.destroy.url(projectToDeleteId.value), {
        onFinish: () => {
            isDeleting.value = false;
            showDeleteModal.value = false;
            projectToDeleteId.value = null;
        }
    });
};
</script>

<template>
    <AdminLayout>
        <Head title="Documentation Projects" />

        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Documentation Projects</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage multiple documentation systems from a single place.</p>
                </div>
                <Link
                    :href="admin.docs.projects.create.url()"
                    class="text-white px-4 py-2 rounded-xl font-bold text-sm shadow-lg transition-all flex items-center gap-2 hover:opacity-90 disabled:opacity-70"
                    :style="{ backgroundColor: 'var(--brand-primary)', boxShadow: '0 10px 15px -3px var(--brand-primary-transparent, rgba(0,0,0,0.1))' }"
                >
                    <Plus :size="18" />
                    New Project
                </Link>
            </div>

            <!-- Search and Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
                <div class="relative max-w-md">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search projects by name or slug..."
                        class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 transition-all outline-none"
                    >
                </div>
            </div>

            <!-- Projects Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="project in projects.data"
                    :key="project.id"
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow group"
                >
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg text-indigo-600">
                                <img
                                    v-if="project.logo"
                                    :src="'/storage/' + project.logo"
                                    class="w-8 h-8 object-cover rounded"
                                    alt="Logo"
                                />
                                <Book v-else class="w-6 h-6" />
                            </div>
                            <div class="flex items-center space-x-2">
                                <span
                                    :class="[
                                        project.is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400',
                                        'px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider'
                                    ]"
                                >
                                    {{ project.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>

                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ project.name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mb-4 h-10">
                            {{ project.description || 'No description provided.' }}
                        </p>

                        <div class="flex items-center text-xs text-gray-400 dark:text-gray-500 space-x-4 mb-6">
                            <div class="flex items-center">
                                <Layers class="w-3 h-3 mr-1" />
                                {{ project.versions_count }} Versions
                            </div>
                            <div class="flex items-center" title="Default Version">
                                <span class="bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider mr-1">Default:</span>
                                {{ project.default_version?.name || 'Not Set' }}
                            </div>
                            <div class="flex items-center">
                                <ChevronRight class="w-3 h-3 mr-1" />
                                <code>/docs/{{ project.slug }}</code>
                            </div>
                        </div>

                        <div class="space-y-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <Link
                                :href="admin.docs.versions.index.url({ query: { doc_project_id: project.id } })"
                                class="flex items-center justify-center w-full px-4 py-2 bg-slate-100 hover:bg-indigo-600 hover:text-white dark:bg-slate-700 dark:hover:bg-indigo-600 text-slate-700 dark:text-slate-200 text-sm font-bold rounded-xl transition-all"
                            >
                                <Layers class="w-4 h-4 mr-2" />
                                Manage Versions
                            </Link>

                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center space-x-1">
                                    <Link
                                        :href="admin.docs.projects.edit.url(project.id)"
                                        class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg text-gray-500 transition-colors border border-transparent hover:border-gray-200"
                                        title="Edit Project"
                                    >
                                        <Edit class="w-4 h-4" />
                                    </Link>
                                    <button
                                        @click="deleteProject(project.id)"
                                        class="p-2 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/30 rounded-lg text-gray-500 transition-colors border border-transparent hover:border-red-200"
                                        title="Delete Project"
                                    >
                                        <Trash2 class="w-4 h-4" />
                                    </button>
                                </div>

                                <a
                                    :href="'/docs/' + project.slug"
                                    target="_blank"
                                    class="inline-flex items-center text-xs font-bold text-indigo-600 hover:text-indigo-700 uppercase tracking-wider"
                                >
                                    View Docs
                                    <ExternalLink class="w-3.5 h-3.5 ml-1.5" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-if="projects.data.length === 0"
                    class="col-span-full py-20 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-800 flex flex-col items-center justify-center text-center"
                >
                    <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-full mb-4">
                        <Book class="w-8 h-8 text-gray-400" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">No projects found</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-xs mx-auto mt-2">
                        Get started by creating your first documentation project.
                    </p>
                    <Link
                        :href="admin.docs.projects.create.url()"
                        class="mt-6 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors"
                    >
                        Create Project
                    </Link>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                <Pagination :links="projects.links" />
            </div>

            <ConfirmationModal
                :show="showDeleteModal"
                title="Delete Project?"
                message="Are you sure you want to delete this project? This action cannot be undone and will delete all associated versions and pages."
                confirm-text="Yes, Delete Project"
                cancel-text="Cancel"
                type="danger"
                :processing="isDeleting"
                @close="showDeleteModal = false"
                @confirm="confirmDelete"
            />
        </div>
    </AdminLayout>
</template>
