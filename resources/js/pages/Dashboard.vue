<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import WaClickChart from '@/components/WaClickChart.vue';
import { dashboard } from '@/routes';

interface DayTotal {
    day: number;
    total: number;
}

interface RoomTypeTotal {
    name: string;
    total: number;
}

const props = defineProps<{
    filter: { month: number; year: number };
    stats: { wa_clicks: number; room_types: number; reviews: number };
    published_posts: number;
    daily: DayTotal[];
    byRoomType: RoomTypeTotal[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

const MONTHS = [
    'Januari',
    'Februari',
    'Maret',
    'April',
    'Mei',
    'Juni',
    'Juli',
    'Agustus',
    'September',
    'Oktober',
    'November',
    'Desember',
];

const thisYear = new Date().getFullYear();
const years = Array.from({ length: 7 }, (_, i) => thisYear + 1 - i).filter((year) => year >= 2020);

const month = ref(props.filter.month);
const year = ref(props.filter.year);

const periodLabel = computed(() => `${MONTHS[props.filter.month - 1]} ${props.filter.year}`);

function applyFilter() {
    router.get(
        dashboard().url,
        { month: month.value, year: year.value },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}

const maxRoomType = computed(() => Math.max(1, ...props.byRoomType.map((item) => item.total)));

const cards = computed(() => [
    { label: 'Klik WhatsApp', value: props.stats.wa_clicks, hint: periodLabel.value },
    { label: 'Tipe Kamar', value: props.stats.room_types, hint: 'Total tipe' },
    { label: 'Ulasan', value: props.stats.reviews, hint: 'Total ulasan' },
    { label: 'Berita Terbit', value: props.published_posts, hint: 'Total terbit' },
]);
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-col gap-6">
        <Heading
            variant="small"
            title="Dashboard"
            description="Ringkasan aktivitas kos"
        />

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div
                v-for="card in cards"
                :key="card.label"
                class="rounded-xl border p-4"
            >
                <p class="text-muted-foreground text-sm">{{ card.label }}</p>
                <p class="mt-1 text-3xl font-bold tracking-tight">
                    {{ card.value }}
                </p>
                <p class="text-muted-foreground mt-1 text-xs">{{ card.hint }}</p>
            </div>
        </div>

        <div class="rounded-xl border p-4 md:p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="font-semibold">Klik WhatsApp Harian</h2>
                    <p class="text-muted-foreground text-sm">
                        Periode {{ periodLabel }}
                    </p>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <select
                        v-model.number="month"
                        aria-label="Bulan"
                        class="border-input bg-background rounded-md border px-3 py-1.5"
                        @change="applyFilter"
                    >
                        <option
                            v-for="(name, index) in MONTHS"
                            :key="name"
                            :value="index + 1"
                        >
                            {{ name }}
                        </option>
                    </select>
                    <select
                        v-model.number="year"
                        aria-label="Tahun"
                        class="border-input bg-background rounded-md border px-3 py-1.5"
                        @change="applyFilter"
                    >
                        <option v-for="item in years" :key="item" :value="item">
                            {{ item }}
                        </option>
                    </select>
                </div>
            </div>

            <WaClickChart :daily="daily" class="mt-4" />

            <div v-if="byRoomType.length > 0" class="mt-6 border-t pt-4">
                <h3 class="text-sm font-semibold">Klik per tipe kamar</h3>
                <ul class="mt-3 space-y-2">
                    <li
                        v-for="item in byRoomType"
                        :key="item.name"
                        class="flex items-center gap-3 text-sm"
                    >
                        <span class="w-32 shrink-0 truncate font-medium">{{
                            item.name
                        }}</span>
                        <div class="bg-muted h-2 flex-1 overflow-hidden rounded-full">
                            <div
                                class="bg-brand-700 h-full rounded-full"
                                :style="{ width: `${(item.total / maxRoomType) * 100}%` }"
                            />
                        </div>
                        <span class="text-muted-foreground w-12 shrink-0 text-right tabular-nums"
                            >{{ item.total }} klik</span
                        >
                    </li>
                </ul>
            </div>
            <p v-else class="text-muted-foreground mt-6 text-sm">
                Belum ada klik WhatsApp pada periode ini.
            </p>
        </div>
    </div>
</template>
