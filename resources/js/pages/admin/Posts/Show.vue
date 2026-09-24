<script setup lang="ts">
import { Head, Link, setLayoutProps } from '@inertiajs/vue3';
import PostController from '@/actions/App/Http/Controllers/Admin/PostController';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { index } from '@/routes/admin/posts';

interface Post {
    id: number;
    title: string;
    slug: string;
    excerpt: string | null;
    body: string;
    is_published: boolean;
    published_at: string | null;
    created_at: string;
    author: {
        id: number;
        name: string;
    } | null;
}

const props = defineProps<{
    post: Post;
    cover_url: string | null;
}>();

setLayoutProps({
    breadcrumbs: [
        {
            title: 'Berita',
            href: index(),
        },
        {
            title: 'Pratinjau',
            href: PostController.show(props.post.id).url,
        },
    ],
});
</script>

<template>
    <Head :title="post.title" />

    <div class="flex max-w-3xl flex-col space-y-6">
        <div class="flex items-center justify-between">
            <Heading
                variant="small"
                title="Pratinjau Berita"
                description="Tampilan berita saat diterbitkan"
            />
            <div class="flex gap-2">
                <Button variant="outline" as-child>
                    <Link :href="index().url">Kembali</Link>
                </Button>
                <Button as-child>
                    <Link :href="PostController.edit(post.id).url">Ubah</Link>
                </Button>
            </div>
        </div>

        <article class="space-y-4">
            <div class="flex flex-wrap items-center gap-2 text-sm">
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
                <span class="text-muted-foreground">
                    Oleh {{ post.author?.name ?? '-' }}
                </span>
                <span class="text-muted-foreground">
                    · {{ post.published_at ?? post.created_at }}
                </span>
            </div>

            <h1 class="text-2xl font-bold tracking-tight">
                {{ post.title }}
            </h1>

            <p v-if="post.excerpt" class="text-muted-foreground text-lg">
                {{ post.excerpt }}
            </p>

            <img
                v-if="cover_url"
                :src="cover_url"
                alt="Sampul berita"
                class="w-full rounded-xl border object-cover"
            />

            <div
                class="text-sm leading-relaxed [&_h2]:mt-6 [&_h2]:text-xl [&_h2]:font-semibold [&_h3]:mt-4 [&_h3]:font-semibold [&_ol]:list-decimal [&_ol]:pl-6 [&_p]:my-3 [&_ul]:list-disc [&_ul]:pl-6 [&_blockquote]:border-l-2 [&_blockquote]:pl-3 [&_blockquote]:text-muted-foreground [&_blockquote]:italic"
                v-html="post.body"
            />
        </article>
    </div>
</template>
