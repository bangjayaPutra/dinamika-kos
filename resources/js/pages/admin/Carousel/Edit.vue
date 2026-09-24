<script setup lang="ts">
import { Form, Head, Link, setLayoutProps } from '@inertiajs/vue3';
import { ref } from 'vue';
import CarouselItemController from '@/actions/App/Http/Controllers/Admin/CarouselItemController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
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

const props = defineProps<{
    item: CarouselItem;
}>();

setLayoutProps({
    breadcrumbs: [
        {
            title: 'Carousel',
            href: index(),
        },
        {
            title: 'Ubah',
            href: CarouselItemController.edit(props.item.id).url,
        },
    ],
});

const imageError = ref('');

function onImageSelected(event: Event) {
    imageError.value = '';

    const file = (event.target as HTMLInputElement).files?.[0];

    if (file && file.size > 10 * 1024 * 1024) {
        imageError.value = `"${file.name}" melebihi 10 MB.`;
    }
}
</script>

<template>
    <Head title="Ubah Slide" />

    <div class="flex max-w-2xl flex-col space-y-6">
        <Heading
            variant="small"
            title="Ubah Slide"
            description="Perbarui slide banner beranda"
        />

        <Form
            v-bind="CarouselItemController.update.form(item.id)"
            class="space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label>Gambar saat ini</Label>
                <img
                    :src="item.image_url"
                    alt=""
                    class="h-40 w-full rounded-lg border object-cover"
                />
                <Label for="image">Ganti gambar</Label>
                <Input
                    id="image"
                    name="image"
                    type="file"
                    accept=".jpg,.jpeg,.png"
                    class="mt-1 block w-full"
                    @change="onImageSelected"
                />
                <p
                    v-if="imageError"
                    class="text-destructive text-sm font-medium"
                >
                    {{ imageError }}
                </p>
                <InputError class="mt-2" :message="errors.image" />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="title">Judul</Label>
                    <Input
                        id="title"
                        name="title"
                        class="mt-1 block w-full"
                        :default-value="item.title ?? ''"
                    />
                    <InputError class="mt-2" :message="errors.title" />
                </div>

                <div class="grid gap-2">
                    <Label for="link_url">Tautan</Label>
                    <Input
                        id="link_url"
                        name="link_url"
                        type="url"
                        class="mt-1 block w-full"
                        :default-value="item.link_url ?? ''"
                    />
                    <InputError class="mt-2" :message="errors.link_url" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="subtitle">Subjudul</Label>
                <Input
                    id="subtitle"
                    name="subtitle"
                    class="mt-1 block w-full"
                    :default-value="item.subtitle ?? ''"
                />
                <InputError class="mt-2" :message="errors.subtitle" />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="sort_order">Urutan</Label>
                    <Input
                        id="sort_order"
                        name="sort_order"
                        type="number"
                        min="0"
                        class="mt-1 block w-full"
                        :default-value="item.sort_order"
                    />
                    <InputError class="mt-2" :message="errors.sort_order" />
                </div>

                <div class="flex items-end gap-2 pb-2">
                    <input
                        id="is_active"
                        name="is_active"
                        type="checkbox"
                        value="1"
                        :checked="item.is_active"
                        class="border-input h-4 w-4 rounded"
                    />
                    <Label for="is_active">Aktif</Label>
                    <InputError class="mt-2" :message="errors.is_active" />
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing || !!imageError">Simpan</Button>
                <Button variant="outline" as-child>
                    <Link :href="index().url">Batal</Link>
                </Button>
            </div>
        </Form>
    </div>
</template>
