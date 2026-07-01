<script setup>
import { ref, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Search as SearchIcon, FileText, Folder, Loader2 } from 'lucide-vue-next';
import axios from 'axios';
import { useDebounceFn } from '@vueuse/core';
import { search } from '@/routes/docs';

const props = defineProps({
    projectSlug: String,
    versionSlug: String,
});

const query = ref('');
const results = ref({ categories: [], pages: [] });
const isLoading = ref(false);
const showDropdown = ref(false);
const searchInput = ref(null);

const performSearch = useDebounceFn(async (q) => {
    if (!q || q.length < 2) {
        results.value = { categories: [], pages: [] };
        return;
    }

    isLoading.value = true;
    try {
        const url = search.url(
            { projectSlug: props.projectSlug, versionSlug: props.versionSlug },
            { query: { query: q } }
        );

        const response = await axios.get(url);
        results.value = response.data;
        showDropdown.value = true;
    } catch (error) {
        console.error('Search failed:', error);
    } finally {
        isLoading.value = false;
    }
}, 300);

watch(query, (newQuery) => {
    if (!newQuery) {
        showDropdown.value = false;
        results.value = { categories: [], pages: [] };
    } else {
        performSearch(newQuery);
    }
});

const closeDropdown = () => {
    setTimeout(() => {
        showDropdown.value = false;
    }, 200);
};

const focusSearch = () => {
    if (query.value && (results.value.categories.length > 0 || results.value.pages.length > 0)) {
        showDropdown.value = true;
    }
};

const highlightMatch = (text, query) => {
    if (!query || !text) return text;
    // Escape special regex characters
    const escapedQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const regex = new RegExp(`(${escapedQuery})`, 'gi');
    return text.replace(regex, '<span class="text-indigo-600 dark:text-indigo-400 font-bold">$1</span>');
};
</script>

<template>
    <div class="relative w-full max-w-lg">
        <div class="relative group">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <SearchIcon class="h-5 w-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors" />
            </div>
            <input
                ref="searchInput"
                v-model="query"
                type="text"
                class="block w-full pl-10 pr-3 py-2 border border-slate-200 dark:border-slate-700 rounded-xl leading-5 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all shadow-sm"
                placeholder="Search documentation..."
                @focus="focusSearch"
                @blur="closeDropdown"
            />
            <div v-if="isLoading" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <Loader2 class="h-4 w-4 text-indigo-600 animate-spin" />
            </div>
        </div>

        <!-- Dropdown Results -->
        <Transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
            <div
                v-if="showDropdown && (results.categories.length > 0 || results.pages.length > 0)"
                class="absolute z-50 mt-2 w-full bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden max-h-[80vh] overflow-y-auto"
            >
                <!-- Categories Section -->
                <div v-if="results.categories.length > 0" class="py-2">
                    <div class="px-4 py-1 text-xs font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50 dark:bg-slate-700/50">
                        Categories
                    </div>
                    <ul>
                        <li v-for="category in results.categories" :key="category.url">
                            <Link
                                :href="category.url"
                                class="flex items-center px-4 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-700/80 transition-colors group"
                            >
                                <Folder class="h-4 w-4 text-slate-400 mr-3 group-hover:text-indigo-500 transition-colors" />
                                <span
                                    class="text-sm font-medium text-slate-700 dark:text-slate-200 group-hover:text-slate-900 dark:group-hover:text-white"
                                    v-html="highlightMatch(category.title, query)"
                                ></span>
                            </Link>
                        </li>
                    </ul>
                </div>

                <div v-if="results.categories.length > 0 && results.pages.length > 0" class="border-t border-slate-100 dark:border-slate-700"></div>

                <!-- Pages Section -->
                <div v-if="results.pages.length > 0" class="py-2">
                    <div class="px-4 py-1 text-xs font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50 dark:bg-slate-700/50">
                        Pages
                    </div>
                    <ul>
                        <li v-for="page in results.pages" :key="page.url">
                            <Link
                                :href="page.url"
                                class="flex items-start px-4 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-700/80 transition-colors group"
                            >
                                <FileText class="h-4 w-4 text-slate-400 mr-3 mt-0.5 group-hover:text-indigo-500 transition-colors" />
                                <div>
                                    <div
                                        class="text-sm font-medium text-slate-700 dark:text-slate-200 group-hover:text-slate-900 dark:group-hover:text-white"
                                        v-html="highlightMatch(page.title, query)"
                                    ></div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        in {{ page.category }}
                                    </div>
                                </div>
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>
        </Transition>
    </div>
</template>
