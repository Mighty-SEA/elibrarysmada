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
      
      <!-- Bulk Actions -->
      <div v-if="selectedBooks.length > 0" class="flex items-center justify-between mb-4 p-3 bg-background rounded-md border shadow-sm">
        <div class="flex items-center gap-3">
          <span class="font-medium text-sm">{{ selectedBooks.length }} buku dipilih</span>
          <Button v-if="!isAllBooksSelected" size="sm" variant="ghost" @click="selectAllBooks" :disabled="isBulkProcessing || isLoadingAllIds">
            <CheckCircle class="h-4 w-4 mr-1" />
            <span v-if="isLoadingAllIds">Loading...</span>
            <span v-else>Pilih Semua</span>
          </Button>
        </div>
        <div class="flex items-center gap-2">
          <!-- Bulk Update Dialog -->
          <Dialog>
            <DialogTrigger as-child>
              <Button size="sm" variant="secondary">
                Update Jumlah
              </Button>
            </DialogTrigger>
            <DialogContent>
              <DialogHeader>
                <DialogTitle>Update Jumlah Buku</DialogTitle>
                <DialogDescription>Set jumlah untuk {{ selectedBooks.length }} buku yang dipilih.</DialogDescription>
              </DialogHeader>
              <form @submit.prevent="bulkUpdateJumlah">
                <div class="grid gap-4 py-4">
                  <div class="grid gap-2">
                    <Label for="jumlah">Jumlah Buku</Label>
                    <Input id="jumlah" type="number" v-model="bulkJumlah" min="0" required />
                  </div>
                </div>
                <DialogFooter>
                  <Button type="submit" :disabled="isBulkProcessing">
                    <LoaderCircle v-if="isBulkProcessing" class="mr-2 h-4 w-4 animate-spin" />
                    Update
                  </Button>
                  <DialogClose as-child>
                    <Button type="button" variant="outline">Batal</Button>
                  </DialogClose>
                </DialogFooter>
              </form>
            </DialogContent>
          </Dialog>
          
          <!-- Bulk Delete Button -->
          <Button 
            size="sm" 
            variant="destructive" 
            @click="confirmBulkDelete = true"
            :disabled="isBulkProcessing"
          >
            <Trash2 class="h-4 w-4 mr-1" />
            Hapus
          </Button>
          
          <!-- Batalkan Pilihan -->
          <Button 
            size="sm" 
            variant="outline" 
            @click="selectedBooks = []"
            :disabled="isBulkProcessing"
          >
            <X class="h-4 w-4 mr-1" />
            Batalkan
          </Button>
        </div>
      </div>
      
      <div class="rounded-md border">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead class="w-10">
                <input 
                  type="checkbox" 
                  @change="toggleSelectAll" 
                  :checked="isAllSelected" 
                  class="h-4 w-4 rounded border-gray-300"
                  :disabled="!props.books.data || props.books.data.length === 0"
                />
              </TableHead>
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
            <TableRow v-for="book in props.books.data" :key="book.id">
              <TableCell>
                <input 
                  type="checkbox" 
                  :value="book.id" 
                  v-model="selectedBooks" 
                  class="h-4 w-4 rounded border-gray-300"
                />
              </TableCell>
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
            <TableRow v-if="!props.books.data || props.books.data.length === 0">
              <TableCell colspan="8" class="text-center py-6">Tidak ada data buku</TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
      
      <!-- Pagination -->
      <div v-if="props.books.last_page > 1" class="mt-4">
        <Pagination 
          :current-page="props.books.current_page"
          :total-pages="props.books.last_page"
          @page-change="changePage"
        />
      </div>
    </div>
  </AppLayout>
  
  <!-- Konfirmasi Bulk Delete Dialog -->
  <Dialog v-model:open="confirmBulkDelete">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>Konfirmasi Hapus</DialogTitle>
        <DialogDescription>
          Anda yakin ingin menghapus {{ selectedBooks.length }} buku yang dipilih? Tindakan ini tidak dapat dibatalkan.
        </DialogDescription>
      </DialogHeader>
      <DialogFooter>
        <Button variant="destructive" @click="bulkDelete" :disabled="isBulkProcessing">
          <LoaderCircle v-if="isBulkProcessing" class="mr-2 h-4 w-4 animate-spin" />
          <Trash2 v-else class="mr-2 h-4 w-4" />
          Hapus
        </Button>
        <Button variant="outline" @click="confirmBulkDelete = false" :disabled="isBulkProcessing">Batal</Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Edit, Trash2, BookPlus, X, LoaderCircle, CheckCircle } from 'lucide-vue-next';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogTrigger from '@/components/ui/dialog/DialogTrigger.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import DialogDescription from '@/components/ui/dialog/DialogDescription.vue';
import DialogFooter from '@/components/ui/dialog/DialogFooter.vue';
import DialogClose from '@/components/ui/dialog/DialogClose.vue';
import Pagination from '@/components/Pagination.vue';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { ref, computed } from 'vue';
import axios from 'axios';

interface PaginatedBooks {
  data: any[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  links: any[];
}

const props = defineProps<{ books: PaginatedBooks }>();

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

// Tambahkan fungsi untuk pindah halaman
function changePage(page: number) {
  router.get(route('books.index'), { page }, {
    preserveState: true,
    preserveScroll: true
  });
}

// Bulk actions state
const selectedBooks = ref<number[]>([]);
const confirmBulkDelete = ref(false);
const bulkJumlah = ref<number>(1);
const isBulkProcessing = ref(false);
const isLoadingAllIds = ref(false);
const allBookIds = ref<number[]>([]);
const isAllBooksSelected = computed(() => {
  return allBookIds.value.length > 0 && 
         allBookIds.value.length === selectedBooks.value.length && 
         allBookIds.value.every(id => selectedBooks.value.includes(id));
});

// Check if all books in current page are selected
const isAllSelected = computed(() => {
  if (!props.books.data || props.books.data.length === 0) return false;
  return props.books.data.every(book => selectedBooks.value.includes(book.id));
});

// Toggle select all books in current page
function toggleSelectAll(event: Event) {
  const target = event.target as HTMLInputElement;
  if (target.checked) {
    selectedBooks.value = props.books.data.map(book => book.id);
  } else {
    selectedBooks.value = [];
  }
}

// Ambil semua ID buku
function selectAllBooks() {
  if (isLoadingAllIds.value) return;
  
  if (allBookIds.value.length > 0) {
    // Jika ID sudah tersedia, langsung pilih semua
    selectedBooks.value = [...allBookIds.value];
  } else {
    // Ambil semua ID dari server
    isLoadingAllIds.value = true;
    fetch(route('books.all-ids'))
      .then(response => response.json())
      .then(data => {
        allBookIds.value = data.ids;
        selectedBooks.value = [...data.ids];
        isLoadingAllIds.value = false;
      })
      .catch(error => {
        console.error('Error fetching all book IDs:', error);
        isLoadingAllIds.value = false;
      });
  }
}

// Bulk delete books
function bulkDelete() {
  if (selectedBooks.value.length === 0) return;
  
  isBulkProcessing.value = true;
  
  axios.delete(route('books.bulk-delete'), {
    data: {
      ids: selectedBooks.value
    },
    headers: {
      'X-CSRF-TOKEN': csrf
    }
  })
  .then(() => {
    selectedBooks.value = [];
    confirmBulkDelete.value = false;
    // Refresh halaman setelah operasi berhasil
    window.location.reload();
  })
  .catch(error => {
    console.error('Error deleting books:', error);
    alert('Gagal menghapus buku. Silakan coba lagi.');
  })
  .finally(() => {
    isBulkProcessing.value = false;
  });
}

// Bulk update jumlah
function bulkUpdateJumlah() {
  if (selectedBooks.value.length === 0) return;
  
  isBulkProcessing.value = true;
  
  axios.post('/admin/books/bulk-update-jumlah', {
    ids: selectedBooks.value,
    jumlah: bulkJumlah.value
  }, {
    headers: {
      'X-CSRF-TOKEN': csrf
    }
  })
  .then(() => {
    selectedBooks.value = [];
    // Refresh halaman setelah operasi berhasil
    window.location.reload();
  })
  .catch(error => {
    console.error('Error updating book quantity:', error);
    alert('Gagal mengupdate jumlah buku. Silakan coba lagi.');
  })
  .finally(() => {
    isBulkProcessing.value = false;
  });
}
</script> 