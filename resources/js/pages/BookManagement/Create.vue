<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { LoaderCircle, Save } from 'lucide-vue-next';
import { ref } from 'vue';

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
  jumlah: '',
  lokasi: '',
  deskripsi: '',
  kategori: '',
  cover: null as File | string | null,
  cover_type: 'upload',
});

const coverPreview = ref<string|null>(null);

function handleCoverChange(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0];
  if (file) {
    form.cover = file;
    coverPreview.value = URL.createObjectURL(file);
  } else {
    form.cover = null;
    coverPreview.value = null;
  }
}

function handleCoverUrlChange(e: Event) {
  const url = (e.target as HTMLInputElement).value;
  form.cover = url;
  coverPreview.value = url || null;
}

function submit() {
  form.post(route('books.store'), {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      coverPreview.value = null;
    }
  });
}
</script>

<template>
  <Head title="Tambah Buku" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Tambah Buku</h1>
      </div>
      <div class="rounded-md border p-6">
        <form @submit.prevent="submit" class="flex flex-col gap-6" enctype="multipart/form-data">
          <div class="grid gap-2">
            <Label for="judul">Judul <span class="text-red-500">*</span></Label>
            <Input id="judul" v-model="form.judul" required autofocus />
            <InputError :message="form.errors.judul" class="mt-2" />
          </div>
          <div class="grid gap-2">
            <Label for="penulis">Penulis</Label>
            <Input id="penulis" v-model="form.penulis" />
            <InputError :message="form.errors.penulis" class="mt-2" />
          </div>
          <div class="grid gap-2">
            <Label for="penerbit">Penerbit</Label>
            <Input id="penerbit" v-model="form.penerbit" />
            <InputError :message="form.errors.penerbit" class="mt-2" />
          </div>
          <div class="grid gap-2">
            <Label for="tahun_terbit">Tahun Terbit</Label>
            <Input id="tahun_terbit" v-model="form.tahun_terbit" type="number" />
            <InputError :message="form.errors.tahun_terbit" class="mt-2" />
          </div>
          <div class="grid gap-2">
            <Label for="isbn">ISBN</Label>
            <Input id="isbn" v-model="form.isbn" />
            <InputError :message="form.errors.isbn" class="mt-2" />
          </div>
          <div class="grid gap-2">
            <Label for="jumlah">Jumlah</Label>
            <Input id="jumlah" v-model="form.jumlah" type="number" />
            <InputError :message="form.errors.jumlah" class="mt-2" />
          </div>
          <div class="grid gap-2">
            <Label for="lokasi">Lokasi</Label>
            <Input id="lokasi" v-model="form.lokasi" />
            <InputError :message="form.errors.lokasi" class="mt-2" />
          </div>
          <div class="grid gap-2">
            <Label for="deskripsi">Deskripsi</Label>
            <Input id="deskripsi" v-model="form.deskripsi" />
            <InputError :message="form.errors.deskripsi" class="mt-2" />
          </div>
          <div class="grid gap-2">
            <Label for="kategori">Kategori</Label>
            <Input id="kategori" v-model="form.kategori" />
            <InputError :message="form.errors.kategori" class="mt-2" />
          </div>
          <div class="grid gap-2">
            <Label>Jenis Cover</Label>
            <div class="flex gap-4">
              <label class="flex items-center gap-1">
                <input type="radio" value="upload" v-model="form.cover_type" /> Upload
              </label>
              <label class="flex items-center gap-1">
                <input type="radio" value="url" v-model="form.cover_type" /> Link URL
              </label>
            </div>
          </div>
          <div class="grid gap-2" v-if="form.cover_type === 'upload'">
            <Label for="cover">Cover Buku</Label>
            <Input id="cover" type="file" accept="image/*" @change="handleCoverChange" />
            <InputError :message="form.errors.cover" class="mt-2" />
            <img v-if="coverPreview" :src="coverPreview" alt="Preview Cover" class="mt-2 max-h-40 rounded" />
          </div>
          <div class="grid gap-2" v-else>
            <Label for="cover_url">Link Cover Buku</Label>
            <Input id="cover_url" type="text" placeholder="https://..." @input="handleCoverUrlChange" />
            <InputError :message="form.errors.cover" class="mt-2" />
            <img v-if="coverPreview" :src="coverPreview" alt="Preview Cover" class="mt-2 max-h-40 rounded" />
          </div>
          <Button type="submit" :disabled="form.processing" class="mt-4 w-full flex items-center justify-center">
            <LoaderCircle v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
            <Save v-else class="mr-2 h-4 w-4" />
            Simpan
          </Button>
          <Link :href="route('books.index')">
            <Button type="button" variant="outline" class="w-full mt-2">Batal</Button>
          </Link>
        </form>
      </div>
    </div>
  </AppLayout>
</template> 