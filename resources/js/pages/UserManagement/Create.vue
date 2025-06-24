<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { LoaderCircle, Save } from 'lucide-vue-next';
import { ref, onMounted, onUnmounted } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Manajemen Anggota',
        href: '/user-management',
    },
    {
        title: 'Tambah Anggota',
        href: '/user-management/create',
    },
];

const form = useForm({
    name: '',
    username: '',
    password: '',
    password_confirmation: '',
    role: '',
    tahun_angkatan: new Date().getFullYear(),
    jenis_kelamin: '',
    jurusan: '',
    nomor_telepon: '',
    foto_profil: null as File | null,
});

const selectRef = ref(null);
const showSelectContent = ref(false);

const handleClickOutside = (event: MouseEvent) => {
    if (selectRef.value && !(selectRef.value as HTMLElement).contains(event.target as Node)) {
        showSelectContent.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

const submit = () => {
    form.post(route('user-management.store'));
};
</script>

<template>
    <Head title="Tambah Anggota" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">Tambah Anggota</h1>
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
                                <Label for="password">Password</Label>
                                <Input id="password" v-model="form.password" type="password" required autocomplete="new-password" class="mt-1 block w-full" />
                                <InputError :message="form.errors.password" class="mt-2" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="password_confirmation">Konfirmasi Password</Label>
                                <Input id="password_confirmation" v-model="form.password_confirmation" type="password" required autocomplete="new-password" class="mt-1 block w-full" />
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