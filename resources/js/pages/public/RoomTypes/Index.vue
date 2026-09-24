<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { show } from '@/routes/public/room-types';

interface RoomType {
    id: number;
    name: string;
    slug: string;
    price_monthly: number;
    capacity: number;
    size_label: string | null;
    is_available: boolean;
    cover_url: string | null;
}

defineProps<{
    roomTypes: RoomType[];
}>();

function rupiah(value: number) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value);
}
</script>

<template>
    <Head title="Tipe Kamar" />

    <div class="flex flex-col gap-6">
        <div>
            <h1 class="flex items-center gap-2 text-3xl font-bold tracking-tight"><span class="bg-brand-600 h-7 w-1.5 rounded-full" />Tipe Kamar</h1>
            <p class="text-muted-foreground mt-1">
                Pilih tipe kamar sesuai kebutuhanmu
            </p>
        </div>

        <div
            v-if="roomTypes.length > 0"
            class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3"
        >
            <Link
                v-for="roomType in roomTypes"
                :key="roomType.id"
                :href="show(roomType.slug).url"
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
                <div class="space-y-1 p-4">
                    <div class="flex items-center justify-between">
                        <p class="font-semibold">{{ roomType.name }}</p>
                        <span
                            v-if="roomType.is_available"
                            class="rounded bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700"
                            >Tersedia</span
                        >
                        <span
                            v-else
                            class="rounded bg-muted px-2 py-0.5 text-xs font-medium text-muted-foreground"
                            >Penuh</span
                        >
                    </div>
                    <p class="text-brand-700 text-sm font-semibold">
                        {{ rupiah(roomType.price_monthly) }}/bulan
                    </p>
                    <p class="text-muted-foreground text-xs">
                        Kapasitas {{ roomType.capacity }} orang
                        <span v-if="roomType.size_label"
                            >· {{ roomType.size_label }}</span
                        >
                    </p>
                </div>
            </Link>
        </div>

        <p v-else class="text-muted-foreground">
            Belum ada tipe kamar ditampilkan.
        </p>
    </div>
</template>
