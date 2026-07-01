<script setup lang="ts">
import { Link, Head, usePage } from '@inertiajs/vue3';
import {
    Search,
    ChevronRight,
    Menu,
    X,
    Layout,
    Globe,
    ExternalLink,
    ChevronDown,
    BookOpen,
    FolderOpen,
    Folder,
    FileText
} from 'lucide-vue-next';
import { ref, onMounted, computed, nextTick } from 'vue';
import SearchInput from '@/Components/Public/Docs/Search.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';


const props = defineProps({
    project: Object,
    versions: Array,
    currentVersion: Object,
    sidebar: Array,
    branding: Object,
});

const isMobileMenuOpen = ref(false);
const isVersionDropdownOpen = ref(false);
const expandedCategories = ref({});
const sidebarContainer = ref(null);

const page = usePage();

// Initialize categories: expand only the active one, or the first one if none active
const initSidebar = () => {
    const page = usePage();
    const currentUrl = page.url;
    let hasActive = false;

    props.sidebar?.forEach((category, index) => {
        // Check if the current URL contains the category slug (indicates active category)
        // We use the category slug surrounded by slashes to avoid partial matches
        // e.g. /docs/project/version/category-slug/page-slug
        // Adding the slash ensures we don't match 'category-slug-2' with 'category-slug'
        if (currentUrl.includes(`/${category.slug}/`)) {
            expandedCategories.value[category.id] = true;
            hasActive = true;
        } else {
            expandedCategories.value[category.id] = false;
        }
    });

    // If no category is active (e.g. root docs page), expand the first one by default
    if (!hasActive && props.sidebar?.length > 0) {
        expandedCategories.value[props.sidebar[0].id] = true;
    }
};

// Run immediately
initSidebar();

// Scroll active item into view on mount
onMounted(() => {
    // Set direction based on locale
    const locale = (page.props as any).locale || 'en';
    const direction = locale === 'ar' ? 'rtl' : 'ltr';
    document.documentElement.setAttribute('dir', direction);
    localStorage.setItem('direction', direction);

    nextTick(() => {
        if (sidebarContainer.value) {
            // Find the active link using the active class (text-indigo-600)
            // This is a bit brittle but we control the classes above
            const activeLink = sidebarContainer.value.querySelector('.text-indigo-600.border-l-3');
            // Or better, look for the specific combination including bg-indigo-50 if classes change
            // The currently used active class includes 'bg-indigo-50'
            const activeItem = sidebarContainer.value.querySelector('.bg-indigo-50');

            if (activeItem) {
                activeItem.scrollIntoView({ block: 'center', behavior: 'smooth' });
            }
        }
    });
});

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const closeMobileMenu = () => {
    isMobileMenuOpen.value = false;
};

const toggleCategory = (categoryId) => {
    expandedCategories.value[categoryId] = !expandedCategories.value[categoryId];
};

const isCategoryExpanded = (categoryId) => {
    return expandedCategories.value[categoryId] ?? false;
};

// Smooth Collapse Animation Hooks
const enter = (element) => {
    element.style.height = '0';
    // Force repaint
    // eslint-disable-next-line no-unused-expressions
    element.offsetHeight;
    element.style.height = element.scrollHeight + 'px';
};

const afterEnter = (element) => {
    element.style.height = 'auto';
};

const leave = (element) => {
    element.style.height = element.scrollHeight + 'px';
    // Force repaint
    // eslint-disable-next-line no-unused-expressions
    element.offsetHeight;
    element.style.height = '0';
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans selection:bg-indigo-100 dark:selection:bg-indigo-900/40">
        <Head :title="project?.name" />

        <!-- Top Navigation -->
        <header class="sticky top-0 z-40 w-full backdrop-blur flex-none border-b border-slate-200 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 shadow-sm">
            <div class="w-full max-w-[1920px] mx-auto px-6 h-16 flex items-center justify-between">
                <!-- Left: Logo & Breadcrumbs -->
                <div class="flex items-center space-x-3 sm:space-x-5 flex-shrink-0">
                    <button @click="toggleMobileMenu" class="lg:hidden p-2 -ml-2 text-slate-500 hover:text-slate-600 dark:text-slate-400 dark:hover:text-slate-300">
                        <Menu v-if="!isMobileMenuOpen" class="w-6 h-6" />
                        <X v-else class="w-6 h-6" />
                    </button>

                    <Link href="/" class="flex items-center space-x-2 group shrink-0">
                        <div class="p-1.5 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-lg group-hover:scale-105 transition-transform shadow-lg shadow-indigo-500/30">
                            <BookOpen class="w-4 h-4 sm:w-5 sm:h-5 text-white" />
                        </div>
                        <span class="font-bold text-lg sm:text-xl tracking-tight text-slate-800 dark:text-white hidden sm:block">{{ $page.props.branding?.business_settings?.business_name || 'Docs' }}</span>
                    </Link>

                    <div class="hidden xl:flex items-center px-4 space-x-3 text-slate-300 dark:text-slate-600">
                        <span class="text-2xl font-light">/</span>
                        <Link :href="'/docs/' + project.slug" class="text-lg font-medium text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                            {{ project.name }}
                        </Link>
                    </div>
                </div>

                <!-- Center: Search Bar -->
                <div class="flex-1 flex justify-center px-2 sm:px-4 min-w-0">
                    <SearchInput :project-slug="project.slug" :version-slug="currentVersion.slug" class="w-full max-w-xl" />
                </div>

                <!-- Right: Version & Links -->
                <div class="flex items-center justify-end space-x-2 sm:space-x-4">
                     <!-- Theme & Language Toggles -->
                    <div class="hidden lg:flex items-center space-x-2">
                        <ThemeToggle />
                    </div>

                    <!-- Version Switcher -->
                    <div class="relative hidden lg:block">
                        <button
                            @click="isVersionDropdownOpen = !isVersionDropdownOpen"
                            class="flex items-center space-x-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-xl text-sm font-medium transition-colors"
                        >
                            <span class="text-slate-500 dark:text-slate-400">Version:</span>
                            <span class="text-indigo-600 dark:text-indigo-400 font-bold">{{ currentVersion?.name }}</span>
                            <ChevronDown class="w-4 h-4 text-slate-400 transition-transform" :class="{ 'rotate-180': isVersionDropdownOpen }" />
                        </button>

                        <div
                            v-if="isVersionDropdownOpen"
                            class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-2xl py-2 z-50"
                        >
                            <div class="px-4 py-2 text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-700 mb-1">
                                Available Versions
                            </div>
                            <Link
                                v-for="version in versions"
                                :key="version.id"
                                :href="'/docs/' + project.slug + '/' + version.slug + '/' + sidebar[0]?.slug + '/' + (sidebar[0]?.pages[0]?.slug || '')"
                                class="flex items-center px-4 py-3 text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                                :class="version.id === currentVersion?.id ? 'text-indigo-600 dark:text-indigo-400 font-bold bg-indigo-50 dark:bg-indigo-900/30' : 'text-slate-600 dark:text-slate-300'"
                                @click="isVersionDropdownOpen = false"
                            >
                                {{ version.name }}
                                <div class="ml-auto flex items-center space-x-2">
                                    <span v-if="version.is_archived" class="text-[10px] bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-400 px-2 py-0.5 rounded-full font-bold uppercase">Archived</span>
                                    <span v-if="version.is_default" class="text-[10px] bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 px-2 py-0.5 rounded-full font-bold uppercase">Default</span>
                                </div>
                            </Link>
                        </div>
                        <div v-if="isVersionDropdownOpen" @click="isVersionDropdownOpen = false" class="fixed inset-0 z-40"></div>
                    </div>

                    <div class="hidden lg:flex items-center space-x-4">
                        <a href="/" target="_blank" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors flex items-center px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                            Main Site
                            <ExternalLink class="w-4 h-4 ml-2 opacity-60" />
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <div class="flex max-w-[1920px] mx-auto">
            <!-- Left Sidebar Navigation -->
            <aside
                ref="sidebarContainer"
                class="fixed lg:sticky top-16 left-0 z-30 w-72 h-[calc(100vh-4rem)] overflow-y-auto bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 transition-transform duration-300 lg:translate-x-0 flex-shrink-0"
                :class="[isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0']"
            >
                <nav class="p-4 space-y-4">
                    <!-- Mobile Actions -->
                    <div class="lg:hidden flex items-center justify-between p-2 mb-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                        <div class="flex items-center space-x-2">
                            <ThemeToggle />
                        </div>
                        <a href="/" target="_blank" class="p-2 text-slate-500 hover:text-indigo-600 transition-colors">
                            <ExternalLink class="w-5 h-5" />
                        </a>
                    </div>
                    <!-- Mobile Version Switcher -->
                    <div class="md:hidden mb-6 pb-6 border-b border-slate-200 dark:border-slate-700">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Version</label>
                        <select
                            :value="currentVersion?.id"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-base p-3 focus:ring-2 focus:ring-indigo-500 outline-none"
                        >
                            <option v-for="v in versions" :key="v.id" :value="v.id">{{ v.name }}</option>
                        </select>
                    </div>

                    <!-- Categories with Pages -->
                    <div v-for="category in sidebar" :key="category.id" class="mb-2">
                        <!-- Category Header (Collapsible) -->
                        <button
                            @click="toggleCategory(category.id)"
                            class="w-full flex items-center justify-between px-4 py-3.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors group"
                        >
                            <div class="flex items-center space-x-3">
                                <component
                                    :is="isCategoryExpanded(category.id) ? FolderOpen : Folder"
                                    class="w-5 h-5 text-slate-400 group-hover:text-indigo-500 transition-colors"
                                />
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors text-left">
                                    {{ category.name }}
                                </span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="px-2.5 py-1 text-xs font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-full">
                                    {{ category.pages?.length || 0 }}
                                </span>
                                <ChevronDown
                                    class="w-4 h-4 text-slate-400 transition-transform duration-200"
                                    :class="{ 'rotate-180': !isCategoryExpanded(category.id) }"
                                />
                            </div>
                        </button>

                        <!-- Pages List (Collapsible) -->
                        <Transition
                            enter-active-class="transition-all duration-300 ease-in-out overflow-hidden"
                            leave-active-class="transition-all duration-300 ease-in-out overflow-hidden"
                            @enter="enter"
                            @after-enter="afterEnter"
                            @leave="leave"
                        >
                            <div
                                v-show="isCategoryExpanded(category.id)"
                                class="mt-1 ml-3 pl-3 border-l border-slate-200 dark:border-slate-800 space-y-1"
                            >
                                <Link
                                    v-for="page in category.pages"
                                    :key="page.id"
                                    :href="'/docs/' + project.slug + '/' + currentVersion.slug + '/' + category.slug + '/' + page.slug"
                                    class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all"
                                    :class="[
                                        $page.url.endsWith(`/${page.slug}`) || $page.url.includes(`/${page.slug}/`)
                                        ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30'
                                        : 'text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800'
                                    ]"
                                    @click="closeMobileMenu"
                                >
                                    <FileText class="w-3.5 h-3.5 mr-2 shrink-0 opacity-70 group-hover:opacity-100 transition-opacity" />
                                    {{ page.title }}
                                </Link>
                            </div>
                        </Transition>
                    </div>
                </nav>
            </aside>

            <!-- Main Content Area -->
            <main class="flex-1 min-w-0 bg-white dark:bg-slate-950 flex flex-col">

                <!-- We removed max-w restriction on container to allow it to fill available space,
                     but we'll add some padding in Show.vue and max-width on content for readability -->
                <slot />
            </main>
        </div>

        <!-- Mobile Backdrop -->
        <div v-if="isMobileMenuOpen" @click="closeMobileMenu" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-20 lg:hidden"></div>
    </div>
</template>

<style>
/* Smooth scrolling for TOC links */
html {
    scroll-behavior: smooth;
    scroll-padding-top: 5rem;
}

/* Custom scrollbar */
/* Custom scrollbar - Global */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: transparent;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1; /* slate-300 */
    border-radius: 4px;
    border: 2px solid transparent;
    background-clip: content-box;
}

.dark ::-webkit-scrollbar-thumb {
    background: #475569; /* slate-600 */
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8; /* slate-400 */
}

.dark ::-webkit-scrollbar-thumb:hover {
    background: #64748b; /* slate-500 */
}

/* Border width utility */
.border-l-3 {
    border-left-width: 3px;
}
</style>
