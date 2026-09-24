<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import SettingController from '@/actions/App/Http/Controllers/Admin/SettingController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit } from '@/routes/admin/settings';

defineProps<{
    settings: Record<string, string | null>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Pengaturan',
                href: edit(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Pengaturan" />

    <div class="flex max-w-2xl flex-col space-y-6">
        <Heading
            variant="small"
            title="Pengaturan"
            description="Informasi umum situs yang tampil di halaman publik"
        />

        <Form
            v-bind="SettingController.update.form()"
            class="space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="site_name">Nama situs</Label>
                <Input
                    id="site_name"
                    name="site_name"
                    class="mt-1 block w-full"
                    required
                    :default-value="settings.site_name ?? ''"
                />
                <InputError class="mt-2" :message="errors.site_name" />
            </div>

            <div class="grid gap-2">
                <Label for="tagline">Slogan</Label>
                <Input
                    id="tagline"
                    name="tagline"
                    class="mt-1 block w-full"
                    :default-value="settings.tagline ?? ''"
                />
                <InputError class="mt-2" :message="errors.tagline" />
            </div>

            <div class="grid gap-2">
                <Label for="welcome_text">Teks sambutan</Label>
                <textarea
                    id="welcome_text"
                    name="welcome_text"
                    rows="3"
                    :value="settings.welcome_text ?? ''"
                    placeholder="Sambutan singkat di beranda"
                    class="border-input bg-background mt-1 block w-full rounded-md border px-3 py-2 text-sm"
                />
                <InputError class="mt-2" :message="errors.welcome_text" />
            </div>

            <div class="grid gap-2">
                <Label for="address">Alamat</Label>
                <textarea
                    id="address"
                    name="address"
                    rows="2"
                    :value="settings.address ?? ''"
                    class="border-input bg-background mt-1 block w-full rounded-md border px-3 py-2 text-sm"
                />
                <InputError class="mt-2" :message="errors.address" />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="wa_number">Nomor WhatsApp</Label>
                    <Input
                        id="wa_number"
                        name="wa_number"
                        class="mt-1 block w-full"
                        placeholder="6281234567890"
                        :default-value="settings.wa_number ?? ''"
                    />
                    <InputError class="mt-2" :message="errors.wa_number" />
                </div>

                <div class="grid gap-2">
                    <Label for="operating_hours">Jam operasional</Label>
                    <Input
                        id="operating_hours"
                        name="operating_hours"
                        class="mt-1 block w-full"
                        placeholder="Senin–Sabtu, 08.00–20.00"
                        :default-value="settings.operating_hours ?? ''"
                    />
                    <InputError
                        class="mt-2"
                        :message="errors.operating_hours"
                    />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="maps_url">Tautan Google Maps</Label>
                <Input
                    id="maps_url"
                    name="maps_url"
                    type="url"
                    class="mt-1 block w-full"
                    placeholder="https://maps.google.com/..."
                    :default-value="settings.maps_url ?? ''"
                />
                <InputError class="mt-2" :message="errors.maps_url" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Simpan</Button>
                <Button variant="outline" as-child>
                    <Link :href="edit().url">Muat ulang</Link>
                </Button>
            </div>
        </Form>
    </div>
</template>
