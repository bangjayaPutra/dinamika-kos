<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { show } from '@/routes/public/posts';

interface PostCard {
    id: number;
    title: string;
    slug: string;
    excerpt: string | null;
    published_at: string | null;
    author_name: string | null;
    cover_url: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedPosts {
    data: PostCard[];
    links: PaginationLink[];
}

defineProps<{
    posts: PaginatedPosts;
}>();
</script>

<template>
    <Head title="Berita" />

    <div class="flex flex-col gap-6">
        <div>
            <h1 class="flex items-center gap-2 text-3xl font-bold tracking-tight"><span class="bg-brand-600 h-7 w-1.5 rounded-full" />Berita</h1>
            <p class="text-muted-foreground mt-1">
                Kabar dan pengumuman terbaru dari kos
            </p>
        </div>

        <div
            v-if="posts.data.length > 0"
            class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3"
        >
            <Link
                v-for="post in posts.data"
                :key="post.id"
                :href="show(post.slug).url"
                class="hover:border-brand-600 overflow-hidden rounded-xl border transition-shadow hover:shadow-md"
            >
                <img
                    v-if="post.cover_url"
                    :src="post.cover_url"
                    :alt="post.title"
                    class="h-40 w-full object-cover"
                />
                <div class="p-4">
                    <p class="font-semibold">{{ post.title }}</p>
                    <p
                        v-if="post.excerpt"
                        class="text-muted-foreground mt-1 line-clamp-2 text-sm"
                    >
                        {{ post.excerpt }}
                    </p>
                    <p class="text-muted-foreground mt-2 text-xs">
                        {{ post.author_name ?? '' }}
                        {{ post.published_at ? `· ${post.published_at}` : '' }}
                    </p>
                </div>
            </Link>
        </div>
        <p v-else class="text-muted-foreground">Belum ada berita.</p>

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
