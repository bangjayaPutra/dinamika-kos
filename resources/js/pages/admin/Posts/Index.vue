<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import PostController from '@/actions/App/Http/Controllers/Admin/PostController';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { index } from '@/routes/admin/posts';

interface Author {
    id: number;
    name: string;
}

interface Post {
    id: number;
    title: string;
    slug: string;
    is_published: boolean;
    published_at: string | null;
    author: Author | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedPosts {
    data: Post[];
    links: PaginationLink[];
}

defineProps<{
    posts: PaginatedPosts;
    status: 'all' | 'published' | 'draft';
    counts: {
        all: number;
        published: number;
        draft: number;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Berita',
                href: index(),
            },
        ],
    },
});

function destroyPost(post: Post) {
    if (confirm(`Hapus berita "${post.title}"?`)) {
        router.delete(PostController.destroy(post.id).url);
    }
}
</script>

<template>
    <Head title="Berita" />

    <div class="flex flex-col space-y-6">
        <div class="flex items-center justify-between">
            <Heading
                variant="small"
                title="Berita"
                description="Kelola berita dan pengumuman kos"
            />
            <Button as-child>
                <Link :href="PostController.create().url">Tambah</Link>
            </Button>
        </div>

        <div class="flex flex-wrap gap-2">
            <Button
                variant="outline"
                size="sm"
                :disabled="status === 'all'"
                as-child
            >
                <Link :href="index().url">Semua ({{ counts.all }})</Link>
            </Button>
            <Button
                variant="outline"
                size="sm"
                :disabled="status === 'published'"
                as-child
            >
                <Link :href="index({ query: { status: 'published' } }).url"
                    >Terbit ({{ counts.published }})</Link
                >
            </Button>
            <Button
                variant="outline"
                size="sm"
                :disabled="status === 'draft'"
                as-child
            >
                <Link :href="index({ query: { status: 'draft' } }).url"
                    >Draf ({{ counts.draft }})</Link
                >
            </Button>
        </div>

        <div class="overflow-x-auto rounded-xl border">
            <table class="w-full text-sm">
                <thead class="bg-muted/50 text-left">
                    <tr>
                        <th class="px-4 py-3 font-medium">Judul</th>
                        <th class="px-4 py-3 font-medium">Penulis</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Terbit</th>
                        <th class="px-4 py-3 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="post in posts.data"
                        :key="post.id"
                        class="border-t"
                    >
                        <td class="px-4 py-3 font-medium">
                            {{ post.title }}
                        </td>
                        <td class="px-4 py-3">
                            {{ post.author?.name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span
                                v-if="post.is_published"
                                class="rounded bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700"
                                >Terbit</span
                            >
                            <span
                                v-else
                                class="rounded bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700"
                                >Draf</span
                            >
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            {{ post.published_at ?? '-' }}
                        </td>
                        <td
                            class="flex justify-end gap-2 px-4 py-3 whitespace-nowrap"
                        >
                            <Button variant="outline" size="sm" as-child>
                                <Link :href="PostController.show(post.id).url"
                                    >Lihat</Link
                                >
                            </Button>
                            <Button variant="outline" size="sm" as-child>
                                <Link :href="PostController.edit(post.id).url"
                                    >Ubah</Link
                                >
                            </Button>
                            <Button
                                variant="destructive"
                                size="sm"
                                @click="destroyPost(post)"
                            >
                                Hapus
                            </Button>
                        </td>
                    </tr>
                    <tr v-if="posts.data.length === 0">
                        <td
                            colspan="5"
                            class="text-muted-foreground px-4 py-8 text-center"
                        >
                            Belum ada berita.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="posts.links.length > 3" class="flex flex-wrap gap-1">
            <template v-for="link in posts.links" :key="link.label">
                <Button
                    v-if="link.url"
                    variant="outline"
                    size="sm"
                    :disabled="link.active"
                    as-child
                >
                    <Link :href="link.url" v-html="link.label" />
                </Button>
                <span
                    v-else
                    class="text-muted-foreground inline-flex h-8 items-center px-2 text-sm"
                    v-html="link.label"
                />
            </template>
        </div>
    </div>
</template>
