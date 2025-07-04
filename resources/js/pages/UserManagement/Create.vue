<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { LoaderCircle, Save, Plus, Upload, X, Image as ImageIcon } from 'lucide-vue-next';
import { ref, onMounted, onUnmounted, watch } from 'vue';

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
    role: 'murid', // Default role adalah murid
    tahun_angkatan: new Date().getFullYear(),
    jenis_kelamin: '',
    jurusan: '',
    nomor_telepon: '',
    foto_profil: null as File | null,
});

// Flag untuk mengetahui apakah username sudah diubah manual
const usernameManuallyChanged = ref(false);

// Flag untuk menentukan tindakan setelah submit
const addAnother = ref(false);

// Preview foto profil
const imagePreview = ref<string | null>(null);

// Ref untuk file input
const fileInput = ref<HTMLInputElement | null>(null);

// Fungsi untuk menangani upload file
const handleFileUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target && target.files && target.files.length > 0) {
        handleSelectedFile(target.files[0]);
    }
};

// Fungsi untuk menangani file yang dipilih
const handleSelectedFile = (file: File) => {
    if (!file.type.startsWith('image/')) {
        alert('Harap pilih file gambar.');
        return;
    }
    
    form.foto_profil = file;
    
    // Buat URL preview
    if (imagePreview.value) {
        URL.revokeObjectURL(imagePreview.value);
    }
    imagePreview.value = URL.createObjectURL(file);
};

// Fungsi untuk menghapus gambar
const removeImage = () => {
    form.foto_profil = null;
    if (imagePreview.value) {
        URL.revokeObjectURL(imagePreview.value);
        imagePreview.value = null;
    }
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

// Fungsi untuk menangani drag and drop
const isDragging = ref(false);
const handleDragEnter = (e: DragEvent) => {
    e.preventDefault();
    e.stopPropagation();
    isDragging.value = true;
};

const handleDragOver = (e: DragEvent) => {
    e.preventDefault();
    e.stopPropagation();
    isDragging.value = true;
};

const handleDragLeave = (e: DragEvent) => {
    e.preventDefault();
    e.stopPropagation();
    isDragging.value = false;
};

const handleDrop = (e: DragEvent) => {
    e.preventDefault();
    e.stopPropagation();
    isDragging.value = false;
    
    if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
        handleSelectedFile(e.dataTransfer.files[0]);
    }
};

// Watch perubahan pada nama untuk mengisi username otomatis
watch(() => form.name, (newName) => {
    // Jika username belum diubah manual, update berdasarkan nama
    if (!usernameManuallyChanged.value) {
        // Ambil karakter sampai spasi pertama dan konversi ke lowercase
        const firstWord = newName.split(' ')[0].toLowerCase();
        form.username = firstWord;
    }
});

// Fungsi untuk menandai bahwa username sudah diubah manual
const handleUsernameInput = () => {
    usernameManuallyChanged.value = true;
};

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
    if (imagePreview.value) {
        URL.revokeObjectURL(imagePreview.value);
    }
});

const submit = (addMore = false) => {
    addAnother.value = addMore;
    
    form.post(route('user-management.store'), {
        onSuccess: () => {
            if (addAnother.value) {
                // Reset form untuk entri baru
                form.reset('password');
                form.reset('name');
                form.reset('username');
                form.reset('jurusan');
                form.reset('jenis_kelamin');
                form.reset('nomor_telepon');
                form.reset('foto_profil');
                
                // Reset preview
                if (imagePreview.value) {
                    URL.revokeObjectURL(imagePreview.value);
                    imagePreview.value = null;
                }
                
                // Tetap mempertahankan role dan tahun_angkatan
                form.role = 'murid';
                form.tahun_angkatan = new Date().getFullYear();
                
                // Reset flag untuk username
                usernameManuallyChanged.value = false;
            }
        }
    });
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
                <form class="flex flex-col gap-6" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="grid gap-2">
                                <Label for="name">Nama</Label>
                                <Input id="name" v-model="form.name" type="text" required autofocus class="mt-1 block w-full h-10" />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="username">Username</Label>
                                <Input 
                                  id="username" 
                                  v-model="form.username" 
                                  @input="handleUsernameInput" 
                                  type="text" 
                                  required 
                                  class="mt-1 block w-full h-10" 
                                />
                                <InputError :message="form.errors.username" class="mt-2" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="password">Password</Label>
                                <Input id="password" v-model="form.password" type="password" required autocomplete="new-password" class="mt-1 block w-full h-10" />
                                <InputError :message="form.errors.password" class="mt-2" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="role">Role</Label>
                                <select id="role" v-model="form.role" required class="mt-1 block w-full h-10 px-3 py-2 rounded-md border border-input bg-background text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                                  <option value="" disabled>Pilih role</option>
                                  <option value="administrasi">Administrator</option>
                                  <option value="guru">Guru</option>
                                  <option value="murid" selected>Murid</option>
                                </select>
                                <InputError :message="form.errors.role" class="mt-2" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="tahun_angkatan">Tahun Angkatan</Label>
                                <Input id="tahun_angkatan" v-model="form.tahun_angkatan" type="number" min="0" max="2100" class="mt-1 block w-full h-10" />
                                <small class="text-gray-500">Gunakan 0 atau kosongkan untuk nilai "0000".</small>
                                <InputError :message="form.errors.tahun_angkatan" class="mt-2" />
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="grid gap-2">
                                <Label for="jenis_kelamin">Jenis Kelamin</Label>
                                <select id="jenis_kelamin" v-model="form.jenis_kelamin" class="mt-1 block w-full h-10 px-3 py-2 rounded-md border border-input bg-background text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                                    <option value="">Pilih</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                                <InputError :message="form.errors.jenis_kelamin" class="mt-2" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="jurusan">Jurusan</Label>
                                <Input id="jurusan" v-model="form.jurusan" type="text" class="mt-1 block w-full h-10" />
                                <InputError :message="form.errors.jurusan" class="mt-2" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="nomor_telepon">Nomor Telepon</Label>
                                <Input id="nomor_telepon" v-model="form.nomor_telepon" type="text" class="mt-1 block w-full h-10" />
                                <InputError :message="form.errors.nomor_telepon" class="mt-2" />
                            </div>
                            
                            <!-- Foto Profil yang Ditingkatkan dengan Drag & Drop -->
                            <div class="grid gap-2 row-span-2">
                                <Label for="foto_profil">Foto Profil</Label>
                                <div 
                                    class="mt-1 h-[160px] border-2 border-dashed rounded-md overflow-hidden relative transition-all flex flex-col justify-center items-center"
                                    :class="{ 'border-blue-500 bg-blue-50': isDragging }"
                                    @dragenter="handleDragEnter"
                                    @dragover="handleDragOver"
                                    @dragleave="handleDragLeave"
                                    @drop="handleDrop"
                                >
                                    <!-- Preview gambar jika ada -->
                                    <div v-if="imagePreview" class="h-full w-full flex items-center justify-center relative">
                                        <img :src="imagePreview" class="max-h-[150px] max-w-full object-contain" alt="Preview" />
                                        <button 
                                            type="button" 
                                            @click="removeImage" 
                                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors"
                                            title="Hapus Gambar"
                                        >
                                            <X class="h-4 w-4" />
                                        </button>
                                    </div>
                                    
                                    <!-- Area upload jika belum ada gambar -->
                                    <div v-else class="flex flex-col items-center justify-center p-4 text-center h-full">
                                        <ImageIcon class="h-10 w-10 text-gray-400 mb-2" />
                                        <p class="text-sm text-gray-600 mb-2">Drag & drop foto di sini atau</p>
                                        <Button 
                                            type="button" 
                                            variant="outline" 
                                            size="sm"
                                            @click="() => fileInput?.click()"
                                            class="text-xs"
                                        >
                                            <Upload class="h-3 w-3 mr-1" />
                                            Pilih File
                                        </Button>
                                    </div>
                                    
                                    <!-- Input file yang tersembunyi -->
                                    <input 
                                        ref="fileInput"
                                        type="file" 
                                        id="foto_profil" 
                                        @change="handleFileUpload"
                                        accept="image/*" 
                                        class="hidden" 
                                    />
                                </div>
                                <small class="text-gray-500">Upload foto dengan ukuran maksimal 2MB.</small>
                                <InputError :message="form.errors.foto_profil" class="mt-2" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end items-center gap-4">
                        <Button 
                            type="button" 
                            @click="submit(true)" 
                            :disabled="form.processing" 
                            variant="secondary" 
                            class="flex items-center justify-center"
                        >
                            <LoaderCircle v-if="form.processing && addAnother" class="mr-2 h-4 w-4 animate-spin" />
                            <Plus v-else class="mr-2 h-4 w-4" />
                            Simpan dan Tambah Lagi
                        </Button>
                        <Button 
                            type="button" 
                            @click="submit(false)" 
                            :disabled="form.processing" 
                            class="flex items-center justify-center"
                        >
                            <LoaderCircle v-if="form.processing && !addAnother" class="mr-2 h-4 w-4 animate-spin" />
                            <Save v-else class="mr-2 h-4 w-4" />
                            Simpan
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Tambahan styling jika diperlukan */
</style> 