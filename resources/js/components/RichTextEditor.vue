<script setup lang="ts">
import {
    Bold,
    Heading2,
    Heading3,
    Italic,
    List,
    ListOrdered,
    Quote,
    Redo2,
    Strikethrough,
    Undo2,
} from '@lucide/vue';
import StarterKit from '@tiptap/starter-kit';
import { Editor, EditorContent } from '@tiptap/vue-3';
import { onBeforeUnmount, shallowRef, watch } from 'vue';

const props = defineProps<{
    modelValue: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const editor = shallowRef<Editor>();

watch(
    () => props.modelValue,
    (value) => {
        if (editor.value && editor.value.getHTML() !== value) {
            editor.value.commands.setContent(value, { emitUpdate: false });
        }
    },
);

editor.value = new Editor({
    extensions: [StarterKit],
    content: props.modelValue,
    editorProps: {
        attributes: {
            class: 'min-h-48 px-3 py-2 text-sm outline-none',
        },
    },
    onUpdate: ({ editor }) => {
        emit('update:modelValue', editor.getHTML());
    },
});

onBeforeUnmount(() => {
    editor.value?.destroy();
});

function runCommand(action: () => void) {
    action();
    editor.value?.commands.focus();
}

const buttonClass = (active: boolean) =>
    [
        'inline-flex h-8 w-8 items-center justify-center rounded-md text-sm',
        active
            ? 'bg-muted text-foreground'
            : 'text-muted-foreground hover:bg-muted/60 hover:text-foreground',
    ].join(' ');
</script>

<template>
    <div
        class="border-input bg-background overflow-hidden rounded-md border"
    >
        <div
            v-if="editor"
            class="border-input flex flex-wrap items-center gap-0.5 border-b bg-muted/40 p-1.5"
        >
            <button
                type="button"
                title="Tebal"
                :class="buttonClass(editor.isActive('bold'))"
                @click="runCommand(() => editor!.chain().toggleBold().run())"
            >
                <Bold class="h-4 w-4" />
            </button>
            <button
                type="button"
                title="Miring"
                :class="buttonClass(editor.isActive('italic'))"
                @click="runCommand(() => editor!.chain().toggleItalic().run())"
            >
                <Italic class="h-4 w-4" />
            </button>
            <button
                type="button"
                title="Coret"
                :class="buttonClass(editor.isActive('strike'))"
                @click="runCommand(() => editor!.chain().toggleStrike().run())"
            >
                <Strikethrough class="h-4 w-4" />
            </button>
            <button
                type="button"
                title="Judul besar"
                :class="buttonClass(editor.isActive('heading', { level: 2 }))"
                @click="
                    runCommand(() =>
                        editor!.chain().toggleHeading({ level: 2 }).run(),
                    )
                "
            >
                <Heading2 class="h-4 w-4" />
            </button>
            <button
                type="button"
                title="Judul kecil"
                :class="buttonClass(editor.isActive('heading', { level: 3 }))"
                @click="
                    runCommand(() =>
                        editor!.chain().toggleHeading({ level: 3 }).run(),
                    )
                "
            >
                <Heading3 class="h-4 w-4" />
            </button>
            <button
                type="button"
                title="Daftar poin"
                :class="buttonClass(editor.isActive('bulletList'))"
                @click="
                    runCommand(() => editor!.chain().toggleBulletList().run())
                "
            >
                <List class="h-4 w-4" />
            </button>
            <button
                type="button"
                title="Daftar bernomor"
                :class="buttonClass(editor.isActive('orderedList'))"
                @click="
                    runCommand(() => editor!.chain().toggleOrderedList().run())
                "
            >
                <ListOrdered class="h-4 w-4" />
            </button>
            <button
                type="button"
                title="Kutipan"
                :class="buttonClass(editor.isActive('blockquote'))"
                @click="
                    runCommand(() => editor!.chain().toggleBlockquote().run())
                "
            >
                <Quote class="h-4 w-4" />
            </button>
            <button
                type="button"
                title="Urungkan"
                :class="buttonClass(false)"
                @click="runCommand(() => editor!.chain().undo().run())"
            >
                <Undo2 class="h-4 w-4" />
            </button>
            <button
                type="button"
                title="Ulangi"
                :class="buttonClass(false)"
                @click="runCommand(() => editor!.chain().redo().run())"
            >
                <Redo2 class="h-4 w-4" />
            </button>
        </div>

        <EditorContent
            :editor="editor"
            class="[&_h2]:mt-4 [&_h2]:text-lg [&_h2]:font-semibold [&_h3]:mt-3 [&_h3]:font-semibold [&_ol]:list-decimal [&_ol]:pl-6 [&_p]:my-2 [&_ul]:list-disc [&_ul]:pl-6 [&_blockquote]:border-l-2 [&_blockquote]:pl-3 [&_blockquote]:text-muted-foreground [&_blockquote]:italic"
        />
    </div>
</template>
