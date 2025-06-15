<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Edit, Trash2, UserPlus } from 'lucide-vue-next';
import { computed } from 'vue';
import Pagination from '@/components/Pagination.vue';

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
    }
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Manajemen User',
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
    <Head title="Manajemen User" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">Manajemen User</h1>
                <Link :href="route('user-management.create')">
                    <Button>
                        <UserPlus class="mr-2 h-4 w-4" />
                        Tambah User
                    </Button>
                </Link>
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
                            <TableCell colspan="9" class="text-center py-6">Tidak ada data user</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <div v-if="users.last_page > 1" class="px-2 sm:px-0 overflow-x-auto py-2">
                <Pagination 
                    :current-page="users.current_page"
                    :total-pages="users.last_page"
                    @page-change="page => router.get(route('user-management.index'), { page }, { preserveState: true, preserveScroll: true })"
                />
            </div>
        </div>
    </AppLayout>
</template> 