<template>
  <AppLayout>
    <template #header>
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <Heading>Arsip Buku</Heading>
      </div>
    </template>
    
    <div class="grid gap-6">
      <Card>
        <CardHeader>
          <CardTitle>Daftar Buku yang Telah Dihapus</CardTitle>
          <CardDescription>
            Ini adalah daftar buku yang telah dihapus (soft delete). Anda dapat memulihkan buku jika diperlukan.
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div class="mb-4 flex w-full max-w-sm flex-col gap-4 md:max-w-md">
            <div class="flex gap-2">
              <Input
                v-model="search"
                placeholder="Cari berdasarkan judul, penulis, penerbit, tahun terbit..."
                class="flex-1"
                @keyup.enter="filter"
              />
              <Button variant="outline" @click="filter">Cari</Button>
            </div>
          </div>

          <div class="rounded-md border">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>ID</TableHead>
                  <TableHead>Cover</TableHead>
                  <TableHead>Judul</TableHead>
                  <TableHead>Penulis</TableHead>
                  <TableHead>Penerbit</TableHead>
                  <TableHead>Tahun</TableHead>
                  <TableHead>Dihapus Pada</TableHead>
                  <TableHead>Aksi</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="book in books.data" :key="book.id">
                  <TableCell>{{ book.id }}</TableCell>
                  <TableCell>
                    <img
                      v-if="book.cover_url"
                      :src="book.cover_url"
                      alt="Cover buku"
                      class="h-12 w-8 object-cover"
                    />
                    <div v-else class="h-12 w-8 bg-gray-100 flex items-center justify-center text-xs text-gray-500">
                      No Cover
                    </div>
                  </TableCell>
                  <TableCell>{{ book.judul }}</TableCell>
                  <TableCell>{{ book.penulis || '-' }}</TableCell>
                  <TableCell>{{ book.penerbit || '-' }}</TableCell>
                  <TableCell>{{ book.tahun_terbit || '-' }}</TableCell>
                  <TableCell>{{ formatDate(book.deleted_at) }}</TableCell>
                  <TableCell>
                    <Button 
                      variant="outline" 
                      size="sm" 
                      @click="restoreBook(book)"
                      class="mr-2"
                    >
                      Pulihkan
                    </Button>
                  </TableCell>
                </TableRow>
                <TableRow v-if="!books.data.length">
                  <TableCell colspan="8" class="text-center">Tidak ada data buku yang dihapus</TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>

          <div class="mt-4">
            <Pagination
              v-if="books.total > books.per_page"
              :links="books.links"
              :from="books.from"
              :to="books.to"
              :total="books.total"
            />
          </div>
        </CardContent>
        <CardFooter class="flex justify-between">
          <Button variant="outline" @click="$router.push({ name: 'books.index' })">
            Kembali ke Daftar Buku
          </Button>
        </CardFooter>
      </Card>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { debounce } from 'lodash'
import AppLayout from '@/layouts/AppLayout.vue'
import Heading from '@/components/Heading.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Input } from '@/components/ui/input'
import Pagination from '@/components/Pagination.vue'

const props = defineProps({
  books: Object,
  filters: Object,
})

const search = ref(props.filters.search)

function filter() {
  router.get(route('books.archives'), { search: search.value }, {
    preserveState: true,
    replace: true,
  })
}

const debouncedFilter = debounce(() => {
  filter()
}, 300)

watch(search, () => {
  debouncedFilter()
})

function formatDate(dateString) {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', { 
    day: '2-digit', 
    month: 'long', 
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit' 
  })
}

function restoreBook(book) {
  if (confirm(`Apakah Anda yakin ingin memulihkan buku "${book.judul}"?`)) {
    router.post(route('books.restore', { id: book.id }))
  }
}
</script> 