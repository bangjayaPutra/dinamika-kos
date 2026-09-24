<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import UserController from '@/actions/App/Http/Controllers/Admin/UserController';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { index } from '@/routes/admin/users';

interface User {
    id: number;
    name: string;
    email: string;
    role: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedUsers {
    data: User[];
    links: PaginationLink[];
}

defineProps<{
    users: PaginatedUsers;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Pengguna',
                href: index(),
            },
        ],
    },
});

const page = usePage();
const currentUserId = computed(() => page.props.auth?.user?.id);

function destroyUser(user: User) {
    if (confirm(`Hapus akun "${user.name}"?`)) {
        router.delete(UserController.destroy(user.id).url, { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="Pengguna" />

    <div class="flex flex-col space-y-6">
        <div class="flex items-center justify-between">
            <Heading
                variant="small"
                title="Pengguna"
                description="Kelola akun admin dan pengguna"
            />
            <Button as-child>
                <Link :href="UserController.create().url">Tambah</Link>
            </Button>
        </div>

        <div class="overflow-x-auto rounded-xl border">
            <table class="w-full text-sm">
                <thead class="bg-muted/50 text-left">
                    <tr>
                        <th class="px-4 py-3 font-medium">Nama</th>
                        <th class="px-4 py-3 font-medium">Email</th>
                        <th class="px-4 py-3 font-medium">Role</th>
                        <th class="px-4 py-3 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="user in users.data"
                        :key="user.id"
                        class="border-t"
                    >
                        <td class="px-4 py-3 font-medium">
                            {{ user.name }}
                            <span
                                v-if="user.id === currentUserId"
                                class="text-muted-foreground ml-1 text-xs font-normal"
                                >(kamu)</span
                            >
                        </td>
                        <td class="px-4 py-3">{{ user.email }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span
                                v-if="user.role === 'admin'"
                                class="rounded bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700"
                                >Admin</span
                            >
                            <span
                                v-else
                                class="rounded bg-muted px-2 py-0.5 text-xs font-medium text-muted-foreground"
                                >User</span
                            >
                        </td>
                        <td
                            class="flex justify-end gap-2 px-4 py-3 whitespace-nowrap"
                        >
                            <Button variant="outline" size="sm" as-child>
                                <Link :href="UserController.edit(user.id).url"
                                    >Ubah</Link
                                >
                            </Button>
                            <Button
                                variant="destructive"
                                size="sm"
                                @click="destroyUser(user)"
                            >
                                Hapus
                            </Button>
                        </td>
                    </tr>
                    <tr v-if="users.data.length === 0">
                        <td
                            colspan="4"
                            class="text-muted-foreground px-4 py-8 text-center"
                        >
                            Belum ada akun.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="users.links.length > 3" class="flex flex-wrap gap-1">
            <template v-for="link in users.links" :key="link.label">
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
