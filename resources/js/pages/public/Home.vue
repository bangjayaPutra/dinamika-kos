<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import HomeCarousel from '@/components/HomeCarousel.vue';
import WaButton from '@/components/WaButton.vue';
import { Button } from '@/components/ui/button';
import { show as roomTypeShow } from '@/routes/public/room-types';
import { show as postShow } from '@/routes/public/posts';

interface RoomType {
    id: number;
    name: string;
    slug: string;
    price_monthly: number;
    cover_url: string | null;
}

interface Facility {
    id: number;
    name: string;
    icon: string | null;
    description: string | null;
}

interface Review {
    id: number;
    nama: string;
    rating: number;
    body: string;
}

interface Post {
    id: number;
    title: string;
    slug: string;
    excerpt: string | null;
    published_at: string | null;
}

interface CarouselSlide {
    id: number;
    title: string | null;
    subtitle: string | null;
    image_url: string;
    link_url: string | null;
}

defineProps<{
    carousel: CarouselSlide[];
    roomTypes: RoomType[];
    facilities: Facility[];
    reviews: Review[];
    posts: Post[];
}>();

const page = usePage();
const settings = computed(() => page.props.siteSettings);

function rupiah(value: number) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value);
}

function stars(rating: number) {
    return '★'.repeat(rating) + '☆'.repeat(5 - rating);
}
</script>

<template>
    <Head :title="settings.site_name ?? 'Beranda'" />

    <div class="flex flex-col gap-16">
        <HomeCarousel :slides="carousel" />

        <section class="bg-gradient-to-b from-brand-50 to-transparent -mx-4 rounded-b-3xl px-4 py-10 text-center">
            <h1 class="text-3xl font-bold tracking-tight md:text-4xl">
                {{ settings.site_name ?? 'Kos' }}
            </h1>
            <p class="text-brand-800 mt-3 text-lg font-medium">
                {{ settings.tagline ?? '' }}
            </p>
            <p v-if="settings.welcome_text" class="mx-auto mt-4 max-w-2xl">
                {{ settings.welcome_text }}
            </p>
            <div class="mt-6 flex justify-center gap-3">
                <WaButton
                    :message="`Halo ${settings.site_name ?? ''}, saya tertarik dengan kosnya.`"
                >
                    Pesan via WhatsApp
                </WaButton>
                <Button variant="outline" class="border-brand-200 hover:border-brand-600 hover:text-brand-700" as-child>
                    <Link href="/tipe-kamar">Lihat Tipe Kamar</Link>
                </Button>
            </div>
        </section>

        <section v-if="roomTypes.length > 0">
            <h2 class="flex items-center gap-2 text-2xl font-semibold"><span class="bg-brand-600 h-6 w-1.5 rounded-full" />Tipe Kamar</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="roomType in roomTypes"
                    :key="roomType.id"
                    :href="roomTypeShow(roomType.slug).url"
                    class="hover:border-brand-600 overflow-hidden rounded-xl border transition-shadow hover:shadow-md"
                >
                    <img
                        v-if="roomType.cover_url"
                        :src="roomType.cover_url"
                        :alt="roomType.name"
                        class="h-44 w-full object-cover"
                    />
                    <div
                        v-else
                        class="bg-muted flex h-44 w-full items-center justify-center text-sm text-muted-foreground"
                    >
                        Tanpa foto
                    </div>
                    <div class="p-4">
                        <p class="font-semibold">{{ roomType.name }}</p>
                        <p class="text-brand-700 mt-1 text-sm font-semibold">
                            {{ rupiah(roomType.price_monthly) }}/bulan
                        </p>
                    </div>
                </Link>
            </div>
        </section>

        <section v-if="facilities.length > 0">
            <h2 class="flex items-center gap-2 text-2xl font-semibold"><span class="bg-brand-600 h-6 w-1.5 rounded-full" />Fasilitas Umum</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="facility in facilities"
                    :key="facility.id"
                    class="hover:border-brand-200 hover:bg-brand-50 rounded-xl border p-4 transition-colors"
                >
                    <p class="font-semibold">{{ facility.name }}</p>
                    <p
                        v-if="facility.description"
                        class="text-muted-foreground mt-1 text-sm"
                    >
                        {{ facility.description }}
                    </p>
                </div>
            </div>
            <Button variant="outline" class="border-brand-200 hover:border-brand-600 hover:text-brand-700 mt-4" as-child>
                <Link href="/fasilitas">Semua fasilitas</Link>
            </Button>
        </section>

        <section v-if="reviews.length > 0">
            <h2 class="flex items-center gap-2 text-2xl font-semibold"><span class="bg-brand-600 h-6 w-1.5 rounded-full" />Kata Penghuni</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <figure
                    v-for="review in reviews"
                    :key="review.id"
                    class="rounded-xl border p-4"
                >
                    <div class="text-amber-500">
                        {{ stars(review.rating) }}
                    </div>
                    <blockquote class="mt-2 text-sm">
                        {{ review.body }}
                    </blockquote>
                    <figcaption class="text-muted-foreground mt-2 text-sm font-medium">
                        — {{ review.nama }}
                    </figcaption>
                </figure>
            </div>
        </section>

        <section v-if="posts.length > 0">
            <h2 class="flex items-center gap-2 text-2xl font-semibold"><span class="bg-brand-600 h-6 w-1.5 rounded-full" />Berita Terbaru</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-3">
                <Link
                    v-for="post in posts"
                    :key="post.id"
                    :href="postShow(post.slug).url"
                    class="hover:border-brand-600 rounded-xl border p-4 transition-shadow hover:shadow-md"
                >
                    <p class="font-semibold">{{ post.title }}</p>
                    <p
                        v-if="post.excerpt"
                        class="text-muted-foreground mt-1 line-clamp-2 text-sm"
                    >
                        {{ post.excerpt }}
                    </p>
                    <p class="text-muted-foreground mt-2 text-xs">
                        {{ post.published_at ?? '' }}
                    </p>
                </Link>
            </div>
        </section>
    </div>
</template>
