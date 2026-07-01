<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { ChevronDown, Check, Search, X } from 'lucide-vue-next';

const props = defineProps({
    modelValue: [String, Number],
    options: {
        type: Array,
        required: true,
        default: () => []
    },
    placeholder: {
        type: String,
        default: 'Select an option'
    },
    labelKey: {
        type: String,
        default: 'name'
    },
    valueKey: {
        type: String,
        default: 'id'
    },
    disabled: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const searchQuery = ref('');
const containerRef = ref(null);

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    if (containerRef.value && !containerRef.value.contains(event.target)) {
        isOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

// Filtered options based on search
const filteredOptions = computed(() => {
    if (!searchQuery.value) return props.options;
    const query = searchQuery.value.toLowerCase();
    return props.options.filter(option =>
        String(option[props.labelKey]).toLowerCase().includes(query)
    );
});

// Selected option object
const selectedOption = computed(() => {
    return props.options.find(option => option[props.valueKey] == props.modelValue);
});

const toggleDropdown = () => {
    if (props.disabled) return;
    isOpen.value = !isOpen.value;
    if (isOpen.value) {
        // Focus search input on open
        setTimeout(() => {
            const input = containerRef.value.querySelector('input');
            if (input) input.focus();
        }, 100);
    } else {
        searchQuery.value = '';
    }
};

const selectOption = (option) => {
    emit('update:modelValue', option[props.valueKey]);
    isOpen.value = false;
    searchQuery.value = '';
};

const clearSelection = (e) => {
    e.stopPropagation();
    emit('update:modelValue', null);
};
</script>

<template>
    <div ref="containerRef" class="relative w-full">
        <!-- Trigger -->
        <div
            @click="toggleDropdown"
            :class="[
                'w-full px-3 py-2 bg-white dark:bg-gray-900 border rounded-lg text-sm flex items-center justify-between cursor-pointer transition-colors',
                isOpen ? 'ring-2 ring-indigo-500 border-indigo-500' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300',
                disabled ? 'opacity-50 cursor-not-allowed bg-gray-50' : ''
            ]"
        >
            <span v-if="selectedOption" class="text-gray-900 dark:text-gray-100 truncate">
                <slot name="label" :option="selectedOption">
                    {{ selectedOption[labelKey] }}
                </slot>
            </span>
            <span v-else class="text-gray-400">
                {{ placeholder }}
            </span>

            <div class="flex items-center ml-2">
                <button
                    v-if="selectedOption && !disabled"
                    @click="clearSelection"
                    class="p-1 mr-1 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                >
                    <X class="w-3 h-3" />
                </button>
                <ChevronDown class="w-4 h-4 text-gray-400" />
            </div>
        </div>

        <!-- Dropdown Menu -->
        <div
            v-if="isOpen"
            class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg overflow-hidden animate-in fade-in zoom-in-95 duration-100"
        >
            <!-- Search Input -->
            <div class="p-2 border-b border-gray-100 dark:border-gray-700">
                <div class="relative">
                    <Search class="absolute left-2.5 top-2.5 w-4 h-4 text-gray-400" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        class="w-full pl-9 pr-3 py-2 bg-gray-50 dark:bg-gray-800 border-none rounded-md text-sm focus:ring-1 focus:ring-indigo-500 placeholder-gray-400 text-gray-900 dark:text-white"
                        placeholder="Search..."
                    >
                </div>
            </div>

            <!-- Options List -->
            <ul class="max-h-60 overflow-y-auto py-1">
                <li v-if="filteredOptions.length === 0" class="px-3 py-2 text-sm text-gray-400 text-center">
                    No results found
                </li>
                <li
                    v-for="option in filteredOptions"
                    :key="option[valueKey]"
                    @click="selectOption(option)"
                    :class="[
                        'px-3 py-2 text-sm cursor-pointer flex items-center justify-between',
                        modelValue == option[valueKey]
                            ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400'
                            : 'text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'
                    ]"
                >
                    <slot name="option" :option="option">
                        <span>{{ option[labelKey] }}</span>
                    </slot>
                    <Check v-if="modelValue == option[valueKey]" class="w-4 h-4" />
                </li>
            </ul>
        </div>
    </div>
</template>
