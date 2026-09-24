<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import InputError from '@/components/InputError.vue';
import WaButton from '@/components/WaButton.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { store as reviewStore } from '@/routes/reviews';

interface RoomImage {
    id: number;
    url: string;
    is_cover: boolean;
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

interface RoomType {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    price_monthly: number;
    capacity: number;
    size_label: string | null;
    is_available: boolean;
}

const props = defineProps<{
    roomType: RoomType;
    images: RoomImage[];
    facilities: Facility[];
    reviews: Review[];
}>();

const page = usePage();
const siteName = computed(() => page.props.siteSettings.site_name ?? '');

const activeImage = ref(props.images[0]?.url ?? null);

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
    <Head :title="roomType.name" />

    <div class="flex flex-col gap-10">
        <div>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h1 class="text-3xl font-bold tracking-tight">
                    {{ roomType.name }}
                </h1>
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
            <p class="text-brand-700 mt-2 text-xl font-semibold">
                {{ rupiah(roomType.price_monthly) }}
                <span class="text-muted-foreground text-sm font-normal"
                    >/bulan</span
                >
            </p>
            <p class="text-muted-foreground mt-1 text-sm">
                Kapasitas {{ roomType.capacity }} orang
                <span v-if="roomType.size_label"
                    >· {{ roomType.size_label }}</span
                >
            </p>
        </div>

        <div v-if="images.length > 0">
            <img
                v-if="activeImage"
                :src="activeImage"
                :alt="roomType.name"
                class="h-80 w-full rounded-xl border object-cover"
            />
            <div class="mt-2 flex gap-2 overflow-x-auto">
                <button
                    v-for="image in images"
                    :key="image.id"
                    type="button"
                    class="shrink-0 overflow-hidden rounded-lg border-2"
                    :class="
                        activeImage === image.url
                            ? 'border-brand-600'
                            : 'border-transparent'
                    "
                    @click="activeImage = image.url"
                >
                    <img
                        :src="image.url"
                        :alt="roomType.name"
                        class="h-16 w-24 object-cover"
                    />
                </button>
            </div>
        </div>

        <p v-if="roomType.description" class="max-w-3xl whitespace-pre-line">
            {{ roomType.description }}
        </p>

        <div v-if="facilities.length > 0">
            <h2 class="text-xl font-semibold">Fasilitas Kamar</h2>
            <ul class="mt-3 grid gap-2 sm:grid-cols-2">
                <li
                    v-for="facility in facilities"
                    :key="facility.id"
                    class="rounded-lg border px-3 py-2 text-sm"
                >
                    {{ facility.name }}
                </li>
            </ul>
        </div>

        <div>
            <WaButton
                :room-type-id="roomType.id"
                :message="`Halo ${siteName}, saya tertarik dengan ${roomType.name}. Apakah masih tersedia?`"
            >
                Tanya via WhatsApp
            </WaButton>
        </div>

        <div>
            <h2 class="text-xl font-semibold">Ulasan Penghuni</h2>
            <div
                v-if="reviews.length > 0"
                class="mt-3 grid gap-4 md:grid-cols-2"
            >
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
                    <figcaption
                        class="text-muted-foreground mt-2 text-sm font-medium"
                    >
                        — {{ review.nama }}
                    </figcaption>
                </figure>
            </div>
            <p v-else class="text-muted-foreground mt-3 text-sm">
                Belum ada ulasan untuk tipe ini. Jadilah yang pertama!
            </p>

            <h3 class="mt-8 text-lg font-semibold">Tulis Ulasan</h3>
            <Form
                v-bind="reviewStore.form()"
                class="mt-3 max-w-xl space-y-4"
                v-slot="{ errors, processing }"
            >
                <input type="hidden" name="room_type_id" :value="roomType.id" />

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="nama">Nama</Label>
                        <Input
                            id="nama"
                            name="nama"
                            required
                            maxlength="100"
                            placeholder="Nama kamu"
                        />
                        <InputError :message="errors.nama" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="rating">Rating</Label>
                        <select
                            id="rating"
                            name="rating"
                            required
                            class="border-input bg-background block w-full rounded-md border px-3 py-2 text-sm"
                        >
                            <option value="5">★★★★★ (5)</option>
                            <option value="4">★★★★ (4)</option>
                            <option value="3">★★★ (3)</option>
                            <option value="2">★★ (2)</option>
                            <option value="1">★ (1)</option>
                        </select>
                        <InputError :message="errors.rating" />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="body">Ulasan</Label>
                    <textarea
                        id="body"
                        name="body"
                        rows="4"
                        required
                        maxlength="2000"
                        placeholder="Ceritakan pengalamanmu tinggal di sini"
                        class="border-input bg-background block w-full rounded-md border px-3 py-2 text-sm"
                    />
                    <InputError :message="errors.body" />
                </div>

                <Button :disabled="processing" class="bg-brand-700 text-white shadow-sm hover:bg-brand-800">Kirim Ulasan</Button>
            </Form>
        </div>
    </div>
</template>
