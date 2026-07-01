<script setup lang="ts">
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import TextAlign from '@tiptap/extension-text-align';
import Underline from '@tiptap/extension-underline';
import ExtendedImage from './extensions/ExtendedImage';
import Link from '@tiptap/extension-link';
import { watch, ref } from 'vue';
import {
    Bold,
    Italic,
    Underline as UnderlineIcon,
    List,
    ListOrdered,
    Heading1,
    Heading2,
    Heading3,
    AlignLeft,
    AlignCenter,
    AlignRight,
    Undo,
    Redo,
    Image as ImageIcon,
    Trash2,
    Link as LinkIcon,
    Type,
    AlignHorizontalJustifyStart,
    AlignHorizontalJustifyEnd,
    X
} from 'lucide-vue-next';

const props = defineProps<{
    modelValue: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

// Image toolbar state
const showImageToolbar = ref(false);
const imageToolbarPosition = ref({ top: 0, left: 0 });
const selectedImageElement = ref<HTMLImageElement | null>(null);
const showAltTextModal = ref(false);
const showLinkModal = ref(false);
const showCaptionModal = ref(false);
const altTextInput = ref('');
const linkInput = ref('');
const captionInput = ref('');

const editor = useEditor({
    content: props.modelValue,
    extensions: [
        StarterKit,
        Underline,
        ExtendedImage.configure({
            inline: true,
            allowBase64: true,
        }),
        Link.configure({
            openOnClick: false,
        }),
        TextAlign.configure({
            types: ['heading', 'paragraph'],
        }),
    ],
    editorProps: {
        attributes: {
            class: 'prose prose-sm sm:prose lg:prose-lg xl:prose-xl max-w-none focus:outline-none min-h-[400px] p-5',
        },
        handleClick: (view, pos, event) => {
            const target = event.target as HTMLElement;
            if (target.tagName === 'IMG') {
                const rect = target.getBoundingClientRect();
                const editorRect = view.dom.getBoundingClientRect();
                imageToolbarPosition.value = {
                    top: rect.bottom - editorRect.top + 10,
                    left: rect.left - editorRect.left + (rect.width / 2),
                };
                selectedImageElement.value = target as HTMLImageElement;
                showImageToolbar.value = true;

                // Get current values
                altTextInput.value = target.getAttribute('alt') || '';
                const parentLink = target.closest('a');
                linkInput.value = parentLink?.getAttribute('href') || '';
                captionInput.value = target.getAttribute('data-caption') || '';
            } else {
                showImageToolbar.value = false;
            }
            return false;
        },
    },
    onUpdate: ({ editor }) => {
        emit('update:modelValue', editor.getHTML());
    },
});

watch(() => props.modelValue, (value) => {
    const isSame = editor.value?.getHTML() === value;
    if (!isSame && editor.value) {
        editor.value.commands.setContent(value, { emitUpdate: false });
    }
});

const addImage = () => {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';
    input.onchange = async () => {
        if (input.files?.length) {
            const file = input.files[0];
            const formData = new FormData();
            formData.append('image', file);

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const tokenContent = csrfToken ? csrfToken.getAttribute('content') : '';

            try {
                const response = await fetch('/admin/docs/upload-image', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': tokenContent || '',
                    },
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.url && editor.value) {
                        editor.value.chain().focus().setImage({ src: data.url }).run();
                    }
                } else {
                    alert('Image upload failed');
                }
            } catch (error) {
                alert('Error uploading image');
            }
        }
    };
    input.click();
};

// Helper to get current style and preserve width
const getCurrentWidth = (): string => {
    if (selectedImageElement.value) {
        const currentStyle = selectedImageElement.value.getAttribute('style') || '';
        const widthMatch = currentStyle.match(/width:\s*[^;]+/);
        return widthMatch ? widthMatch[0] + ';' : '';
    }
    return '';
};

// Image control functions - using TipTap's updateAttributes for persistence
const setImageAlignment = (alignment: 'left' | 'center' | 'right') => {
    if (!editor.value || !selectedImageElement.value) return;

    const width = getCurrentWidth();
    let style = '';

    if (alignment === 'left') {
        style = `display: block; float: none; margin-left: 0; margin-right: auto; ${width}`;
    } else if (alignment === 'right') {
        style = `display: block; float: none; margin-left: auto; margin-right: 0; ${width}`;
    } else {
        style = `display: block; float: none; margin-left: auto; margin-right: auto; ${width}`;
    }

    // Update via TipTap for persistence
    editor.value.chain().focus().updateAttributes('image', { style: style.trim() }).run();

    // Also update DOM for immediate feedback
    selectedImageElement.value.setAttribute('style', style);
};

const setImageFloat = (float: 'left' | 'right' | 'none') => {
    if (!editor.value || !selectedImageElement.value) return;

    const width = getCurrentWidth();
    let style = '';

    if (float === 'left') {
        style = `float: left; margin: 0 1rem 0.5rem 0; ${width}`;
    } else if (float === 'right') {
        style = `float: right; margin: 0 0 0.5rem 1rem; ${width}`;
    } else {
        style = `display: block; float: none; margin-left: auto; margin-right: auto; ${width}`;
    }

    // Update via TipTap for persistence
    editor.value.chain().focus().updateAttributes('image', { style: style.trim() }).run();

    // Also update DOM for immediate feedback
    selectedImageElement.value.setAttribute('style', style);
};

const deleteImage = () => {
    if (editor.value) {
        editor.value.chain().focus().deleteSelection().run();
        showImageToolbar.value = false;
    }
};

const saveAltText = () => {
    if (editor.value) {
        editor.value.chain().focus().updateAttributes('image', {
            alt: altTextInput.value,
            title: altTextInput.value
        }).run();
        showAltTextModal.value = false;
    }
};

const saveLink = () => {
    if (selectedImageElement.value && linkInput.value) {
        let wrapper = selectedImageElement.value.closest('a');
        if (!wrapper) {
            wrapper = document.createElement('a');
            selectedImageElement.value.parentNode?.insertBefore(wrapper, selectedImageElement.value);
            wrapper.appendChild(selectedImageElement.value);
        }
        wrapper.setAttribute('href', linkInput.value);
        wrapper.setAttribute('target', '_blank');
        showLinkModal.value = false;

        // Trigger update
        if (editor.value) {
            emit('update:modelValue', editor.value.getHTML());
        }
    } else if (selectedImageElement.value && !linkInput.value) {
        const wrapper = selectedImageElement.value.closest('a');
        if (wrapper) {
            wrapper.parentNode?.insertBefore(selectedImageElement.value, wrapper);
            wrapper.remove();
        }
        showLinkModal.value = false;

        if (editor.value) {
            emit('update:modelValue', editor.value.getHTML());
        }
    }
};

const saveCaption = () => {
    if (editor.value) {
        editor.value.chain().focus().updateAttributes('image', {
            'data-caption': captionInput.value
        }).run();
        showCaptionModal.value = false;
    }
};

const closeToolbar = () => {
    showImageToolbar.value = false;
};
</script>

<template>
    <div v-if="editor" class="border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden bg-white dark:bg-slate-800 relative">
        <!-- Toolbar -->
        <div class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 p-2 flex flex-wrap gap-1">
            <!-- Text Formatting -->
            <button
                @click="editor.chain().focus().toggleBold().run()"
                :class="{ 'bg-brand-100 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400': editor.isActive('bold') }"
                class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                type="button"
                title="Bold"
            >
                <Bold :size="18" />
            </button>
            <button
                @click="editor.chain().focus().toggleItalic().run()"
                :class="{ 'bg-brand-100 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400': editor.isActive('italic') }"
                class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                type="button"
                title="Italic"
            >
                <Italic :size="18" />
            </button>
            <button
                @click="editor.chain().focus().toggleUnderline().run()"
                :class="{ 'bg-brand-100 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400': editor.isActive('underline') }"
                class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                type="button"
                title="Underline"
            >
                <UnderlineIcon :size="18" />
            </button>

            <div class="w-px h-6 bg-slate-200 dark:bg-slate-700 mx-1"></div>

            <!-- Headings -->
            <button
                @click="editor.chain().focus().toggleHeading({ level: 1 }).run()"
                :class="{ 'bg-brand-100 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400': editor.isActive('heading', { level: 1 }) }"
                class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                type="button"
                title="Heading 1"
            >
                <Heading1 :size="18" />
            </button>
            <button
                @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
                :class="{ 'bg-brand-100 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400': editor.isActive('heading', { level: 2 }) }"
                class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                type="button"
                title="Heading 2"
            >
                <Heading2 :size="18" />
            </button>
            <button
                @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
                :class="{ 'bg-brand-100 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400': editor.isActive('heading', { level: 3 }) }"
                class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                type="button"
                title="Heading 3"
            >
                <Heading3 :size="18" />
            </button>

            <div class="w-px h-6 bg-slate-200 dark:bg-slate-700 mx-1"></div>

            <!-- Lists -->
            <button
                @click="editor.chain().focus().toggleBulletList().run()"
                :class="{ 'bg-brand-100 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400': editor.isActive('bulletList') }"
                class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                type="button"
                title="Bullet List"
            >
                <List :size="18" />
            </button>
            <button
                @click="editor.chain().focus().toggleOrderedList().run()"
                :class="{ 'bg-brand-100 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400': editor.isActive('orderedList') }"
                class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                type="button"
                title="Numbered List"
            >
                <ListOrdered :size="18" />
            </button>

            <div class="w-px h-6 bg-slate-200 dark:bg-slate-700 mx-1"></div>

            <!-- Alignment -->
            <button
                @click="editor.chain().focus().setTextAlign('left').run()"
                :class="{ 'bg-brand-100 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400': editor.isActive({ textAlign: 'left' }) }"
                class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                type="button"
                title="Align Left"
            >
                <AlignLeft :size="18" />
            </button>
            <button
                @click="editor.chain().focus().setTextAlign('center').run()"
                :class="{ 'bg-brand-100 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400': editor.isActive({ textAlign: 'center' }) }"
                class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                type="button"
                title="Align Center"
            >
                <AlignCenter :size="18" />
            </button>
            <button
                @click="editor.chain().focus().setTextAlign('right').run()"
                :class="{ 'bg-brand-100 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400': editor.isActive({ textAlign: 'right' }) }"
                class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                type="button"
                title="Align Right"
            >
                <AlignRight :size="18" />
            </button>

            <div class="w-px h-6 bg-slate-200 dark:bg-slate-700 mx-1"></div>

            <!-- Undo/Redo -->
            <button
                @click="editor.chain().focus().undo().run()"
                :disabled="!editor.can().undo()"
                class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                type="button"
                title="Undo"
            >
                <Undo :size="18" />
            </button>
            <button
                @click="editor.chain().focus().redo().run()"
                :disabled="!editor.can().redo()"
                class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                type="button"
                title="Redo"
            >
                <Redo :size="18" />
            </button>

            <div class="w-px h-6 bg-slate-200 dark:bg-slate-700 mx-1"></div>

            <button
                @click="addImage"
                class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                type="button"
                title="Insert Image"
            >
                <ImageIcon :size="18" />
            </button>
        </div>

        <!-- Editor Content Container -->
        <div class="relative">
            <EditorContent :editor="editor" class="bg-white dark:bg-slate-800" />

            <!-- Image Floating Toolbar -->
            <Transition
                enter-active-class="transition ease-out duration-150"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
            >
                <div
                    v-if="showImageToolbar"
                    class="absolute z-50 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl p-2"
                    :style="{ top: `${imageToolbarPosition.top}px`, left: `${imageToolbarPosition.left}px`, transform: 'translateX(-50%)' }"
                >
                    <!-- Close button -->
                    <button
                        @click="closeToolbar"
                        class="absolute -top-2 -right-2 w-5 h-5 bg-slate-500 hover:bg-slate-600 text-white rounded-full flex items-center justify-center"
                        type="button"
                    >
                        <X :size="12" />
                    </button>

                    <div class="flex flex-col gap-2">
                        <!-- Alignment Row -->
                        <div class="flex items-center gap-1 pb-2 border-b border-slate-200 dark:border-slate-700">
                            <span class="text-xs text-slate-400 px-1">Align:</span>
                            <button @click="setImageAlignment('left')" class="p-1.5 rounded hover:bg-slate-100 dark:hover:bg-slate-700" title="Align Left" type="button">
                                <AlignLeft :size="16" />
                            </button>
                            <button @click="setImageAlignment('center')" class="p-1.5 rounded hover:bg-slate-100 dark:hover:bg-slate-700" title="Align Center" type="button">
                                <AlignCenter :size="16" />
                            </button>
                            <button @click="setImageAlignment('right')" class="p-1.5 rounded hover:bg-slate-100 dark:hover:bg-slate-700" title="Align Right" type="button">
                                <AlignRight :size="16" />
                            </button>
                        </div>

                        <!-- Float Row -->
                        <div class="flex items-center gap-1 pb-2 border-b border-slate-200 dark:border-slate-700">
                            <span class="text-xs text-slate-400 px-1">Float:</span>
                            <button @click="setImageFloat('left')" class="p-1.5 rounded hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300" title="Float Left" type="button">
                                <AlignHorizontalJustifyStart :size="16" />
                            </button>
                            <button @click="setImageFloat('none')" class="p-1.5 rounded hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300" title="No Float" type="button">
                                <AlignCenter :size="16" />
                            </button>
                            <button @click="setImageFloat('right')" class="p-1.5 rounded hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300" title="Float Right" type="button">
                                <AlignHorizontalJustifyEnd :size="16" />
                            </button>
                        </div>

                        <!-- Actions Row -->
                        <div class="flex items-center gap-1">
                            <button @click="showAltTextModal = true" class="p-1.5 rounded hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300" title="Alt Text" type="button">
                                <Type :size="16" />
                            </button>
                            <button @click="showCaptionModal = true" class="p-1.5 rounded hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300" title="Caption" type="button">
                                <span class="text-xs font-medium">Cc</span>
                            </button>
                            <button @click="showLinkModal = true" class="p-1.5 rounded hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300" title="Add Link" type="button">
                                <LinkIcon :size="16" />
                            </button>
                            <button @click="deleteImage" class="p-1.5 rounded hover:bg-red-50 dark:hover:bg-red-900/30 text-red-500" title="Delete" type="button">
                                <Trash2 :size="16" />
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </div>

        <!-- Alt Text Modal -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showAltTextModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50" @click.self="showAltTextModal = false">
                    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl p-6 w-96">
                        <h3 class="text-lg font-semibold mb-4 dark:text-white">Alt Text (for SEO)</h3>
                        <input
                            v-model="altTextInput"
                            type="text"
                            class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white"
                            placeholder="Describe this image..."
                        />
                        <div class="flex justify-end gap-2 mt-4">
                            <button @click="showAltTextModal = false" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg" type="button">Cancel</button>
                            <button @click="saveAltText" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700" type="button">Save</button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- Link Modal -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showLinkModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50" @click.self="showLinkModal = false">
                    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl p-6 w-96">
                        <h3 class="text-lg font-semibold mb-4 dark:text-white">Image Link</h3>
                        <input
                            v-model="linkInput"
                            type="url"
                            class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white"
                            placeholder="https://example.com"
                        />
                        <p class="text-xs text-slate-500 mt-1">Leave empty to remove link</p>
                        <div class="flex justify-end gap-2 mt-4">
                            <button @click="showLinkModal = false" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg" type="button">Cancel</button>
                            <button @click="saveLink" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700" type="button">Save</button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- Caption Modal -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showCaptionModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50" @click.self="showCaptionModal = false">
                    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl p-6 w-96">
                        <h3 class="text-lg font-semibold mb-4 dark:text-white">Image Caption</h3>
                        <input
                            v-model="captionInput"
                            type="text"
                            class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white"
                            placeholder="Enter caption..."
                        />
                        <div class="flex justify-end gap-2 mt-4">
                            <button @click="showCaptionModal = false" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg" type="button">Cancel</button>
                            <button @click="saveCaption" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700" type="button">Save</button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<style>
/* TipTap Editor Styles */
.ProseMirror {
    outline: none;
}

.ProseMirror p.is-editor-empty:first-child::before {
    content: attr(data-placeholder);
    float: left;
    color: #adb5bd;
    pointer-events: none;
    height: 0;
}

.ProseMirror h1 {
    font-size: 2em;
    font-weight: bold;
    margin-top: 0.67em;
    margin-bottom: 0.67em;
}

.ProseMirror h2 {
    font-size: 1.5em;
    font-weight: bold;
    margin-top: 0.83em;
    margin-bottom: 0.83em;
}

.ProseMirror h3 {
    font-size: 1.17em;
    font-weight: bold;
    margin-top: 1em;
    margin-bottom: 1em;
}

.ProseMirror ul,
.ProseMirror ol {
    padding-left: 1.5rem;
    margin: 1rem 0;
}

.ProseMirror ul {
    list-style-type: disc;
}

.ProseMirror ol {
    list-style-type: decimal;
}

.ProseMirror li {
    margin: 0.25rem 0;
}

.ProseMirror a {
    color: #3b82f6;
    text-decoration: underline;
    cursor: pointer;
}

.ProseMirror a:hover {
    color: #2563eb;
}

.ProseMirror strong {
    font-weight: bold;
}

.ProseMirror em {
    font-style: italic;
}

.ProseMirror u {
    text-decoration: underline;
}

.ProseMirror p {
    margin: 0.5rem 0;
}

/* Image Styles */
.ProseMirror img {
    max-width: 100%;
    height: auto;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: box-shadow 0.2s;
}

.ProseMirror img:hover {
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3);
}

.ProseMirror img.ProseMirror-selectednode {
    outline: 3px solid #6366f1;
    outline-offset: 2px;
}

/* Image Caption */
.ProseMirror .image-caption {
    display: block;
    text-align: center;
    font-size: 0.875rem;
    color: #6b7280;
    margin-top: 0.5rem;
    font-style: italic;
}

/* Resize handle styles */
.ProseMirror .image-resizer {
    display: inline-flex;
    position: relative;
    flex-grow: 0;
}

.ProseMirror .image-resizer .resize-trigger {
    position: absolute;
    right: -6px;
    bottom: -6px;
    width: 12px;
    height: 12px;
    background: #6366f1;
    border: 2px solid white;
    border-radius: 50%;
    cursor: se-resize;
    box-shadow: 0 1px 3px rgba(0,0,0,0.3);
}

/* Clear floats */
.ProseMirror::after {
    content: "";
    display: table;
    clear: both;
}
</style>
