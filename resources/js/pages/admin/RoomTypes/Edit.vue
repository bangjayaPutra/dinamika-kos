<script setup lang="ts">
import { Form, Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { ref } from 'vue';
import RoomTypeController from '@/actions/App/Http/Controllers/Admin/RoomTypeController';
import RoomImageController from '@/actions/App/Http/Controllers/Admin/RoomImageController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index } from '@/routes/admin/room-types';
import { index as facilitiesIndex } from '@/routes/admin/facilities';

interface FacilityOption {
    id: number;
    name: string;
}

interface RoomTypeImage {
    id: number;
    url: string;
    is_cover: boolean;
}

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

const props = defineProps<{
    roomType: RoomType;
    facilities: FacilityOption[];
    selectedFacilities: number[];
    images: RoomTypeImage[];
}>();

function setCoverImage(image: RoomTypeImage) {
    router.patch(RoomImageController.setCover(image.id).url);
}

function destroyImage(image: RoomTypeImage) {
    if (confirm('Hapus gambar ini dari galeri?')) {
        router.delete(RoomImageController.destroy(image.id).url);
    }
}

const uploadError = ref('');

function onFilesSelected(event: Event) {
    uploadError.value = '';

    const files = Array.from(
        (event.target as HTMLInputElement).files ?? [],
    );

    if (props.images.length + files.length > 6) {
        uploadError.value = `Tipe kamar maksimal memiliki 6 gambar (sudah ada ${props.images.length}).`;
        return;
    }

    for (const file of files) {
        if (file.size > 10 * 1024 * 1024) {
            uploadError.value = `"${file.name}" melebihi 10 MB.`;
            return;
        }
    }
}

setLayoutProps({
    breadcrumbs: [
        {
            title: 'Tipe Kamar',
            href: index(),
        },
        {
            title: 'Ubah',
            href: RoomTypeController.edit(props.roomType).url,
        },
    ],
});
</script>

<template>
    <Head title="Ubah Tipe Kamar" />

    <div class="flex max-w-2xl flex-col space-y-6">
        <Heading
            variant="small"
            title="Ubah Tipe Kamar"
            description="Perbarui detail tipe kamar"
        />

        <Form
            v-bind="RoomTypeController.update.form(roomType)"
            class="space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="name">Nama</Label>
                <Input
                    id="name"
                    name="name"
                    class="mt-1 block w-full"
                    required
                    :default-value="roomType.name"
                />
                <InputError class="mt-2" :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="description">Deskripsi</Label>
                <textarea
                    id="description"
                    name="description"
                    rows="3"
                    :value="roomType.description ?? ''"
                    class="border-input bg-background mt-1 block w-full rounded-md border px-3 py-2 text-sm"
                />
                <InputError class="mt-2" :message="errors.description" />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="price_monthly">Harga per bulan (Rp)</Label>
                    <Input
                        id="price_monthly"
                        name="price_monthly"
                        type="number"
                        min="0"
                        class="mt-1 block w-full"
                        required
                        :default-value="roomType.price_monthly"
                    />
                    <InputError class="mt-2" :message="errors.price_monthly" />
                </div>

                <div class="grid gap-2">
                    <Label for="size_label">Ukuran</Label>
                    <Input
                        id="size_label"
                        name="size_label"
                        class="mt-1 block w-full"
                        :default-value="roomType.size_label ?? ''"
                    />
                    <InputError class="mt-2" :message="errors.size_label" />
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="capacity">Kapasitas (orang)</Label>
                    <Input
                        id="capacity"
                        name="capacity"
                        type="number"
                        min="1"
                        max="10"
                        class="mt-1 block w-full"
                        required
                        :default-value="roomType.capacity"
                    />
                    <InputError class="mt-2" :message="errors.capacity" />
                </div>

                <div class="grid gap-2">
                    <Label for="stock_total">Stok kamar</Label>
                    <Input
                        id="stock_total"
                        name="stock_total"
                        type="number"
                        min="0"
                        class="mt-1 block w-full"
                        required
                        :default-value="roomType.stock_total"
                    />
                    <InputError class="mt-2" :message="errors.stock_total" />
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="is_available">Tersedia</Label>
                    <select
                        id="is_available"
                        name="is_available"
                        :value="roomType.is_available ? '1' : '0'"
                        class="border-input bg-background mt-1 block w-full rounded-md border px-3 py-2 text-sm"
                    >
                        <option value="1">Ya</option>
                        <option value="0">Tidak</option>
                    </select>
                    <InputError class="mt-2" :message="errors.is_available" />
                </div>

                <div class="grid gap-2">
                    <Label for="sort_order">Urutan tampil</Label>
                    <Input
                        id="sort_order"
                        name="sort_order"
                        type="number"
                        min="0"
                        class="mt-1 block w-full"
                        :default-value="roomType.sort_order"
                    />
                    <InputError class="mt-2" :message="errors.sort_order" />
                </div>
            </div>

            <div class="grid gap-2">
                <span class="text-sm font-medium">Fasilitas kamar</span>
                <p
                    v-if="facilities.length === 0"
                    class="text-sm text-muted-foreground"
                >
                    Belum ada fasilitas kamar.
                    <Link
                        :href="facilitiesIndex().url"
                        class="underline underline-offset-4"
                        >Tambah dulu di halaman Fasilitas.</Link
                    >
                </p>
                <div v-else class="grid gap-2 rounded-md border p-4">
                    <label
                        v-for="facility in facilities"
                        :key="facility.id"
                        class="flex cursor-pointer items-center gap-2 text-sm"
                    >
                        <input
                            type="checkbox"
                            name="facilities[]"
                            :value="facility.id"
                            :checked="selectedFacilities.includes(facility.id)"
                            class="border-input h-4 w-4 rounded"
                        />
                        {{ facility.name }}
                    </label>
                </div>
                <InputError class="mt-2" :message="errors.facilities" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Simpan</Button>
                <Button variant="outline" as-child>
                    <Link :href="index().url">Batal</Link>
                </Button>
            </div>
        </Form>

        <div class="flex flex-col space-y-4 border-t pt-6">
            <Heading
                variant="small"
                title="Galeri"
                description="Maksimal 6 gambar (JPG/PNG, 10 MB per file)"
            />

            <p
                v-if="images.length === 0"
                class="text-sm text-muted-foreground"
            >
                Belum ada gambar untuk tipe kamar ini.
            </p>
            <div v-else class="grid grid-cols-2 gap-4 md:grid-cols-3">
                <div
                    v-for="image in images"
                    :key="image.id"
                    class="overflow-hidden rounded-md border"
                >
                    <img
                        :src="image.url"
                        alt="Foto tipe kamar"
                        class="aspect-video w-full object-cover"
                    />
                    <div class="flex items-center justify-between gap-2 p-2">
                        <span
                            v-if="image.is_cover"
                            class="bg-primary text-primary-foreground rounded px-2 py-0.5 text-xs font-medium"
                            >Cover</span
                        >
                        <Button
                            v-else
                            variant="outline"
                            size="sm"
                            @click="setCoverImage(image)"
                            >Jadikan Cover</Button
                        >
                        <Button
                            variant="destructive"
                            size="sm"
                            @click="destroyImage(image)"
                            >Hapus</Button
                        >
                    </div>
                </div>
            </div>

            <Form
                v-if="images.length < 6"
                v-bind="RoomImageController.store.form(roomType)"
                class="space-y-2"
                v-slot="{ errors, processing: uploading }"
            >
                <Label for="images">Tambah gambar (bisa pilih banyak)</Label>
                <Input
                    id="images"
                    name="images[]"
                    type="file"
                    multiple
                    accept=".jpg,.jpeg,.png"
                    required
                    @change="onFilesSelected"
                />
                <p v-if="uploadError" class="text-sm text-red-600">
                    {{ uploadError }}
                </p>
                <InputError class="mt-2" :message="errors.images" />
                <Button
                    size="sm"
                    :disabled="uploading || uploadError !== ''"
                    >Upload</Button
                >
            </Form>
        </div>
    </div>
</template>
