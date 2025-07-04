<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { LoaderCircle, Save, Plus, Upload, X, Image as ImageIcon } from 'lucide-vue-next';
import { ref, onUnmounted, watch } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Manajemen Buku', href: '/books' },
  { title: 'Tambah Buku', href: '/books/create' },
];

const form = useForm({
  judul: '',
  penulis: '',
  penerbit: '',
  tahun_terbit: '',
  isbn: '',
  eksemplar: '',
  ketersediaan: '',
  no_panggil: '',
  asal_koleksi: '',
  kota_terbit: '',
  deskripsi: '',
  kategori: '',
  cover: null as File | string | null,
  cover_type: 'upload',
});

const coverPreview = ref<string|null>(null);
const fileInput = ref<HTMLInputElement | null>(null);

// Flag untuk menentukan tindakan setelah submit
const addAnother = ref(false);

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
  
  form.cover = file;
  
  // Buat URL preview
  if (coverPreview.value) {
    URL.revokeObjectURL(coverPreview.value);
  }
  coverPreview.value = URL.createObjectURL(file);
};

// Fungsi untuk menghapus gambar
const removeImage = () => {
  form.cover = null;
  if (coverPreview.value) {
    URL.revokeObjectURL(coverPreview.value);
    coverPreview.value = null;
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

// Fungsi untuk menangani perubahan URL cover
const handleCoverUrlChange = (e: Event) => {
  const url = (e.target as HTMLInputElement).value;
  form.cover = url;
  coverPreview.value = url || null;
};

onUnmounted(() => {
  if (coverPreview.value) {
    URL.revokeObjectURL(coverPreview.value);
  }
});

// Watch perubahan pada eksemplar untuk menyesuaikan ketersediaan
watch(() => form.eksemplar, (newValue) => {
  form.ketersediaan = newValue;
});

const submit = (addMore = false) => {
  addAnother.value = addMore;
  
  // Pastikan ketersediaan sama dengan eksemplar
  form.ketersediaan = form.eksemplar;
  
  form.post(route('books.store'), {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      if (addAnother.value) {
        // Reset form untuk entri baru
        form.reset('judul');
        form.reset('penulis');
        form.reset('penerbit');
        form.reset('tahun_terbit');
        form.reset('isbn');
        form.reset('eksemplar');
        form.reset('ketersediaan');
        form.reset('no_panggil');
        form.reset('asal_koleksi');
        form.reset('kota_terbit');
        form.reset('deskripsi');
        form.reset('kategori');
        form.reset('cover');
        
        // Reset preview
        if (coverPreview.value) {
          URL.revokeObjectURL(coverPreview.value);
          coverPreview.value = null;
        }
        
        // Tetap mempertahankan cover_type
        form.cover_type = 'upload';
      }
    }
  });
};
</script>

<template>
  <Head title="Tambah Buku" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col p-4">
      <div class="flex justify-between items-center mb-2">
        <h1 class="text-xl font-bold">Tambah Buku</h1>
      </div>
      <div class="rounded-md border p-4">
        <form class="flex flex-col" enctype="multipart/form-data">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2">
            <!-- Kolom 1 -->
            <div class="space-y-2">
              <div class="grid gap-1">
                <Label for="judul" class="text-sm">Judul <span class="text-red-500">*</span></Label>
                <Input id="judul" v-model="form.judul" type="text" required autofocus class="h-8" />
                <InputError :message="form.errors.judul" class="text-xs" />
              </div>
              <div class="grid gap-1">
                <Label for="penulis" class="text-sm">Penulis</Label>
                <Input id="penulis" v-model="form.penulis" type="text" class="h-8" />
                <InputError :message="form.errors.penulis" class="text-xs" />
              </div>
              <div class="grid gap-1">
                <Label for="penerbit" class="text-sm">Penerbit</Label>
                <Input id="penerbit" v-model="form.penerbit" type="text" class="h-8" />
                <InputError :message="form.errors.penerbit" class="text-xs" />
              </div>
              <div class="grid gap-1">
                <Label for="tahun_terbit" class="text-sm">Tahun Terbit</Label>
                <Input id="tahun_terbit" v-model="form.tahun_terbit" type="number" class="h-8" />
                <InputError :message="form.errors.tahun_terbit" class="text-xs" />
              </div>
              <div class="grid gap-1">
                <Label for="isbn" class="text-sm">ISBN</Label>
                <Input id="isbn" v-model="form.isbn" type="text" class="h-8" />
                <InputError :message="form.errors.isbn" class="text-xs" />
              </div>
              <div class="grid gap-1">
                <Label for="eksemplar" class="text-sm">Eksemplar</Label>
                <Input id="eksemplar" v-model="form.eksemplar" type="number" class="h-8" />
                <small class="text-xs text-gray-500">Ketersediaan akan otomatis sama dengan jumlah eksemplar.</small>
                <InputError :message="form.errors.eksemplar" class="text-xs" />
              </div>
            </div>
            
            <!-- Kolom 2 -->
            <div class="space-y-2">
              <div class="grid gap-1">
                <Label for="no_panggil" class="text-sm">No. Panggil</Label>
                <Input id="no_panggil" v-model="form.no_panggil" type="text" class="h-8" />
                <InputError :message="form.errors.no_panggil" class="text-xs" />
              </div>
              <div class="grid gap-1">
                <Label for="asal_koleksi" class="text-sm">Asal Koleksi</Label>
                <Input id="asal_koleksi" v-model="form.asal_koleksi" type="text" class="h-8" />
                <InputError :message="form.errors.asal_koleksi" class="text-xs" />
              </div>
              <div class="grid gap-1">
                <Label for="kota_terbit" class="text-sm">Kota Terbit</Label>
                <Input id="kota_terbit" v-model="form.kota_terbit" type="text" class="h-8" />
                <InputError :message="form.errors.kota_terbit" class="text-xs" />
              </div>
              <div class="grid gap-1">
                <Label for="deskripsi" class="text-sm">Deskripsi</Label>
                <Input id="deskripsi" v-model="form.deskripsi" type="text" class="h-8" />
                <InputError :message="form.errors.deskripsi" class="text-xs" />
              </div>
              <div class="grid gap-1">
                <Label for="kategori" class="text-sm">Kategori</Label>
                <Input id="kategori" v-model="form.kategori" type="text" class="h-8" placeholder="Dipisahkan dengan koma" />
                <InputError :message="form.errors.kategori" class="text-xs" />
              </div>
            </div>
          </div>
          
          <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-if="form.cover_type === 'upload'" class="grid gap-1">
              <div class="flex items-center justify-between">
                <Label for="cover" class="text-sm">Cover Buku</Label>
                <div class="flex gap-3">
                  <label class="flex items-center gap-1">
                    <input type="radio" value="upload" v-model="form.cover_type" class="h-3 w-3" /> 
                    <span class="text-xs">Upload</span>
                  </label>
                  <label class="flex items-center gap-1">
                    <input type="radio" value="url" v-model="form.cover_type" class="h-3 w-3" /> 
                    <span class="text-xs">URL</span>
                  </label>
                </div>
              </div>
              <div 
                class="h-[120px] border-2 border-dashed rounded-md overflow-hidden relative transition-all flex flex-col justify-center items-center"
                :class="{ 'border-blue-500 bg-blue-50': isDragging }"
                @dragenter="handleDragEnter"
                @dragover="handleDragOver"
                @dragleave="handleDragLeave"
                @drop="handleDrop"
              >
                <!-- Preview gambar jika ada -->
                <div v-if="coverPreview" class="h-full w-full flex items-center justify-center relative">
                  <img :src="coverPreview" class="max-h-[110px] max-w-full object-contain" alt="Preview" />
                  <button 
                    type="button" 
                    @click="removeImage" 
                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors"
                    title="Hapus Gambar"
                  >
                    <X class="h-3 w-3" />
                  </button>
                </div>
                
                <!-- Area upload jika belum ada gambar -->
                <div v-else class="flex flex-col items-center justify-center p-2 text-center h-full">
                  <ImageIcon class="h-6 w-6 text-gray-400 mb-1" />
                  <p class="text-xs text-gray-600 mb-1">Drag & drop foto cover di sini atau</p>
                  <Button 
                    type="button" 
                    variant="outline" 
                    size="sm"
                    @click="() => fileInput?.click()"
                    class="text-xs h-6 px-2"
                  >
                    <Upload class="h-3 w-3 mr-1" />
                    Pilih File
                  </Button>
                </div>
                
                <!-- Input file yang tersembunyi -->
                <input 
                  ref="fileInput"
                  type="file" 
                  id="cover" 
                  @change="handleFileUpload"
                  accept="image/*" 
                  class="hidden" 
                />
              </div>
              <small class="text-xs text-gray-500">Maks. 2MB</small>
              <InputError :message="form.errors.cover" class="text-xs" />
            </div>
            
            <div v-else class="grid gap-1">
              <div class="flex items-center justify-between">
                <Label for="cover_url" class="text-sm">Link Cover Buku</Label>
                <div class="flex gap-3">
                  <label class="flex items-center gap-1">
                    <input type="radio" value="upload" v-model="form.cover_type" class="h-3 w-3" /> 
                    <span class="text-xs">Upload</span>
                  </label>
                  <label class="flex items-center gap-1">
                    <input type="radio" value="url" v-model="form.cover_type" class="h-3 w-3" /> 
                    <span class="text-xs">URL</span>
                  </label>
                </div>
              </div>
              <Input id="cover_url" type="text" v-model="form.cover" placeholder="https://..." class="h-8" />
              <div class="h-[90px] overflow-hidden flex items-center justify-center">
                <img v-if="coverPreview" :src="coverPreview" alt="Preview Cover" class="max-h-[85px] rounded" />
              </div>
              <InputError :message="form.errors.cover" class="text-xs" />
            </div>
            
            <div class="flex items-center justify-end gap-2 md:mt-8">
              <Button 
                type="button" 
                @click="submit(true)" 
                :disabled="form.processing" 
                variant="secondary" 
                size="sm"
                class="h-8 text-xs"
              >
                <LoaderCircle v-if="form.processing && addAnother" class="mr-1 h-3 w-3 animate-spin" />
                <Plus v-else class="mr-1 h-3 w-3" />
                Simpan & Tambah Lagi
              </Button>
              <Button 
                type="button" 
                @click="submit(false)" 
                :disabled="form.processing" 
                size="sm"
                class="h-8 text-xs"
              >
                <LoaderCircle v-if="form.processing && !addAnother" class="mr-1 h-3 w-3 animate-spin" />
                <Save v-else class="mr-1 h-3 w-3" />
                Simpan
              </Button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Tambahan styling jika diperlukan */
</style> 