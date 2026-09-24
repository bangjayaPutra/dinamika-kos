<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import WaButton from '@/components/WaButton.vue';
import { dashboard } from '@/routes';
import { index as facilitiesIndex } from '@/routes/public/facilities';
import { index as postsIndex } from '@/routes/public/posts';
import { index as roomTypesIndex } from '@/routes/public/room-types';

const page = usePage();
const settings = computed(() => page.props.siteSettings);
const isAdmin = computed(() => page.props.auth?.user?.role === 'admin');

const navLinks = [
    { title: 'Beranda', href: '/' },
    { title: 'Tipe Kamar', href: roomTypesIndex().url },
    { title: 'Fasilitas', href: facilitiesIndex().url },
    { title: 'Berita', href: postsIndex().url },
    { title: 'Kontak', href: '/kontak' },
];
</script>

<template>
    <div class="bg-background text-foreground flex min-h-screen flex-col">
        <Head :title="settings.site_name ?? 'Kos'" />

        <header class="sticky top-0 z-10 border-b bg-background/95 backdrop-blur">
            <div class="bg-gradient-to-r from-brand-500 via-brand-700 to-brand-900 h-1" />
            <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4">
                <Link href="/" class="text-brand-700 text-lg font-bold tracking-tight">
                    {{ settings.site_name ?? 'Kos' }}
                </Link>

                <nav class="hidden items-center gap-6 text-sm md:flex">
                    <Link
                        v-for="link in navLinks"
                        :key="link.title"
                        :href="link.href"
                        class="text-muted-foreground hover:text-brand-700 transition-colors"
                    >
                        {{ link.title }}
                    </Link>
                </nav>

                <div class="flex items-center gap-2">
                    <WaButton size="sm">Pesan Sekarang</WaButton>
                    <Link
                        v-if="isAdmin"
                        :href="dashboard()"
                        class="text-muted-foreground hover:text-brand-700 hidden text-sm sm:inline"
                    >
                        Admin
                    </Link>
                </div>
            </div>

            <nav class="flex gap-4 overflow-x-auto border-t px-4 py-2 text-sm md:hidden">
                <Link
                    v-for="link in navLinks"
                    :key="link.title"
                    :href="link.href"
                    class="text-muted-foreground hover:text-brand-700 whitespace-nowrap"
                >
                    {{ link.title }}
                </Link>
            </nav>
        </header>

        <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-8">
            <slot />
        </main>

        <footer class="bg-brand-900 text-white">
            <div class="mx-auto grid max-w-6xl gap-6 px-4 py-8 text-sm md:grid-cols-3">
                <div>
                    <p class="font-semibold">{{ settings.site_name ?? 'Kos' }}</p>
                    <p class="mt-1 text-white/70">
                        {{ settings.tagline ?? '' }}
                    </p>
                </div>
                <div>
                    <p class="font-semibold">Alamat</p>
                    <p class="mt-1 text-white/70">
                        {{ settings.address || '-' }}
                    </p>
                    <p class="mt-1 text-white/70">
                        {{ settings.operating_hours || '' }}
                    </p>
                    <a
                        v-if="settings.maps_url"
                        :href="settings.maps_url"
                        target="_blank"
                        rel="noopener"
                        class="mt-1 inline-block text-white underline decoration-white/50 underline-offset-2 hover:decoration-white"
                    >
                        Lihat di Google Maps
                    </a>
                </div>
                <div>
                    <p class="font-semibold">Hubungi kami</p>
                    <p class="mt-1 text-white/70">
                        WA: {{ settings.wa_number || '-' }}
                    </p>
                    <WaButton size="sm" class="mt-2 border border-white/40 bg-white/10 text-white shadow-none hover:bg-white/20">Chat WhatsApp</WaButton>
                </div>
            </div>
            <div class="border-t border-white/15 py-4 text-center text-xs text-white/70">
                © {{ new Date().getFullYear() }} {{ settings.site_name ?? 'Kos' }}
            </div>
        </footer>
    </div>
</template>
