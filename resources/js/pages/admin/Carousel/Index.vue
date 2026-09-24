<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import CarouselItemController from '@/actions/App/Http/Controllers/Admin/CarouselItemController';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { index } from '@/routes/admin/carousel-items';

interface CarouselItem {
    id: number;
    title: string | null;
    subtitle: string | null;
    image_url: string;
    link_url: string | null;
    sort_order: number;
    is_active: boolean;
}

defineProps<{
    items: CarouselItem[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Carousel',
                href: index(),
            },
        ],
    },
});

function destroyItem(item: CarouselItem) {
    if (confirm(`Hapus slide "${item.title ?? 'tanpa judul'}"?`)) {
        router.delete(CarouselItemController.destroy(item.id).url);
    }
}
</script>

<template>
    <Head title="Carousel" />

    <div class="flex flex-col space-y-6">
        <div class="flex items-center justify-between">
            <Heading
                variant="small"
                title="Carousel"
                description="Kelola slide banner di beranda"
            />
            <Button as-child>
                <Link :href="CarouselItemController.create().url">Tambah</Link>
            </Button>
        </div>

        <div class="overflow-x-auto rounded-xl border">
            <table class="w-full text-sm">
                <thead class="bg-muted/50 text-left">
                    <tr>
                        <th class="px-4 py-3 font-medium">Gambar</th>
                        <th class="px-4 py-3 font-medium">Judul</th>
                        <th class="px-4 py-3 font-medium">Urutan</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="item in items"
                        :key="item.id"
                        class="border-t"
                    >
                        <td class="px-4 py-3">
                            <img
                                :src="item.image_url"
                                alt=""
                                class="h-12 w-20 rounded object-cover"
                            />
                        </td>
                        <td class="px-4 py-3 font-medium">
                            {{ item.title ?? '—' }}
                        </td>
                        <td class="px-4 py-3">{{ item.sort_order }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span
                                v-if="item.is_active"
                                class="rounded bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700"
                                >Aktif</span
                            >
                            <span
                                v-else
                                class="rounded bg-muted px-2 py-0.5 text-xs font-medium text-muted-foreground"
                                >Nonaktif</span
                            >
                        </td>
                        <td
                            class="flex justify-end gap-2 px-4 py-3 whitespace-nowrap"
                        >
                            <Button variant="outline" size="sm" as-child>
                                <Link
                                    :href="
                                        CarouselItemController.edit(item.id).url
                                    "
                                    >Ubah</Link
                                >
                            </Button>
                            <Button
                                variant="destructive"
                                size="sm"
                                @click="destroyItem(item)"
                            >
                                Hapus
                            </Button>
                        </td>
                    </tr>
                    <tr v-if="items.length === 0">
                        <td
                            colspan="5"
                            class="text-muted-foreground px-4 py-8 text-center"
                        >
                            Belum ada slide.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
