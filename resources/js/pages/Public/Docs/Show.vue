<script setup>
import DocsLayout from '@/Layouts/DocsLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    Clock,
    User,
    ChevronRight,
    Anchor,
    Search,
    MessageSquare,
    Link2,
    ExternalLink,
    ArrowLeft,
    ArrowRight,
    List,
    AlertTriangle
} from 'lucide-vue-next';
import { onMounted, ref, computed, watch } from 'vue';

const props = defineProps({
    project: Object,
    versions: Array,
    currentVersion: Object,
    sidebar: Array,
    page: Object,
    toc: Array,
    branding: Object,
});

const activeTOC = ref(null);

// Flatten the sidebar structure to a linear list of pages with category context
const flatPages = computed(() => {
    if (!props.sidebar) return [];
    return props.sidebar.flatMap(category =>
        (category.pages || []).map(page => ({
            ...page,
            categorySlug: category.slug
        }))
    );
});

// Find current page index
const currentPageIndex = computed(() => {
    return flatPages.value.findIndex(p => p.slug === props.page.slug);
});

// Previous Page
const prevPage = computed(() => {
    if (currentPageIndex.value > 0) {
        return flatPages.value[currentPageIndex.value - 1];
    }
    return null;
});

// Next Page
const nextPage = computed(() => {
    if (currentPageIndex.value !== -1 && currentPageIndex.value < flatPages.value.length - 1) {
        return flatPages.value[currentPageIndex.value + 1];
    }
    return null;
});

// Helper to generate Page URL
const pageUrl = (page) => {
    return `/docs/${props.project.slug}/${props.currentVersion.slug}/${page.categorySlug}/${page.slug}`;
};

// Scroll to top on page change
import { router } from '@inertiajs/vue3';

onMounted(() => {
    // Basic Intersection Observer for TOC highlighting
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                activeTOC.value = entry.target.id;
            }
        });
    }, { rootMargin: '-10% 0% -80% 0%' });

    document.querySelectorAll('h2[id], h3[id]').forEach(h => observer.observe(h));

    // Ensure scroll to top on navigation
    router.on('success', () => {
        window.scrollTo({ top: 0, left: 0, behavior: 'instant' });
    });
});

// Format date
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const copyLink = (id) => {
    const url = window.location.href.split('#')[0] + (id ? '#' + id : '');
    navigator.clipboard.writeText(url);
};
</script>

<template>
    <DocsLayout
        :project="project"
        :versions="versions"
        :currentVersion="currentVersion"
        :sidebar="sidebar"
        :branding="branding"
    >
        <Head>
            <title>{{ page.title }} - {{ project.name }} Docs</title>
            <meta name="description" :content="page.meta_description || project.description" />
        </Head>

        <!-- Main Flex Container -->
        <div class="flex relative">
            <!-- Main Article Content -->
            <article class="flex-1 min-w-0 py-6 px-4 sm:px-8 lg:px-12 lg:py-14 bg-white dark:bg-slate-950">
                <div class="max-w-4xl mx-auto xl:mr-12 2xl:mr-24"> <!-- Aligned left-center for readability -->
                    <!-- Breadcrumbs -->
                    <nav class="flex items-center space-x-3 text-sm font-medium text-slate-400 mb-10">
                        <Link :href="'/docs/' + project.slug" class="hover:text-indigo-600 transition-colors uppercase tracking-wide">Docs</Link>
                        <ChevronRight class="w-4 h-4" />
                        <span class="text-slate-600 dark:text-slate-300 uppercase tracking-wide">{{ page.category?.name }}</span>
                    </nav>

                    <!-- Archived Version Warning -->
                    <div v-if="currentVersion?.is_archived" class="mb-10 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl px-6 py-4 flex items-center justify-center text-center">
                        <div class="flex items-center text-sm font-medium text-amber-800 dark:text-amber-200">
                            <AlertTriangle class="w-5 h-5 mr-3 flex-shrink-0 text-amber-600 dark:text-amber-400" />
                            <span>This version is archived and no longer maintained.</span>
                            <Link :href="`/docs/${project.slug}`" class="ml-2 underline hover:text-amber-900 dark:hover:text-amber-100 font-bold">
                                Go to latest version
                            </Link>
                        </div>
                    </div>

                    <header class="mb-10 lg:mb-14 pb-8 border-b border-slate-100 dark:border-slate-800">
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-6 leading-[1.15]">
                            {{ page.title }}
                        </h1>

                        <div class="flex flex-wrap items-center gap-x-6 gap-y-3 text-sm sm:text-base text-slate-500 dark:text-slate-400">
                            <div class="flex items-center">
                                <User class="w-4 h-4 sm:w-5 sm:h-5 mr-2 opacity-60" />
                                <span class="font-medium text-slate-700 dark:text-slate-300">Documentation Team</span>
                            </div>
                            <div class="text-slate-300 dark:text-slate-600 hidden sm:block">•</div>
                            <div class="flex items-center">
                                <Clock class="w-4 h-4 sm:w-5 sm:h-5 mr-2 opacity-60" />
                                <span>Updated {{ formatDate(page.updated_at) }}</span>
                            </div>
                        </div>
                    </header>

                    <!-- Mobile TOC -->
                    <div class="block xl:hidden mb-12">
                        <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-5 flex items-center">
                                <List class="w-4 h-4 mr-2" />
                                On this page
                            </h3>
                            <nav class="space-y-3">
                                <a
                                    v-for="item in toc"
                                    :key="item.id"
                                    :href="`#${item.id}`"
                                    class="block text-base transition-colors"
                                    :class="[
                                        activeTOC === item.id ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200',
                                        item.level === 3 ? 'ml-5' : ''
                                    ]"
                                >
                                    {{ item.text }}
                                </a>
                            </nav>
                        </div>
                    </div>

                    <!-- Page Content -->
                    <div
                        class="documentation-content"
                        v-html="page.content_html"
                    ></div>

                <!-- Page Navigation -->
                <div class="mt-16 pt-8 border-t border-slate-100 dark:border-slate-800 grid grid-cols-1 md:grid-cols-2 gap-6" v-if="prevPage || nextPage">
                    <!-- Previous Page -->
                    <Link
                        v-if="prevPage"
                        :href="pageUrl(prevPage)"
                        class="group flex flex-col p-5 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-indigo-300 dark:hover:border-indigo-700 hover:bg-slate-50 dark:hover:bg-slate-900 transition-all text-left"
                    >
                        <div class="flex items-center text-sm font-medium text-slate-500 dark:text-slate-400 mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                            <ArrowLeft class="w-4 h-4 mr-1.5 transition-transform group-hover:-translate-x-1" />
                            Previous
                        </div>
                        <div class="text-base font-bold text-slate-900 dark:text-white group-hover:text-indigo-700 dark:group-hover:text-indigo-300 transition-colors">
                            {{ prevPage.title }}
                        </div>
                    </Link>
                    <div v-else></div> <!-- Spacer -->

                    <!-- Next Page -->
                    <Link
                        v-if="nextPage"
                        :href="pageUrl(nextPage)"
                        class="group flex flex-col items-end p-5 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-indigo-300 dark:hover:border-indigo-700 hover:bg-slate-50 dark:hover:bg-slate-900 transition-all text-right"
                    >
                        <div class="flex items-center text-sm font-medium text-slate-500 dark:text-slate-400 mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                            Next
                            <ArrowRight class="w-4 h-4 ml-1.5 transition-transform group-hover:translate-x-1" />
                        </div>
                        <div class="text-base font-bold text-slate-900 dark:text-white group-hover:text-indigo-700 dark:group-hover:text-indigo-300 transition-colors">
                            {{ nextPage.title }}
                        </div>
                    </Link>
                </div>

                <!-- Footer -->
                <footer class="mt-12 pt-10 border-t border-slate-100 dark:border-slate-800">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="text-base text-slate-500">
                            © {{ new Date().getFullYear() }} {{ branding?.business_name }}. All rights reserved.
                        </div>
                        <div class="flex items-center space-x-6">
                            <button @click="copyLink('')" class="text-base font-medium text-slate-500 hover:text-indigo-600 transition-colors flex items-center group">
                                <Link2 class="w-5 h-5 mr-2 opacity-60 group-hover:opacity-100 transition-opacity" />
                                Copy link
                            </button>
                        </div>
                    </div>
                </footer>
            </div>
        </article>

            <!-- Desktop Right Sidebar (TOC) -->
            <aside class="hidden xl:block w-[320px] flex-shrink-0 border-l border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950">
                <div class="sticky top-28 px-8 py-10 overflow-y-auto max-h-[calc(100vh-7rem)]">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-6 flex items-center">
                        <List class="w-4 h-4 mr-2" />
                        On this page
                    </h3>
                    <nav class="space-y-4 relative">
                        <!-- Active Indicator Line -->
                        <div class="absolute left-0 top-0 bottom-0 w-[0px] bg-indigo-500 hidden"></div>

                        <a
                            v-for="item in toc"
                            :key="item.id"
                            :href="`#${item.id}`"
                            class="block text-[15px] leading-snug transition-all duration-200"
                            :class="[
                                activeTOC === item.id
                                    ? 'text-indigo-600 dark:text-indigo-400 font-semibold'
                                    : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200',
                                item.level === 3 ? 'ml-4 text-sm' : ''
                            ]"
                        >
                            {{ item.text }}
                        </a>
                    </nav>

                    <!-- Need Help Card -->
                    <div class="mt-16 pt-8 border-t border-slate-100 dark:border-slate-800">
                        <div class="bg-indigo-50/80 dark:bg-indigo-900/10 rounded-2xl p-6 border border-indigo-100 dark:border-indigo-900/30">
                            <h4 class="text-base font-bold text-indigo-900 dark:text-indigo-100 mb-3 flex items-center">
                                <MessageSquare class="w-5 h-5 mr-2.5 opacity-80" />
                                Need help?
                            </h4>
                            <p class="text-sm text-indigo-700 dark:text-indigo-300 mb-4 leading-relaxed opacity-90">
                                Can't find what you're looking for? Reach out to our support team.
                            </p>
                            <a href="/contact-us" class="inline-flex items-center text-sm font-bold text-indigo-700 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-200 transition-colors">
                                Contact Support
                                <ExternalLink class="w-3.5 h-3.5 ml-1.5" />
                            </a>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </DocsLayout>
</template>

<style>
@reference "tailwindcss";

/* Documentation Content Styles - Spacious and Readable */
.documentation-content {
    font-size: 1.125rem; /* text-lg */
    line-height: 2; /* leading-8 */
    color: #0f172a; /* text-slate-900 */
}

.dark .documentation-content {
    color: #f1f5f9; /* dark:text-slate-100 */
}

.documentation-content h1,
.documentation-content h2,
.documentation-content h3,
.documentation-content h4,
.documentation-content h5,
.documentation-content h6 {
    color: #020617; /* text-slate-950 (blackish) */
    font-weight: 700;
    margin-top: 3rem;
    margin-bottom: 1.5rem;
    scroll-margin-top: 8rem;
    letter-spacing: -0.025em;
}

.dark .documentation-content h1,
.dark .documentation-content h2,
.dark .documentation-content h3,
.dark .documentation-content h4,
.dark .documentation-content h5,
.dark .documentation-content h6 {
    color: #ffffff;
}

.documentation-content p {
    margin-bottom: 2rem;
    color: #0f172a; /* text-slate-900 */
}

.dark .documentation-content p {
    color: #f1f5f9; /* dark:text-slate-100 */
}

.documentation-content a:not(.no-style) {
    @apply font-medium text-indigo-700 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 underline underline-offset-4 decoration-indigo-300/50 dark:decoration-indigo-700/50 hover:decoration-indigo-500 transition-all;
}

.documentation-content code:not(pre code) {
    @apply px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 rounded-md text-[0.9em] font-mono border border-slate-200/50 dark:border-slate-700/50;
    color: #0f172a;
}

.dark .documentation-content code:not(pre code) {
    color: #f1f5f9;
}

.documentation-content pre {
    @apply bg-slate-900 dark:bg-slate-950 rounded-2xl p-6 mb-10 overflow-x-auto border border-slate-800 shadow-xl ring-1 ring-white/10;
}

.documentation-content pre code {
    @apply font-mono text-base bg-transparent p-0 border-none;
    color: #e2e8f0; /* text-slate-200 */
    line-height: 1.7;
}

.dark .documentation-content pre code {
    color: #cbd5e1; /* text-slate-300 */
}

.documentation-content ul {
    @apply list-disc list-outside ml-6 mb-6 marker:text-indigo-600 dark:marker:text-indigo-400;
}

.documentation-content ol {
    @apply list-decimal list-outside ml-6 mb-6 marker:text-indigo-600 dark:marker:text-indigo-400 font-medium;
}

.documentation-content li {
    padding-left: 0.5rem;
    margin-bottom: 0.5rem;
    color: #0f172a;
}

.documentation-content li > p {
    margin-bottom: 0;
    margin-top: 0;
}

.dark .documentation-content li {
    color: #f1f5f9;
}

.documentation-content blockquote {
    @apply border-l-4 border-indigo-600 pl-8 my-10 italic bg-slate-50 dark:bg-slate-900/30 py-6 pr-6 rounded-r-2xl;
    color: #1e293b; /* slate-800 */
}

.dark .documentation-content blockquote {
    color: #e2e8f0; /* slate-200 */
}

.documentation-content img {
    @apply rounded-2xl shadow-lg border border-slate-200 dark:border-slate-800;
    max-width: 100%;
    height: auto;
}

/* Images with inline styles - preserve them */
.documentation-content img[style*="float"] {
    margin: 0.5rem;
}

/* Clear floats after paragraphs containing floated images */
.documentation-content p::after,
.documentation-content::after {
    content: "";
    display: table;
    clear: both;
}

/* Image captions */
.documentation-content .image-caption,
.documentation-content em.image-caption {
    display: block;
    text-align: center;
    font-size: 0.875rem;
    color: #6b7280;
    margin-top: 0.5rem;
    margin-bottom: 1.5rem;
    font-style: italic;
}

.documentation-content table {
    @apply w-full border-collapse my-10 text-base rounded-lg overflow-hidden ring-1 ring-slate-200 dark:ring-slate-800;
    display: block;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.documentation-content thead {
    @apply bg-slate-100 dark:bg-slate-900;
}

.documentation-content th {
    @apply border-b border-slate-200 dark:border-slate-800 px-6 py-4 text-left uppercase tracking-wider text-sm;
    color: #020617;
    font-weight: 700;
}

.dark .documentation-content th {
    color: #ffffff;
}

.documentation-content td {
    @apply border-b border-slate-100 dark:border-slate-800 px-6 py-4 bg-white dark:bg-slate-950;
    color: #0f172a;
}

.dark .documentation-content td {
    color: #f1f5f9;
}

.documentation-content tr:last-child td {
    @apply border-b-0;
}

.documentation-content tr:hover td {
    @apply bg-slate-50 dark:bg-slate-900/50;
}

.documentation-content hr {
    @apply my-16 border-t border-slate-100 dark:border-slate-800;
}

/* Ensure content doesn't overflow */
.documentation-content {
    @apply break-words;
    overflow-wrap: break-word;
    word-wrap: break-word;
}

</style>
