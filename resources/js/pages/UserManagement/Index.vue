<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Edit, Trash2, UserPlus, Search, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import Pagination from '@/components/Pagination.vue';
import { Input } from '@/components/ui/input';
import { debounce } from 'lodash';

defineProps<{
    users: {
        data: Array<{
            id: number;
            name: string;
            username: string;
            role: string;
            jenis_kelamin?: string;
            jurusan?: string;
            nomor_telepon?: string;
            foto_profil?: string;
        }>;
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: any[];
    },
    filters: {
        search: string;
    }
}>();

const search = ref('');

// Mengisi nilai awal pencarian dari filter
search.value = route().params.search || '';

// Debounce pencarian
const debouncedSearch = debounce(() => {
    router.get(route('user-management.index'), { search: search.value }, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
}, 500);

// Watch perubahan pada search
watch(search, () => {
    debouncedSearch();
});

// Fungsi untuk menghapus pencarian
const clearSearch = () => {
    search.value = '';
    router.get(route('user-management.index'), {}, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Manajemen Anggota',
        href: '/user-management',
    },
];

const roleLabels = computed(() => {
    return {
        'administrasi': 'Administrator',
        'guru': 'Guru',
        'murid': 'Murid'
    } as {[key: string]: string};
});

const deleteUser = (id: number) => {
    if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
        router.delete(route('user-management.destroy', id));
    }
};
</script>

<template>
    <Head title="Manajemen Anggota" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">Manajemen Anggota</h1>
                <div class="flex space-x-2">
                    <Link :href="route('user-management.archives')">
                        <Button variant="outline">
                            <span class="mr-2">🗄️</span>
                            Lihat Arsip
                        </Button>
                    </Link>
                    <Link :href="route('user-management.create')">
                        <Button>
                            <UserPlus class="mr-2 h-4 w-4" />
                            Tambah Anggota
                        </Button>
                    </Link>
                </div>
            </div>
            
            <!-- Komponen Pencarian -->
            <div class="mb-4 relative">
                <div class="flex">
                    <div class="relative flex-1">
                        <Input 
                            v-model="search"
                            placeholder="Cari anggota berdasarkan nama, username, ID, role atau jurusan..."
                            class="pl-10 pr-10"
                        />
                        <div class="absolute left-0 top-0 h-full flex items-center pl-3">
                            <Search class="h-4 w-4 text-gray-400" />
                        </div>
                        <button 
                            v-if="search"
                            @click="clearSearch"
                            class="absolute right-0 top-0 h-full flex items-center pr-3"
                        >
                            <X class="h-4 w-4 text-gray-400" />
                        </button>
                    </div>
                </div>
                <div v-if="users.total > 0" class="text-sm text-gray-500 mt-2">
                    Menampilkan {{ users.total }} hasil pencarian
                </div>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-12">No</TableHead>
                            <TableHead>ID</TableHead>
                            <TableHead>Foto</TableHead>
                            <TableHead>Nama</TableHead>
                            <TableHead>Username</TableHead>
                            <TableHead>Jenis Kelamin</TableHead>
                            <TableHead>Jurusan</TableHead>
                            <TableHead>Nomor Telepon</TableHead>
                            <TableHead>Role</TableHead>
                            <TableHead class="text-right">Aksi</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="(user, idx) in users.data" :key="user.id">
                            <TableCell>{{ idx + 1 + ((users.current_page - 1) * users.per_page) }}</TableCell>
                            <TableCell>{{ user.id }}</TableCell>
                            <TableCell>
                                <img v-if="user.foto_profil" :src="'/storage/' + user.foto_profil" alt="Foto Profil" class="h-10 w-10 rounded-full object-cover border" />
                                <span v-else class="text-gray-400 italic">-</span>
                            </TableCell>
                            <TableCell>{{ user.name }}</TableCell>
                            <TableCell>{{ user.username }}</TableCell>
                            <TableCell>{{ user.jenis_kelamin || '-' }}</TableCell>
                            <TableCell>{{ user.jurusan || '-' }}</TableCell>
                            <TableCell>{{ user.nomor_telepon || '-' }}</TableCell>
                            <TableCell>{{ roleLabels[user.role] }}</TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end space-x-2">
                                    <Link :href="route('user-management.edit', user.id)">
                                        <Button variant="outline" size="icon">
                                            <Edit class="h-4 w-4" />
                                        </Button>
                                    </Link>
                                    <Button variant="destructive" size="icon" @click="deleteUser(user.id)">
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="users.data.length === 0">
                            <TableCell colspan="9" class="text-center py-6">Tidak ada data anggota</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <div v-if="users.last_page > 1" class="px-2 sm:px-0 overflow-x-auto py-2">
                <Pagination 
                    :current-page="users.current_page"
                    :total-pages="users.last_page"
                    @page-change="page => router.get(route('user-management.index'), { page, search }, { preserveState: true, preserveScroll: true })"
                />
            </div>
        </div>
    </AppLayout>
</template> 