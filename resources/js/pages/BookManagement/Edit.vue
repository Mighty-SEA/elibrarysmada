<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { LoaderCircle, Save } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{ book: any }>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Manajemen Buku', href: '/books' },
  { title: 'Edit Buku', href: `/books/${props.book.id}/edit` },
];

const form = useForm({
  judul: props.book.judul ?? '',
  penulis: props.book.penulis ?? '',
  penerbit: props.book.penerbit ?? '',
  tahun_terbit: props.book.tahun_terbit ?? '',
  isbn: props.book.isbn ?? '',
  jumlah: props.book.jumlah ?? '',
  lokasi: props.book.lokasi ?? '',
  deskripsi: props.book.deskripsi ?? '',
  kategori: props.book.kategori ?? '',
  cover: props.book.cover_type === 'url' ? props.book.cover : null,
  cover_type: props.book.cover_type || 'upload',
  _method: 'PUT',
});

const coverPreview = ref<string|null>(props.book.cover_type === 'url' ? props.book.cover : (props.book.cover_url || null));

watch(() => form.cover_type, (newType) => {
  if (newType === 'url') {
    form.cover = typeof props.book.cover === 'string' ? props.book.cover : '';
    coverPreview.value = typeof props.book.cover === 'string' ? props.book.cover : null;
  } else {
    form.cover = null;
    coverPreview.value = props.book.cover_url || null;
  }
});

function handleCoverChange(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0];
  if (file) {
    form.cover = file;
    coverPreview.value = URL.createObjectURL(file);
  } else {
    form.cover = null;
    coverPreview.value = props.book.cover_url || null;
  }
}

form.transform(data => ({
  ...data,
  judul: form.judul || props.book.judul || '',
  penulis: form.penulis ?? props.book.penulis ?? '',
  penerbit: form.penerbit ?? props.book.penerbit ?? '',
  tahun_terbit: form.tahun_terbit ?? props.book.tahun_terbit ?? '',
  isbn: form.isbn ?? props.book.isbn ?? '',
  jumlah: form.jumlah ?? props.book.jumlah ?? '',
  lokasi: form.lokasi ?? props.book.lokasi ?? '',
  deskripsi: form.deskripsi ?? props.book.deskripsi ?? '',
  kategori: form.kategori ?? props.book.kategori ?? '',
}));

function submit() {
  form.post(route('books.update', props.book.id), {
    forceFormData: true,
    preserveScroll: true,
  });
}
</script>

<template>
  <Head title="Edit Buku" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Edit Buku</h1>
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
            <Label for="cover_type">Tipe Cover</Label>
            <select id="cover_type" v-model="form.cover_type">
              <option value="upload">Upload</option>
              <option value="url">URL</option>
            </select>
            <InputError :message="form.errors.cover_type" class="mt-2" />
          </div>
          <div class="grid gap-2" v-if="form.cover_type === 'upload'">
            <Label for="cover">Cover Buku</Label>
            <Input id="cover" type="file" accept="image/*" @change="handleCoverChange" />
            <InputError :message="form.errors.cover" class="mt-2" />
            <img v-if="coverPreview" :src="coverPreview" alt="Preview Cover" class="mt-2 max-h-40 rounded" />
          </div>
          <div class="grid gap-2" v-else>
            <Label for="cover_url">Link Cover Buku</Label>
            <Input id="cover_url" type="text" v-model="form.cover" />
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