<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import FacilityController from '@/actions/App/Http/Controllers/Admin/FacilityController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { create, index } from '@/routes/admin/facilities';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Fasilitas',
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
    <Head title="Tambah Fasilitas" />

    <div class="flex max-w-2xl flex-col space-y-6">
        <Heading
            variant="small"
            title="Tambah Fasilitas"
            description="Isi detail fasilitas baru"
        />

        <Form
            v-bind="FacilityController.store.form()"
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
                    placeholder="WiFi"
                />
                <InputError class="mt-2" :message="errors.name" />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="scope">Scope</Label>
                    <select
                        id="scope"
                        name="scope"
                        class="border-input bg-background mt-1 block w-full rounded-md border px-3 py-2 text-sm"
                    >
                        <option value="kamar" selected>Kamar</option>
                        <option value="umum">Umum</option>
                    </select>
                    <InputError class="mt-2" :message="errors.scope" />
                </div>

                <div class="grid gap-2">
                    <Label for="icon">Ikon</Label>
                    <Input
                        id="icon"
                        name="icon"
                        class="mt-1 block w-full"
                        placeholder="wifi"
                    />
                    <InputError class="mt-2" :message="errors.icon" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="description">Deskripsi</Label>
                <textarea
                    id="description"
                    name="description"
                    rows="3"
                    placeholder="Deskripsi singkat fasilitas"
                    class="border-input bg-background mt-1 block w-full rounded-md border px-3 py-2 text-sm"
                />
                <InputError class="mt-2" :message="errors.description" />
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
