<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { LoaderCircle, Save } from 'lucide-vue-next';

interface User {
    id: number;
    name: string;
    email: string;
    role: string;
}

const props = defineProps<{
    user: User;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Manajemen User',
        href: '/user-management',
    },
    {
        title: 'Edit User',
        href: `/user-management/${props.user.id}/edit`,
    },
];

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    role: props.user.role,
});

const submit = () => {
    form.put(route('user-management.update', props.user.id));
};
</script>

<template>
    <Head title="Edit User" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">Edit User</h1>
            </div>

            <div class="rounded-md border p-6">
                <form @submit.prevent="submit" class="flex flex-col gap-6">
                    <div class="grid gap-2">
                        <Label for="name">Nama</Label>
                        <Input id="name" v-model="form.name" type="text" required autofocus class="mt-1 block w-full" />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="email">Email</Label>
                        <Input id="email" v-model="form.email" type="email" required class="mt-1 block w-full" />
                        <InputError :message="form.errors.email" class="mt-2" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="password">Password (kosongkan jika tidak ingin mengubah)</Label>
                        <Input id="password" v-model="form.password" type="password" class="mt-1 block w-full" />
                        <InputError :message="form.errors.password" class="mt-2" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="password_confirmation">Konfirmasi Password</Label>
                        <Input id="password_confirmation" v-model="form.password_confirmation" type="password" class="mt-1 block w-full" />
                        <InputError :message="form.errors.password_confirmation" class="mt-2" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="role">Role</Label>
                        <select id="role" v-model="form.role" required class="mt-1 block w-full">
                          <option value="" disabled>Pilih role</option>
                          <option value="administrasi">Administrator</option>
                          <option value="guru">Guru</option>
                          <option value="murid">Murid</option>
                        </select>
                        <InputError :message="form.errors.role" class="mt-2" />
                    </div>
                    <Button type="submit" :disabled="form.processing" class="mt-4 w-full flex items-center justify-center">
                        <LoaderCircle v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                        <Save v-else class="mr-2 h-4 w-4" />
                        Simpan
                    </Button>
                </form>
            </div>
        </div>
    </AppLayout>
</template> 