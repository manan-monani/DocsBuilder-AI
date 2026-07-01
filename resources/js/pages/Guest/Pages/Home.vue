<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, usePage, Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Search, ArrowRight, Book, ExternalLink, ArrowUpRight } from 'lucide-vue-next';

const page = usePage();

const props = defineProps<{
    projects: Array<{
        id: number;
        slug: string;
        name: string;
        description: string;
        logo: string;
        default_version: { slug: string } | null;
    }>
}>();

const getLogoUrl = (logo: string) => {
    if (!logo) return null;
    return logo.startsWith('http') ? logo : '/storage/' + logo;
};
</script>

<template>
    <Head title="Landing Page" />
    <PublicLayout :fluid="true">
        <div class="bg-gray-50 dark:bg-gray-900 min-h-screen font-sans text-gray-900 dark:text-gray-100 transition-colors duration-300">

            <!-- Hero Section -->
            <section class="relative pt-24 pb-6 overflow-hidden bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
                    <div class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-xs font-semibold mb-6 uppercase tracking-wider">
                        Documentation Hub
                    </div>
                    <h1 class="text-4xl md:text-6xl font-bold tracking-tight mb-6 leading-tight text-gray-900 dark:text-white">
                        Explore our <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">Documentation</span>
                    </h1>
                    <p class="max-w-2xl mx-auto text-lg text-gray-500 dark:text-gray-400 mb-10 leading-relaxed">
                        Everything you need to build, integrate, and scale with our platform. Browse guides, API references, and tutorials.
                    </p>
                </div>

                <!-- Background Decorations -->
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full -z-10 pointer-events-none overflow-hidden">
                    <div class="absolute top-0 left-1/4 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl mix-blend-multiply dark:mix-blend-screen"></div>
                    <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl mix-blend-multiply dark:mix-blend-screen"></div>
                </div>
            </section>

            <!-- Projects Grid -->
            <section class="pt-6 pb-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div v-if="props.projects.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <Link
                            v-for="project in props.projects"
                            :key="project.id"
                            :href="`/docs/${project.slug}`"
                            class="group relative bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-300 hover:-translate-y-1 block h-full flex flex-col"
                        >
                            <div class="absolute inset-0 bg-gradient-to-br from-transparent to-indigo-50/50 dark:to-indigo-900/10 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>

                            <div class="relative flex items-start justify-between mb-4">
                                <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                    <img v-if="project.logo" :src="getLogoUrl(project.logo)" class="w-8 h-8 object-contain" :alt="project.name">
                                    <Book v-else class="w-6 h-6" />
                                </div>
                                <span class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs px-2 py-1 rounded-md font-medium">
                                    v{{ project.default_version?.slug || '1.0' }}
                                </span>
                            </div>

                            <h3 class="relative text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                {{ project.name }}
                            </h3>

                            <p class="relative text-gray-500 dark:text-gray-400 mb-6 line-clamp-2 min-h-[3rem] flex-grow">
                                {{ project.description || 'Comprehensive documentation and guides.' }}
                            </p>

                            <div class="relative mt-auto">
                                <span
                                    class="inline-flex items-center text-sm font-semibold text-indigo-600 dark:text-indigo-400 group-hover:text-indigo-700 dark:group-hover:text-indigo-300 group-hover:underline"
                                >
                                    View Documentation
                                    <ArrowRight class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" />
                                </span>
                            </div>
                        </Link>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="text-center py-20">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                            <Book class="w-8 h-8 text-gray-400" />
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No projects found</h3>
                        <p class="text-gray-500 dark:text-gray-400">
                            There are currently no documentation projects available.
                        </p>
                    </div>
                </div>
            </section>
        </div>
    </PublicLayout>
</template>
