<template>
  <AppLayout>
    <template #header>
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <Heading>Sampah Anggota</Heading>
      </div>
    </template>
    
    <div class="grid gap-6">
      <Card>
        <CardHeader>
          <CardTitle>Daftar Anggota yang Telah Dihapus</CardTitle>
          <CardDescription>
            Ini adalah daftar anggota yang telah dihapus (soft delete). Anda dapat memulihkan anggota jika diperlukan.
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div class="mb-4 flex w-full max-w-sm flex-col gap-4 md:max-w-md">
            <div class="flex gap-2">
              <Input
                v-model="search"
                placeholder="Cari berdasarkan nama, username, id, role, jurusan..."
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
                  <TableHead>Nama</TableHead>
                  <TableHead>Username</TableHead>
                  <TableHead>Role</TableHead>
                  <TableHead>Jurusan</TableHead>
                  <TableHead>Dihapus Pada</TableHead>
                  <TableHead>Aksi</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="user in users.data" :key="user.id">
                  <TableCell>{{ user.id }}</TableCell>
                  <TableCell>{{ user.name }}</TableCell>
                  <TableCell>{{ user.username }}</TableCell>
                  <TableCell>{{ user.role }}</TableCell>
                  <TableCell>{{ user.jurusan || '-' }}</TableCell>
                  <TableCell>{{ formatDate(user.deleted_at) }}</TableCell>
                  <TableCell>
                    <Button 
                      variant="outline" 
                      size="sm" 
                      @click="restoreUser(user)"
                      class="mr-2"
                    >
                      Pulihkan
                    </Button>
                  </TableCell>
                </TableRow>
                <TableRow v-if="!users.data.length">
                  <TableCell colspan="7" class="text-center">Tidak ada data anggota yang dihapus</TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>

          <div class="mt-4">
            <Pagination
              v-if="users.total > users.per_page"
              :links="users.links"
              :from="users.from"
              :to="users.to"
              :total="users.total"
            />
          </div>
        </CardContent>
        <CardFooter class="flex justify-between">
          <Button variant="outline" @click="$router.push({ name: 'user-management.index' })">
            Kembali ke Daftar Anggota
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
  users: Object,
  filters: Object,
})

const search = ref(props.filters.search)

function filter() {
  router.get(route('user-management.recycle'), { search: search.value }, {
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

function restoreUser(user) {
  if (confirm(`Apakah Anda yakin ingin memulihkan anggota "${user.name}"?`)) {
    router.post(route('user-management.restore', { id: user.id }))
  }
}
</script> 