<script setup lang="ts">
import { Form, Head, Link, setLayoutProps } from '@inertiajs/vue3';
import UserController from '@/actions/App/Http/Controllers/Admin/UserController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index } from '@/routes/admin/users';

interface User {
    id: number;
    name: string;
    email: string;
    role: string;
}

const props = defineProps<{
    user: User;
}>();

setLayoutProps({
    breadcrumbs: [
        {
            title: 'Pengguna',
            href: index(),
        },
        {
            title: 'Ubah',
            href: UserController.edit(props.user.id).url,
        },
    ],
});
</script>

<template>
    <Head title="Ubah Akun" />

    <div class="flex max-w-2xl flex-col space-y-6">
        <Heading
            variant="small"
            title="Ubah Akun"
            description="Perbarui data akun dan role"
        />

        <Form
            v-bind="UserController.update.form(user.id)"
            class="space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-4 md:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="name">Nama</Label>
                    <Input
                        id="name"
                        name="name"
                        required
                        maxlength="255"
                        class="mt-1 block w-full"
                        :default-value="user.name"
                    />
                    <InputError class="mt-2" :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="role">Role</Label>
                    <select
                        id="role"
                        name="role"
                        required
                        class="border-input bg-background mt-1 block w-full rounded-md border px-3 py-2 text-sm"
                        :value="user.role"
                    >
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                    <InputError class="mt-2" :message="errors.role" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="email">Email</Label>
                <Input
                    id="email"
                    name="email"
                    type="email"
                    required
                    maxlength="255"
                    class="mt-1 block w-full"
                    :default-value="user.email"
                />
                <InputError class="mt-2" :message="errors.email" />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="password">Password baru</Label>
                    <PasswordInput
                        id="password"
                        name="password"
                        class="mt-1 block w-full"
                        autocomplete="new-password"
                        placeholder="Kosongkan jika tidak diubah"
                    />
                    <InputError class="mt-2" :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Konfirmasi Password</Label>
                    <PasswordInput
                        id="password_confirmation"
                        name="password_confirmation"
                        class="mt-1 block w-full"
                        autocomplete="new-password"
                    />
                </div>
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
