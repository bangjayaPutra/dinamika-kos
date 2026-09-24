<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

interface Facility {
    id: number;
    name: string;
    icon: string | null;
    description: string | null;
}

defineProps<{
    roomFacilities: Facility[];
    generalFacilities: Facility[];
}>();
</script>

<template>
    <Head title="Fasilitas" />

    <div class="flex flex-col gap-10">
        <div>
            <h1 class="flex items-center gap-2 text-3xl font-bold tracking-tight"><span class="bg-brand-600 h-7 w-1.5 rounded-full" />Fasilitas</h1>
            <p class="text-muted-foreground mt-1">
                Fasilitas kamar dan fasilitas umum yang tersedia
            </p>
        </div>

        <section>
            <h2 class="flex items-center gap-2 text-xl font-semibold"><span class="bg-brand-200 h-5 w-1 rounded-full" />Fasilitas Umum</h2>
            <div
                v-if="generalFacilities.length > 0"
                class="mt-3 grid gap-4 sm:grid-cols-2 lg:grid-cols-3"
            >
                <div
                    v-for="facility in generalFacilities"
                    :key="facility.id"
                    class="rounded-xl border p-4"
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
            <p v-else class="text-muted-foreground mt-3 text-sm">
                Belum ada fasilitas umum.
            </p>
        </section>

        <section>
            <h2 class="flex items-center gap-2 text-xl font-semibold"><span class="bg-brand-200 h-5 w-1 rounded-full" />Fasilitas Kamar</h2>
            <p class="text-muted-foreground mt-1 text-sm">
                Setiap tipe kamar dapat memiliki kombinasi berbeda — detailnya
                ada di halaman masing-masing tipe.
            </p>
            <div
                v-if="roomFacilities.length > 0"
                class="mt-3 grid gap-4 sm:grid-cols-2 lg:grid-cols-3"
            >
                <div
                    v-for="facility in roomFacilities"
                    :key="facility.id"
                    class="rounded-xl border p-4"
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
            <p v-else class="text-muted-foreground mt-3 text-sm">
                Belum ada fasilitas kamar.
            </p>
        </section>
    </div>
</template>
