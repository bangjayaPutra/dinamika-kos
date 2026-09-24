<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import FacilityController from '@/actions/App/Http/Controllers/Admin/FacilityController';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { index } from '@/routes/admin/facilities';

interface Facility {
    id: number;
    name: string;
    icon: string | null;
    description: string | null;
    scope: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedFacilities {
    data: Facility[];
    links: PaginationLink[];
    meta: {
        total: number;
    };
}

defineProps<{
    facilities: PaginatedFacilities;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Fasilitas',
                href: index(),
            },
        ],
    },
});

function destroyFacility(facility: Facility) {
    if (confirm(`Hapus fasilitas "${facility.name}"?`)) {
        router.delete(FacilityController.destroy(facility).url);
    }
}
</script>

<template>
    <Head title="Fasilitas" />

    <div class="flex flex-col space-y-6">
        <div class="flex items-center justify-between">
            <Heading
                variant="small"
                title="Fasilitas"
                description="Kelola fasilitas kamar dan fasilitas umum"
            />
            <Button as-child>
                <Link :href="FacilityController.create().url">Tambah</Link>
            </Button>
        </div>

        <div class="overflow-x-auto rounded-xl border">
            <table class="w-full text-sm">
                <thead class="bg-muted/50 text-left">
                    <tr>
                        <th class="px-4 py-3 font-medium">Nama</th>
                        <th class="px-4 py-3 font-medium">Scope</th>
                        <th class="px-4 py-3 font-medium">Deskripsi</th>
                        <th class="px-4 py-3 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="facility in facilities.data"
                        :key="facility.id"
                        class="border-t"
                    >
                        <td class="px-4 py-3 font-medium">
                            {{ facility.name }}
                        </td>
                        <td class="px-4 py-3">
                            {{ facility.scope === 'kamar' ? 'Kamar' : 'Umum' }}
                        </td>
                        <td class="px-4 py-3">
                            {{ facility.description ?? '-' }}
                        </td>
                        <td
                            class="flex justify-end gap-2 px-4 py-3 whitespace-nowrap"
                        >
                            <Button variant="outline" size="sm" as-child>
                                <Link
                                    :href="
                                        FacilityController.edit(facility).url
                                    "
                                    >Ubah</Link
                                >
                            </Button>
                            <Button
                                variant="destructive"
                                size="sm"
                                @click="destroyFacility(facility)"
                            >
                                Hapus
                            </Button>
                        </td>
                    </tr>
                    <tr v-if="facilities.data.length === 0">
                        <td
                            colspan="4"
                            class="text-muted-foreground px-4 py-8 text-center"
                        >
                            Belum ada fasilitas.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="facilities.links.length > 3" class="flex flex-wrap gap-1">
            <template v-for="link in facilities.links" :key="link.label">
                <Button
                    v-if="link.url"
                    variant="outline"
                    size="sm"
                    :disabled="link.active"
                    as-child
                >
                    <Link :href="link.url" v-html="link.label" />
                </Button>
                <span
                    v-else
                    class="text-muted-foreground inline-flex h-8 items-center px-2 text-sm"
                    v-html="link.label"
                />
            </template>
        </div>
    </div>
</template>
