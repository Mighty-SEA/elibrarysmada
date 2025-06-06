<template>
  <Head title="Manajemen Buku" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Manajemen Buku</h1>
        <div class="flex gap-2">
          <Dialog>
            <DialogTrigger as-child>
              <Button type="button">Import</Button>
            </DialogTrigger>
            <DialogContent>
              <DialogHeader>
                <DialogTitle>Import Buku</DialogTitle>
                <DialogDescription>Upload file Excel (.xlsx/.xls) sesuai template export.</DialogDescription>
              </DialogHeader>
              <form :action="route('books.import')" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_token" :value="csrf" />
                <input type="file" name="file" accept=".xlsx,.xls" required class="mb-4" />
                <DialogFooter>
                  <Button type="submit">Upload</Button>
                  <DialogClose as-child>
                    <Button type="button" variant="outline">Batal</Button>
                  </DialogClose>
                </DialogFooter>
              </form>
            </DialogContent>
          </Dialog>
          <Button type="button" @click="exportBooks">Export</Button>
          <Link :href="route('books.create')">
            <Button>
              <BookPlus class="mr-2 h-4 w-4" />
              Tambah Buku
            </Button>
          </Link>
        </div>
      </div>
      <div class="rounded-md border">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Cover</TableHead>
              <TableHead>Judul</TableHead>
              <TableHead>Penulis</TableHead>
              <TableHead>Penerbit</TableHead>
              <TableHead>Tahun</TableHead>
              <TableHead>Kategori</TableHead>
              <TableHead>Jumlah</TableHead>
              <TableHead class="text-right">Aksi</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="book in props.books" :key="book.id">
              <TableCell>
                <img v-if="book.cover_url" :src="book.cover_url" alt="Cover" class="w-12 h-16 object-cover rounded shadow" />
                <span v-else class="text-gray-400 italic">Tidak ada</span>
              </TableCell>
              <TableCell>{{ book.judul }}</TableCell>
              <TableCell>{{ book.penulis }}</TableCell>
              <TableCell>{{ book.penerbit }}</TableCell>
              <TableCell>{{ book.tahun_terbit }}</TableCell>
              <TableCell>{{ book.kategori }}</TableCell>
              <TableCell>{{ book.jumlah }}</TableCell>
              <TableCell class="text-right">
                <div class="flex justify-end space-x-2">
                  <Link :href="route('books.edit', book.id)">
                    <Button variant="outline" size="icon">
                      <Edit class="h-4 w-4" />
                    </Button>
                  </Link>
                  <Button variant="destructive" size="icon" @click="deleteBook(book.id)">
                    <Trash2 class="h-4 w-4" />
                  </Button>
                </div>
              </TableCell>
            </TableRow>
            <TableRow v-if="props.books.length === 0">
              <TableCell colspan="7" class="text-center py-6">Tidak ada data buku</TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Edit, Trash2, BookPlus } from 'lucide-vue-next';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogTrigger from '@/components/ui/dialog/DialogTrigger.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import DialogDescription from '@/components/ui/dialog/DialogDescription.vue';
import DialogFooter from '@/components/ui/dialog/DialogFooter.vue';
import DialogClose from '@/components/ui/dialog/DialogClose.vue';

const props = defineProps<{ books: any[] }>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: route('dashboard'),
  },
  {
    title: 'Manajemen Buku',
    href: route('books.index'),
  },
];

const deleteBook = (id: number) => {
  if (confirm('Yakin ingin menghapus buku ini?')) {
    router.delete(route('books.destroy', id));
  }
};

let csrf = '';
const meta = document.querySelector('meta[name="csrf-token"]');
if (meta) {
  csrf = meta.getAttribute('content') || '';
}

function exportBooks() {
  window.location.href = route('books.export');
}
</script> 