<script setup lang="ts">
import { computed } from 'vue';

interface DayTotal {
    day: number;
    total: number;
}

const props = defineProps<{
    daily: DayTotal[];
}>();

const WIDTH = 720;
const HEIGHT = 260;
const PAD_LEFT = 36;
const PAD_RIGHT = 8;
const PAD_TOP = 12;
const PAD_BOTTOM = 28;

const maxTotal = computed(() => Math.max(1, ...props.daily.map((d) => d.total)));

const niceMax = computed(() => {
    const max = maxTotal.value;
    const step = Math.pow(10, Math.floor(Math.log10(max)));
    const normalized = max / step;

    if (normalized <= 1) {
        return step;
    }

    if (normalized <= 2) {
        return 2 * step;
    }

    if (normalized <= 5) {
        return 5 * step;
    }

    return 10 * step;
});

const gridlines = computed(() => [
    ...new Set([0, 0.25, 0.5, 0.75, 1].map((ratio) => Math.round(niceMax.value * ratio))),
]);

const plotWidth = WIDTH - PAD_LEFT - PAD_RIGHT;
const plotHeight = HEIGHT - PAD_TOP - PAD_BOTTOM;

const slotWidth = computed(() => plotWidth / props.daily.length);
const barWidth = computed(() => Math.max(2, Math.min(24, slotWidth.value * 0.6)));

function barHeight(total: number) {
    return (total / niceMax.value) * plotHeight;
}

function barX(index: number) {
    return PAD_LEFT + index * slotWidth.value + (slotWidth.value - barWidth.value) / 2;
}

function barY(total: number) {
    return PAD_TOP + plotHeight - barHeight(total);
}

function gridY(value: number) {
    return PAD_TOP + plotHeight - (value / niceMax.value) * plotHeight;
}

const labelStep = computed(() => Math.ceil(props.daily.length / 12));
</script>

<template>
    <svg
        :viewBox="`0 0 ${WIDTH} ${HEIGHT}`"
        class="h-auto w-full"
        role="img"
        aria-label="Grafik klik WhatsApp harian"
    >
        <g v-for="value in gridlines" :key="value">
            <line
                :x1="PAD_LEFT"
                :x2="WIDTH - PAD_RIGHT"
                :y1="gridY(value)"
                :y2="gridY(value)"
                class="stroke-border"
                stroke-dasharray="4 4"
            />
            <text
                :x="PAD_LEFT - 6"
                :y="gridY(value) + 4"
                text-anchor="end"
                class="fill-muted-foreground text-[11px]"
            >
                {{ value }}
            </text>
        </g>

        <g v-for="(item, index) in daily" :key="item.day">
            <rect
                :x="barX(index)"
                :y="barY(item.total)"
                :width="barWidth"
                :height="Math.max(item.total > 0 ? 3 : 0, barHeight(item.total))"
                rx="3"
                :class="item.total === maxTotal && item.total > 0 ? 'fill-brand-500' : 'fill-brand-700/80'"
            >
                <title>Tanggal {{ item.day }}: {{ item.total }} klik</title>
            </rect>
            <text
                v-if="index % labelStep === 0"
                :x="PAD_LEFT + index * slotWidth + slotWidth / 2"
                :y="HEIGHT - 8"
                text-anchor="middle"
                class="fill-muted-foreground text-[11px]"
            >
                {{ item.day }}
            </text>
        </g>
    </svg>
</template>
