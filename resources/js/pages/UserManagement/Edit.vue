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
    username: string;
    role: string;
    jenis_kelamin?: string;
    jurusan?: string;
    nomor_telepon?: string;
    foto_profil?: string;
    tahun_angkatan?: number;
}

const props = defineProps<{ user: User }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Manajemen Anggota', href: '/user-management' },
    { title: 'Edit Anggota', href: `/user-management/${props.user.id}/edit` },
];

const form = useForm({
    _method: 'PUT',
    name: props.user.name || '',
    username: props.user.username || '',
    password: '',
    password_confirmation: '',
    role: props.user.role || '',
    tahun_angkatan: props.user.tahun_angkatan || new Date().getFullYear(),
    jenis_kelamin: props.user.jenis_kelamin || '',
    jurusan: props.user.jurusan || '',
    nomor_telepon: props.user.nomor_telepon || '',
    foto_profil: null as File | null,
});

const submit = () => {
    form.post(route('user-management.update', props.user.id), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.password = '';
            form.password_confirmation = '';
            form.foto_profil = null;
        }
    });
};
</script>

<template>
    <Head title="Edit Anggota" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">Edit Anggota</h1>
            </div>

            <div class="rounded-md border p-6">
                <form @submit.prevent="submit" class="flex flex-col gap-6" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="grid gap-2">
                                <Label for="name">Nama</Label>
                                <Input id="name" v-model="form.name" type="text" required autofocus class="mt-1 block w-full" />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="username">Username</Label>
                                <Input id="username" v-model="form.username" type="text" required class="mt-1 block w-full" />
                                <InputError :message="form.errors.username" class="mt-2" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="password">Password (kosongkan jika tidak ingin mengubah)</Label>
                                <Input id="password" v-model="form.password" type="password" autocomplete="new-password" class="mt-1 block w-full" />
                                <InputError :message="form.errors.password" class="mt-2" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="password_confirmation">Konfirmasi Password</Label>
                                <Input id="password_confirmation" v-model="form.password_confirmation" type="password" autocomplete="new-password" class="mt-1 block w-full" />
                                <InputError :message="form.errors.password_confirmation" class="mt-2" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="role">Role</Label>
                                <select id="role" v-model="form.role" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                  <option value="" disabled>Pilih role</option>
                                  <option value="administrasi">Administrator</option>
                                  <option value="guru">Guru</option>
                                  <option value="murid">Murid</option>
                                </select>
                                <InputError :message="form.errors.role" class="mt-2" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="tahun_angkatan">Tahun Angkatan</Label>
                                <Input id="tahun_angkatan" v-model="form.tahun_angkatan" type="number" min="2000" max="2100" required class="mt-1 block w-full" />
                                <small class="text-yellow-600">Perubahan tahun angkatan akan mengubah ID user</small>
                                <InputError :message="form.errors.tahun_angkatan" class="mt-2" />
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="grid gap-2">
                                <Label for="jenis_kelamin">Jenis Kelamin</Label>
                                <select id="jenis_kelamin" v-model="form.jenis_kelamin" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Pilih</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                                <InputError :message="form.errors.jenis_kelamin" class="mt-2" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="jurusan">Jurusan</Label>
                                <Input id="jurusan" v-model="form.jurusan" type="text" class="mt-1 block w-full" />
                                <InputError :message="form.errors.jurusan" class="mt-2" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="nomor_telepon">Nomor Telepon</Label>
                                <Input id="nomor_telepon" v-model="form.nomor_telepon" type="text" class="mt-1 block w-full" />
                                <InputError :message="form.errors.nomor_telepon" class="mt-2" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="foto_profil">Foto Profil</Label>
                                <input id="foto_profil" type="file" @change="e => {
                                    const target = e.target as HTMLInputElement;
                                    if (target && target.files) {
                                        form.foto_profil = target.files[0];
                                    }
                                }" accept="image/*" class="mt-1 block w-full" />
                                <div v-if="props.user.foto_profil" class="mt-2">
                                    <img :src="'/storage/' + props.user.foto_profil" alt="Foto Profil" class="h-16 w-16 rounded-full object-cover border" />
                                </div>
                                <InputError :message="form.errors.foto_profil" class="mt-2" />
                            </div>
                        </div>
                    </div>
                    
                    <Button type="submit" :disabled="form.processing" class="mt-6 w-full flex items-center justify-center">
                        <LoaderCircle v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                        <Save v-else class="mr-2 h-4 w-4" />
                        Simpan
                    </Button>
                </form>
            </div>
        </div>
    </AppLayout>
</template> 