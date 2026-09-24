<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import WaButton from '@/components/WaButton.vue';
import { index, show } from '@/routes/public/posts';

interface Post {
    id: number;
    title: string;
    excerpt: string | null;
    body: string;
    published_at: string | null;
    author_name: string | null;
    cover_url: string | null;
}

interface LatestPost {
    id: number;
    title: string;
    slug: string;
}

defineProps<{
    post: Post;
    latest: LatestPost[];
}>();
</script>

<template>
    <Head :title="post.title" />

    <div class="mx-auto flex max-w-3xl flex-col gap-6">
        <div>
            <p class="text-muted-foreground text-sm">
                {{ post.author_name ?? '' }}
                {{ post.published_at ? `· ${post.published_at}` : '' }}
            </p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight">
                {{ post.title }}
            </h1>
            <p v-if="post.excerpt" class="text-muted-foreground mt-2 text-lg">
                {{ post.excerpt }}
            </p>
        </div>

        <img
            v-if="post.cover_url"
            :src="post.cover_url"
            :alt="post.title"
            class="w-full rounded-xl border object-cover"
        />

        <div
            class="text-sm leading-relaxed [&_a]:text-brand-700 [&_a]:underline [&_h2]:mt-6 [&_h2]:text-xl [&_h2]:font-semibold [&_h3]:mt-4 [&_h3]:font-semibold [&_ol]:list-decimal [&_ol]:pl-6 [&_p]:my-3 [&_ul]:list-disc [&_ul]:pl-6 [&_blockquote]:border-l-2 [&_blockquote]:border-brand-200 [&_blockquote]:pl-3 [&_blockquote]:text-muted-foreground [&_blockquote]:italic"
            v-html="post.body"
        />

        <div
            class="flex flex-wrap items-center justify-between gap-3 border-t pt-6"
        >
            <WaButton size="sm">Tanya Kos via WhatsApp</WaButton>
            <Link :href="index().url" class="text-brand-700 text-sm underline underline-offset-2 hover:text-brand-800">
                Kembali ke semua berita
            </Link>
        </div>

        <div v-if="latest.length > 0" class="border-t pt-6">
            <h2 class="text-lg font-semibold">Berita lainnya</h2>
            <ul class="mt-2 space-y-1 text-sm">
                <li v-for="item in latest" :key="item.id">
                    <Link
                        :href="show(item.slug).url"
                        class="text-brand-700 underline underline-offset-2 hover:text-brand-800"
                    >
                        {{ item.title }}
                    </Link>
                </li>
            </ul>
        </div>
    </div>
</template>
