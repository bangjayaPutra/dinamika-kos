<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { BedDouble, BookOpen, FolderGit2, Images, LayoutGrid, MessageSquare, Newspaper, Settings, Sparkles, Users } from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as facilitiesIndex } from '@/routes/admin/facilities';
import { index as carouselIndex } from '@/routes/admin/carousel-items';
import { index as postsIndex } from '@/routes/admin/posts';
import { index as reviewsIndex } from '@/routes/admin/reviews';
import { index as roomTypesIndex } from '@/routes/admin/room-types';
import { edit as settingsEdit } from '@/routes/admin/settings';
import { index as usersIndex } from '@/routes/admin/users';
import type { NavItem } from '@/types';

const page = usePage();
const isAdmin = computed(() => page.props.auth?.user?.role === 'admin');

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
];

const adminNavItems = computed<NavItem[]>(() => [
    {
        title: 'Tipe Kamar',
        href: roomTypesIndex(),
        icon: BedDouble,
    },
    {
        title: 'Fasilitas',
        href: facilitiesIndex(),
        icon: Sparkles,
    },
    {
        title: 'Ulasan',
        href: reviewsIndex(),
        icon: MessageSquare,
    },
    {
        title: 'Berita',
        href: postsIndex(),
        icon: Newspaper,
    },
    {
        title: 'Carousel',
        href: carouselIndex(),
        icon: Images,
    },
    {
        title: 'Pengaturan',
        href: settingsEdit(),
        icon: Settings,
    },
    {
        title: 'Pengguna',
        href: usersIndex(),
        icon: Users,
    },
]);
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" label="Platform" />
            <NavMain
                v-if="isAdmin"
                :items="adminNavItems"
                label="Admin"
            />
        </SidebarContent>

        <SidebarFooter>
           
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
