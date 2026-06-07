<script setup lang="ts">
import { StarterKit } from '@tiptap/starter-kit';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import { watch } from 'vue';

const props = defineProps<{ modelValue: string }>();
const emit = defineEmits<{ 'update:modelValue': [value: string] }>();

const editor = useEditor({
    content: props.modelValue,
    extensions: [StarterKit],
    editorProps: {
        attributes: {
            class: 'prose prose-sm max-w-none min-h-[200px] focus:outline-none p-3',
        },
    },
    onUpdate({ editor: e }) {
        emit('update:modelValue', e.getHTML());
    },
});

watch(
    () => props.modelValue,
    (value) => {
        if (editor.value && editor.value.getHTML() !== value) {
            editor.value.commands.setContent(value, { emitUpdate: false });
        }
    },
);
</script>

<template>
    <div class="rounded-md border border-input bg-background">
        <div v-if="editor" class="flex flex-wrap gap-1 border-b border-input p-2">
            <button
                type="button"
                class="rounded px-2 py-1 text-xs font-medium hover:bg-accent"
                :class="{ 'bg-accent': editor.isActive('bold') }"
                @click="editor.chain().focus().toggleBold().run()"
            >
                B
            </button>
            <button
                type="button"
                class="rounded px-2 py-1 text-xs italic hover:bg-accent"
                :class="{ 'bg-accent': editor.isActive('italic') }"
                @click="editor.chain().focus().toggleItalic().run()"
            >
                I
            </button>
            <button
                type="button"
                class="rounded px-2 py-1 text-xs font-medium hover:bg-accent"
                :class="{ 'bg-accent': editor.isActive('heading', { level: 2 }) }"
                @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
            >
                H2
            </button>
            <button
                type="button"
                class="rounded px-2 py-1 text-xs font-medium hover:bg-accent"
                :class="{ 'bg-accent': editor.isActive('heading', { level: 3 }) }"
                @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
            >
                H3
            </button>
            <button
                type="button"
                class="rounded px-2 py-1 text-xs hover:bg-accent"
                :class="{ 'bg-accent': editor.isActive('bulletList') }"
                @click="editor.chain().focus().toggleBulletList().run()"
            >
                •—
            </button>
            <button
                type="button"
                class="rounded px-2 py-1 text-xs hover:bg-accent"
                :class="{ 'bg-accent': editor.isActive('orderedList') }"
                @click="editor.chain().focus().toggleOrderedList().run()"
            >
                1.
            </button>
            <button
                type="button"
                class="rounded px-2 py-1 text-xs hover:bg-accent"
                :class="{ 'bg-accent': editor.isActive('blockquote') }"
                @click="editor.chain().focus().toggleBlockquote().run()"
            >
                ❝
            </button>
        </div>
        <EditorContent :editor="editor" />
    </div>
</template>
