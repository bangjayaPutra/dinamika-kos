<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import RoomTypeController from '@/actions/App/Http/Controllers/Admin/RoomTypeController';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { index } from '@/routes/admin/room-types';

interface RoomType {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    price_monthly: number;
    size_label: string | null;
    capacity: number;
    stock_total: number;
    is_available: boolean;
    sort_order: number;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedRoomTypes {
    data: RoomType[];
    links: PaginationLink[];
    meta: {
        total: number;
    };
}

defineProps<{
    roomTypes: PaginatedRoomTypes;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Tipe Kamar',
                href: index(),
            },
        ],
    },
});

const rupiah = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
});

function destroyRoomType(roomType: RoomType) {
    if (confirm(`Hapus tipe kamar "${roomType.name}"?`)) {
        router.delete(RoomTypeController.destroy(roomType).url);
    }
}
</script>

<template>
    <Head title="Tipe Kamar" />

    <div class="flex flex-col space-y-6">
        <div class="flex items-center justify-between">
            <Button as-child>
                <Link :href="RoomTypeController.create().url">Tambah</Link>
            </Button>
        </div>

        <div class="overflow-x-auto rounded-xl border">
            <table class="w-full text-sm">
                <thead class="bg-muted/50 text-left">
                    <tr>
                        <th class="px-4 py-3 font-medium">Nama</th>
                        <th class="px-4 py-3 font-medium">Harga / bulan</th>
                        <th class="px-4 py-3 font-medium">Kapasitas</th>
                        <th class="px-4 py-3 font-medium">Stok</th>
                        <th class="px-4 py-3 font-medium">Tersedia</th>
                        <th class="px-4 py-3 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="roomType in roomTypes.data"
                        :key="roomType.id"
                        class="border-t"
                    >
                        <td class="px-4 py-3 font-medium">
                            {{ roomType.name }}
                        </td>
                        <td class="px-4 py-3">
                            {{ rupiah.format(roomType.price_monthly) }}
                        </td>
                        <td class="px-4 py-3">{{ roomType.capacity }} orang</td>
                        <td class="px-4 py-3">{{ roomType.stock_total }}</td>
                        <td class="px-4 py-3">
                            {{ roomType.is_available ? 'Ya' : 'Tidak' }}
                        </td>
                        <td
                            class="flex justify-end gap-2 px-4 py-3 whitespace-nowrap"
                        >
                            <Button variant="outline" size="sm" as-child>
                                <Link
                                    :href="
                                        RoomTypeController.edit(roomType).url
                                    "
                                    >Ubah</Link
                                >
                            </Button>
                            <Button
                                variant="destructive"
                                size="sm"
                                @click="destroyRoomType(roomType)"
                            >
                                Hapus
                            </Button>
                        </td>
                    </tr>
                    <tr v-if="roomTypes.data.length === 0">
                        <td
                            colspan="6"
                            class="text-muted-foreground px-4 py-8 text-center"
                        >
                            Belum ada tipe kamar.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="roomTypes.links.length > 3" class="flex flex-wrap gap-1">
            <template v-for="link in roomTypes.links" :key="link.label">
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
