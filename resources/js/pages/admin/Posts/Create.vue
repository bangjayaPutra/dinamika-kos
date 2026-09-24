<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import PostController from '@/actions/App/Http/Controllers/Admin/PostController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import RichTextEditor from '@/components/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { create, index } from '@/routes/admin/posts';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Berita',
                href: index(),
            },
            {
                title: 'Tambah',
                href: create(),
            },
        ],
    },
});

const coverError = ref('');
const bodyHtml = ref('');

function onCoverSelected(event: Event) {
    coverError.value = '';

    const file = (event.target as HTMLInputElement).files?.[0];

    if (file && file.size > 10 * 1024 * 1024) {
        coverError.value = `"${file.name}" melebihi 10 MB.`;
    }
}
</script>

<template>
    <Head title="Tambah Berita" />

    <div class="flex max-w-2xl flex-col space-y-6">
        <Heading
            variant="small"
            title="Tambah Berita"
            description="Isi berita atau pengumuman baru"
        />

        <Form
            v-bind="PostController.store.form()"
            class="space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="title">Judul</Label>
                <Input
                    id="title"
                    name="title"
                    class="mt-1 block w-full"
                    required
                    placeholder="Pengumuman libur bersama"
                />
                <InputError class="mt-2" :message="errors.title" />
            </div>

            <div class="grid gap-2">
                <Label for="excerpt">Ringkasan</Label>
                <textarea
                    id="excerpt"
                    name="excerpt"
                    rows="2"
                    placeholder="Satu-dua kalimat pembuka berita"
                    class="border-input bg-background mt-1 block w-full rounded-md border px-3 py-2 text-sm"
                />
                <InputError class="mt-2" :message="errors.excerpt" />
            </div>

            <div class="grid gap-2">
                <Label for="body">Isi</Label>
                <RichTextEditor v-model="bodyHtml" />
                <input
                    type="hidden"
                    name="body"
                    :value="bodyHtml === '<p></p>' ? '' : bodyHtml"
                />
                <InputError class="mt-2" :message="errors.body" />
            </div>

            <div class="grid gap-2">
                <Label for="cover_path">Gambar sampul</Label>
                <Input
                    id="cover_path"
                    name="cover_path"
                    type="file"
                    accept=".jpg,.jpeg,.png"
                    class="mt-1 block w-full"
                    @change="onCoverSelected"
                />
                <p
                    v-if="coverError"
                    class="text-destructive text-sm font-medium"
                >
                    {{ coverError }}
                </p>
                <InputError class="mt-2" :message="errors.cover_path" />
            </div>

            <div class="flex items-center gap-2">
                <input
                    id="is_published"
                    name="is_published"
                    type="checkbox"
                    value="1"
                    class="border-input h-4 w-4 rounded"
                />
                <Label for="is_published">Terbitkan langsung</Label>
                <InputError class="mt-2" :message="errors.is_published" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing || !!coverError">Simpan</Button>
                <Button variant="outline" as-child>
                    <Link :href="index().url">Batal</Link>
                </Button>
            </div>
        </Form>
    </div>
</template>
