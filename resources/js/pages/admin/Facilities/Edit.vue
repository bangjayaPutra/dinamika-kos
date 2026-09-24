<script setup lang="ts">
import { Form, Head, Link, setLayoutProps } from '@inertiajs/vue3';
import FacilityController from '@/actions/App/Http/Controllers/Admin/FacilityController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index } from '@/routes/admin/facilities';

interface Facility {
    id: number;
    name: string;
    icon: string | null;
    description: string | null;
    scope: string;
}

const props = defineProps<{
    facility: Facility;
}>();

setLayoutProps({
    breadcrumbs: [
        {
            title: 'Fasilitas',
            href: index(),
        },
        {
            title: 'Ubah',
            href: FacilityController.edit(props.facility).url,
        },
    ],
});
</script>

<template>
    <Head title="Ubah Fasilitas" />

    <div class="flex max-w-2xl flex-col space-y-6">
        <Heading
            variant="small"
            title="Ubah Fasilitas"
            description="Perbarui detail fasilitas"
        />

        <Form
            v-bind="FacilityController.update.form(facility)"
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
                    :default-value="facility.name"
                />
                <InputError class="mt-2" :message="errors.name" />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="scope">Scope</Label>
                    <select
                        id="scope"
                        name="scope"
                        :value="facility.scope"
                        class="border-input bg-background mt-1 block w-full rounded-md border px-3 py-2 text-sm"
                    >
                        <option value="kamar">Kamar</option>
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
                        :default-value="facility.icon ?? ''"
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
                    :value="facility.description ?? ''"
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
