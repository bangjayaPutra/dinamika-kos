<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import WaButton from '@/components/WaButton.vue';

const page = usePage();
const settings = computed(() => page.props.siteSettings);
</script>

<template>
    <Head title="Kontak" />

    <div class="mx-auto flex max-w-2xl flex-col gap-6">
        <div>
            <h1 class="flex items-center gap-2 text-3xl font-bold tracking-tight"><span class="bg-brand-600 h-7 w-1.5 rounded-full" />Kontak</h1>
            <p class="text-muted-foreground mt-1">
                Hubungi kami untuk survei atau pemesanan kamar
            </p>
        </div>

        <div class="border-brand-100 space-y-4 rounded-xl border p-6">
            <div>
                <p class="text-sm font-semibold">Alamat</p>
                <p class="text-muted-foreground mt-1 text-sm">
                    {{ settings.address || '-' }}
                </p>
                <a
                    v-if="settings.maps_url"
                    :href="settings.maps_url"
                    target="_blank"
                    rel="noopener"
                    class="text-brand-700 mt-1 inline-block text-sm underline underline-offset-2 hover:text-brand-800"
                >
                    Buka di Google Maps
                </a>
            </div>

            <div>
                <p class="text-sm font-semibold">Jam operasional</p>
                <p class="text-muted-foreground mt-1 text-sm">
                    {{ settings.operating_hours || '-' }}
                </p>
            </div>

            <div>
                <p class="text-sm font-semibold">WhatsApp</p>
                <p class="text-muted-foreground mt-1 text-sm">
                    {{ settings.wa_number || '-' }}
                </p>
                <WaButton
                    size="sm"
                    class="mt-2"
                    :message="`Halo ${settings.site_name ?? ''}, saya ingin bertanya tentang kos.`"
                >
                    Chat Sekarang
                </WaButton>
            </div>
        </div>
    </div>
</template>
