<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight } from 'lucide-vue-next';

const props = defineProps({
    links: Array,
});

// Compute visible page links with smart truncation
const visibleLinks = computed(() => {
    if (!props.links || props.links.length <= 3) return [];

    // Get only page number links (exclude prev/next)
    const pageLinks = props.links.slice(1, -1);
    const totalPages = pageLinks.length;

    if (totalPages <= 7) {
        return pageLinks;
    }

    // Find active page index
    const activeIndex = pageLinks.findIndex(link => link.active);
    const result = [];

    // Always show first page
    result.push(pageLinks[0]);

    // Calculate visible range around active page
    let start = Math.max(1, activeIndex - 1);
    let end = Math.min(totalPages - 2, activeIndex + 1);

    // Adjust range for edge cases
    if (activeIndex <= 2) {
        start = 1;
        end = Math.min(4, totalPages - 2);
    } else if (activeIndex >= totalPages - 3) {
        start = Math.max(1, totalPages - 5);
        end = totalPages - 2;
    }

    // Add ellipsis before middle section if needed
    if (start > 1) {
        result.push({ label: '...', url: null, isEllipsis: true });
    }

    // Add middle pages
    for (let i = start; i <= end; i++) {
        result.push(pageLinks[i]);
    }

    // Add ellipsis after middle section if needed
    if (end < totalPages - 2) {
        result.push({ label: '...', url: null, isEllipsis: true });
    }

    // Always show last page
    if (totalPages > 1) {
        result.push(pageLinks[totalPages - 1]);
    }

    return result;
});

// Get prev and next links
const prevLink = computed(() => props.links?.[0] || null);
const nextLink = computed(() => props.links?.[props.links.length - 1] || null);

// Get current and total page info
const paginationInfo = computed(() => {
    if (!props.links || props.links.length <= 3) return null;

    const pageLinks = props.links.slice(1, -1);
    const activeLink = pageLinks.find(link => link.active);
    const currentPage = activeLink ? pageLinks.indexOf(activeLink) + 1 : 1;
    const totalPages = pageLinks.length;

    return { currentPage, totalPages };
});
</script>

<template>
    <div v-if="links && links.length > 3" class="flex flex-col sm:flex-row items-center justify-between gap-4 px-4 py-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
        <!-- Page Info -->
        <div class="text-sm text-gray-500 dark:text-gray-400">
            Page <span class="font-semibold text-gray-900 dark:text-white">{{ paginationInfo?.currentPage }}</span>
            of <span class="font-semibold text-gray-900 dark:text-white">{{ paginationInfo?.totalPages }}</span>
        </div>

        <!-- Pagination Controls -->
        <nav class="flex items-center gap-1" aria-label="Pagination">
            <!-- Previous Button -->
            <Link
                :href="prevLink?.url || '#'"
                :class="[
                    'inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium transition-all',
                    prevLink?.url
                        ? 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-indigo-600 dark:hover:text-indigo-400'
                        : 'text-gray-300 dark:text-gray-600 cursor-not-allowed'
                ]"
                :aria-disabled="!prevLink?.url"
                title="Previous"
            >
                <ChevronLeft class="w-4 h-4" />
            </Link>

            <!-- Page Numbers -->
            <div class="flex items-center gap-1">
                <template v-for="(link, index) in visibleLinks" :key="index">
                    <!-- Ellipsis -->
                    <span
                        v-if="link.isEllipsis"
                        class="inline-flex items-center justify-center w-9 h-9 text-gray-400 dark:text-gray-500 text-sm"
                    >
                        •••
                    </span>

                    <!-- Page Number -->
                    <Link
                        v-else
                        :href="link.url || '#'"
                        :class="[
                            'inline-flex items-center justify-center min-w-[36px] h-9 px-3 rounded-lg text-sm font-medium transition-all',
                            link.active
                                ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-500/30'
                                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700',
                            !link.url && !link.active ? 'opacity-50 cursor-not-allowed' : ''
                        ]"
                        v-html="link.label"
                    />
                </template>
            </div>

            <!-- Next Button -->
            <Link
                :href="nextLink?.url || '#'"
                :class="[
                    'inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium transition-all',
                    nextLink?.url
                        ? 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-indigo-600 dark:hover:text-indigo-400'
                        : 'text-gray-300 dark:text-gray-600 cursor-not-allowed'
                ]"
                :aria-disabled="!nextLink?.url"
                title="Next"
            >
                <ChevronRight class="w-4 h-4" />
            </Link>
        </nav>
    </div>
</template>
