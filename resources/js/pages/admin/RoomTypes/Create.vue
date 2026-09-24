<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import RoomTypeController from '@/actions/App/Http/Controllers/Admin/RoomTypeController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { create, index } from '@/routes/admin/room-types';
import { index as facilitiesIndex } from '@/routes/admin/facilities';

interface FacilityOption {
    id: number;
    name: string;
}

defineProps<{
    facilities: FacilityOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Tipe Kamar',
                href: index(),
            },
            {
                title: 'Tambah',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Tambah Tipe Kamar" />

    <div class="flex max-w-2xl flex-col space-y-6">
        <Heading
            variant="small"
            title="Tambah Tipe Kamar"
            description="Isi detail tipe kamar baru"
        />

        <Form
            v-bind="RoomTypeController.store.form()"
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
                    placeholder="Kamar Standar"
                />
                <InputError class="mt-2" :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="description">Deskripsi</Label>
                <textarea
                    id="description"
                    name="description"
                    rows="3"
                    placeholder="Deskripsi singkat tipe kamar"
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
                        placeholder="1500000"
                    />
                    <InputError class="mt-2" :message="errors.price_monthly" />
                </div>

                <div class="grid gap-2">
                    <Label for="size_label">Ukuran</Label>
                    <Input
                        id="size_label"
                        name="size_label"
                        class="mt-1 block w-full"
                        placeholder="3x4 m"
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
                        placeholder="1"
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
                        placeholder="10"
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
                        class="border-input bg-background mt-1 block w-full rounded-md border px-3 py-2 text-sm"
                    >
                        <option value="1" selected>Ya</option>
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
                        placeholder="0"
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
    </div>
</template>
