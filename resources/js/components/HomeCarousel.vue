<script setup lang="ts">
import { ChevronLeft, ChevronRight } from '@lucide/vue';
import { onBeforeUnmount, ref } from 'vue';

interface Slide {
    id: number;
    title: string | null;
    subtitle: string | null;
    image_url: string;
    link_url: string | null;
}

const props = defineProps<{
    slides: Slide[];
}>();

const current = ref(0);
const paused = ref(false);

const timer = setInterval(() => {
    if (!paused.value && props.slides.length > 1) {
        current.value = (current.value + 1) % props.slides.length;
    }
}, 5000);

onBeforeUnmount(() => {
    clearInterval(timer);
});

function goTo(index: number) {
    current.value = (index + props.slides.length) % props.slides.length;
}
</script>

<template>
    <div
        v-if="slides.length > 0"
        class="relative overflow-hidden rounded-xl border"
        @mouseenter="paused = true"
        @mouseleave="paused = false"
    >
        <div
            class="flex transition-transform duration-500"
            :style="{ transform: `translateX(-${current * 100}%)` }"
        >
            <div
                v-for="slide in slides"
                :key="slide.id"
                class="relative w-full shrink-0"
            >
                <img
                    :src="slide.image_url"
                    :alt="slide.title ?? 'Banner'"
                    class="h-64 w-full object-cover md:h-96"
                />
                <div
                    v-if="slide.title || slide.subtitle"
                    class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent p-6 pt-12 text-white"
                >
                    <p v-if="slide.title" class="text-xl font-bold md:text-2xl">
                        {{ slide.title }}
                    </p>
                    <p v-if="slide.subtitle" class="mt-1 text-sm md:text-base">
                        {{ slide.subtitle }}
                    </p>
                    <a
                        v-if="slide.link_url"
                        :href="slide.link_url"
                        target="_blank"
                        rel="noopener"
                        class="bg-brand-700 hover:bg-brand-800 mt-2 inline-block rounded-md px-4 py-1.5 text-sm font-medium text-white"
                    >
                        Selengkapnya
                    </a>
                </div>
            </div>
        </div>

        <template v-if="slides.length > 1">
            <button
                type="button"
                aria-label="Sebelumnya"
                class="absolute top-1/2 left-3 -translate-y-1/2 rounded-full bg-black/40 p-1.5 text-white hover:bg-brand-800/80"
                @click="goTo(current - 1)"
            >
                <ChevronLeft class="h-5 w-5" />
            </button>
            <button
                type="button"
                aria-label="Berikutnya"
                class="absolute top-1/2 right-3 -translate-y-1/2 rounded-full bg-black/40 p-1.5 text-white hover:bg-brand-800/80"
                @click="goTo(current + 1)"
            >
                <ChevronRight class="h-5 w-5" />
            </button>
            <div
                class="absolute bottom-3 left-1/2 flex -translate-x-1/2 gap-1.5"
            >
                <button
                    v-for="(slide, index) in slides"
                    :key="slide.id"
                    type="button"
                    :aria-label="`Ke slide ${index + 1}`"
                    class="h-2 rounded-full transition-all"
                    :class="
                        index === current
                            ? 'w-6 bg-brand-500'
                            : 'w-2 bg-white/50 hover:bg-white/80'
                    "
                    @click="goTo(index)"
                />
            </div>
        </template>
    </div>
</template>
