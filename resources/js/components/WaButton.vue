<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { store as waClickStore } from '@/routes/wa-clicks';

const props = withDefaults(
    defineProps<{
        roomTypeId?: number | null;
        message?: string;
        size?: 'sm' | 'default';
    }>(),
    {
        roomTypeId: null,
        message: '',
        size: 'default',
    },
);

const page = usePage();
const waNumber = computed(() => page.props.siteSettings.wa_number);

function openWhatsApp() {
    if (!waNumber.value) {
        return;
    }

    router.post(
        waClickStore().url,
        {
            room_type_id: props.roomTypeId,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                const text = props.message
                    ? `?text=${encodeURIComponent(props.message)}`
                    : '';
                window.open(
                    `https://wa.me/${waNumber.value}${text}`,
                    '_blank',
                    'noopener',
                );
            },
        },
    );
}
</script>

<template>
    <Button
        :size="size"
        :disabled="!waNumber"
        :title="waNumber ? 'Chat via WhatsApp' : 'Nomor WhatsApp belum diatur'"
        class="bg-brand-700 text-white shadow-sm hover:bg-brand-800"
        @click="openWhatsApp"
    >
        <slot>Chat WhatsApp</slot>
    </Button>
</template>
