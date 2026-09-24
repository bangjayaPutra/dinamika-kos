<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import ReviewController from '@/actions/App/Http/Controllers/Admin/ReviewController';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { index } from '@/routes/admin/reviews';

interface RoomTypeRef {
    id: number;
    name: string;
}

interface Review {
    id: number;
    nama: string;
    rating: number;
    body: string;
    room_type: RoomTypeRef | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedReviews {
    data: Review[];
    links: PaginationLink[];
}

defineProps<{
    reviews: PaginatedReviews;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Ulasan',
                href: index(),
            },
        ],
    },
});

function stars(rating: number) {
    return '★'.repeat(rating) + '☆'.repeat(5 - rating);
}

function destroyReview(review: Review) {
    if (confirm(`Hapus ulasan dari "${review.nama}"?`)) {
        router.delete(ReviewController.destroy(review.id).url, { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="Ulasan" />

    <div class="flex flex-col space-y-6">
        <Heading
            variant="small"
            title="Ulasan"
            description="Kelola ulasan pengunjung kos"
        />

        <div class="overflow-x-auto rounded-xl border">
            <table class="w-full text-sm">
                <thead class="bg-muted/50 text-left">
                    <tr>
                        <th class="px-4 py-3 font-medium">Nama</th>
                        <th class="px-4 py-3 font-medium">Rating</th>
                        <th class="px-4 py-3 font-medium">Tipe Kamar</th>
                        <th class="px-4 py-3 font-medium">Ulasan</th>
                        <th class="px-4 py-3 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="review in reviews.data"
                        :key="review.id"
                        class="border-t"
                    >
                        <td class="px-4 py-3 font-medium">
                            {{ review.nama }}
                        </td>
                        <td
                            class="px-4 py-3 whitespace-nowrap text-amber-500"
                        >
                            {{ stars(review.rating) }}
                        </td>
                        <td class="px-4 py-3">
                            {{ review.room_type?.name ?? 'Umum' }}
                        </td>
                        <td class="max-w-xs px-4 py-3">
                            {{ review.body }}
                        </td>
                        <td
                            class="flex justify-end gap-2 px-4 py-3 whitespace-nowrap"
                        >
                            <Button
                                variant="destructive"
                                size="sm"
                                @click="destroyReview(review)"
                            >
                                Hapus
                            </Button>
                        </td>
                    </tr>
                    <tr v-if="reviews.data.length === 0">
                        <td
                            colspan="5"
                            class="text-muted-foreground px-4 py-8 text-center"
                        >
                            Belum ada ulasan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="reviews.links.length > 3" class="flex flex-wrap gap-1">
            <template v-for="link in reviews.links" :key="link.label">
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
