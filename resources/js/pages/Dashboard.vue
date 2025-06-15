<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { LayoutGrid, Book, BookOpen, Users, BookMarked, Clock, CheckCircle2, AlertTriangle } from 'lucide-vue-next';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const page = usePage();
const totalBooks = page.props.totalBooks ?? 0;
const totalLoans = page.props.totalLoans ?? 0;
const pendingRequests = page.props.pendingRequests ?? 0;
const totalUsers = page.props.totalUsers ?? 0;
const booksAdded = page.props.booksAdded;
const booksDeleted = page.props.booksDeleted;

const startDate = ref(page.props.startDate || '');
const endDate = ref(page.props.endDate || '');

function filterByDate() {
    router.get(route('dashboard'), {
        start: startDate.value,
        end: endDate.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}

function resetDateFilter() {
    startDate.value = '';
    endDate.value = '';
    router.get(route('dashboard'), {}, {
        preserveState: true,
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Dashboard Administrasi</h1>
            
            <!-- Filter Tanggal Compact -->
            <div class="flex flex-wrap items-center gap-2 mb-4 bg-blue-50 border border-blue-100 rounded-lg px-4 py-2 shadow-sm">
                <div class="flex items-center gap-1">
                    <span class="text-blue-600"><svg xmlns='http://www.w3.org/2000/svg' class='h-4 w-4' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'/></svg></span>
                    <input type="date" v-model="startDate" class="border border-blue-200 rounded px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 bg-white" />
                </div>
                <span class="text-gray-500 text-sm">-</span>
                <div class="flex items-center gap-1">
                    <span class="text-blue-600"><svg xmlns='http://www.w3.org/2000/svg' class='h-4 w-4' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'/></svg></span>
                    <input type="date" v-model="endDate" class="border border-blue-200 rounded px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 bg-white" />
                </div>
                <button @click="filterByDate" class="inline-flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white rounded px-3 py-1.5 text-sm font-semibold transition shadow-sm">
                    <svg xmlns='http://www.w3.org/2000/svg' class='h-4 w-4' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 13l4 4L19 7'/></svg>
                    Filter
                </button>
                <button @click="resetDateFilter" class="inline-flex items-center gap-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded px-3 py-1.5 text-sm font-semibold transition shadow-sm border border-gray-300">
                    <svg xmlns='http://www.w3.org/2000/svg' class='h-4 w-4' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M6 18L18 6M6 6l12 12'/></svg>
                    Seluruh Data
                </button>
            </div>
            
            <!-- Stats Cards -->
            <div class="grid gap-4 grid-cols-1 md:grid-cols-4">
                <!-- Total Buku -->
                <div class="bg-white rounded-xl border border-sidebar-border/70 p-4 shadow-sm flex flex-col">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-medium text-gray-700">Total Buku</h3>
                        <Book class="h-6 w-6 text-blue-500" />
                    </div>
                    <p class="text-3xl font-bold text-gray-900">{{ totalBooks }}</p>
                    <p class="text-sm text-gray-500 mt-2">Tersedia di perpustakaan</p>
                    <template v-if="startDate || endDate">
                        <p class="text-xs mt-1 text-gray-600">
                            <span v-if="booksAdded !== null">+ {{ booksAdded }} buku ditambah</span>
                            <span v-if="booksDeleted !== null && booksDeleted > 0">, - {{ booksDeleted }} buku dihapus</span>
                        </p>
                    </template>
                </div>
                
                <!-- Total Peminjaman -->
                <div class="bg-white rounded-xl border border-sidebar-border/70 p-4 shadow-sm flex flex-col">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-medium text-gray-700">Total Peminjaman</h3>
                        <BookOpen class="h-6 w-6 text-green-500" />
                    </div>
                    <p class="text-3xl font-bold text-gray-900">{{ totalLoans }}</p>
                    <p class="text-sm text-gray-500 mt-2">
                        <template v-if="startDate || endDate">
                            Peminjaman pada rentang tanggal
                        </template>
                        <template v-else>
                            Total peminjaman
                        </template>
                    </p>
                </div>
                
                <!-- Menunggu Persetujuan -->
                <div class="bg-white rounded-xl border border-sidebar-border/70 p-4 shadow-sm flex flex-col">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-medium text-gray-700">Permintaan Baru</h3>
                        <Clock class="h-6 w-6 text-yellow-500" />
                    </div>
                    <p class="text-3xl font-bold text-gray-900">{{ pendingRequests }}</p>
                    <p class="text-sm text-gray-500 mt-2">Menunggu persetujuan</p>
                </div>
                
                <!-- Total Pengguna -->
                <div class="bg-white rounded-xl border border-sidebar-border/70 p-4 shadow-sm flex flex-col">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-medium text-gray-700">Total Pengguna</h3>
                        <Users class="h-6 w-6 text-purple-500" />
                    </div>
                    <p class="text-3xl font-bold text-gray-900">{{ totalUsers }}</p>
                    <p class="text-sm text-gray-500 mt-2">Terdaftar di sistem</p>
                </div>
            </div>
            
            <!-- Quick Access Section -->
            <div class="mt-6 grid gap-4 grid-cols-1 md:grid-cols-2">
                <!-- Manajemen Peminjaman -->
                <div class="bg-white rounded-xl border border-sidebar-border/70 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-900">Manajemen Peminjaman</h2>
                        <BookMarked class="h-7 w-7 text-blue-500" />
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                        <Link 
                            :href="route('loans.pending')"
                            class="flex flex-col gap-2 p-4 border rounded-lg hover:border-blue-400 hover:shadow-md transition-all"
                        >
                            <div class="flex items-center gap-2">
                                <Clock class="h-5 w-5 text-yellow-500" />
                                <h3 class="font-medium text-gray-900">Permintaan Peminjaman</h3>
                            </div>
                            <p class="text-sm text-gray-600">Peminjaman yang menunggu persetujuan</p>
                        </Link>
                        
                        <Link 
                            :href="route('loans.active')"
                            class="flex flex-col gap-2 p-4 border rounded-lg hover:border-blue-400 hover:shadow-md transition-all"
                        >
                            <div class="flex items-center gap-2">
                                <BookOpen class="h-5 w-5 text-blue-500" />
                                <h3 class="font-medium text-gray-900">Buku Dipinjam</h3>
                            </div>
                            <p class="text-sm text-gray-600">Buku yang sedang dipinjam</p>
                        </Link>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <Link 
                            :href="route('loans.index', {status: 'terlambat'})"
                            class="flex flex-col gap-2 p-4 border rounded-lg hover:border-blue-400 hover:shadow-md transition-all"
                        >
                            <div class="flex items-center gap-2">
                                <AlertTriangle class="h-5 w-5 text-red-500" />
                                <h3 class="font-medium text-gray-900">Terlambat</h3>
                            </div>
                            <p class="text-sm text-gray-600">Peminjaman yang terlambat dikembalikan</p>
                        </Link>
                        
                        <Link 
                            :href="route('loans.index')"
                            class="flex flex-col gap-2 p-4 border rounded-lg hover:border-blue-400 hover:shadow-md transition-all"
                        >
                            <div class="flex items-center gap-2">
                                <CheckCircle2 class="h-5 w-5 text-green-500" />
                                <h3 class="font-medium text-gray-900">Semua Peminjaman</h3>
                            </div>
                            <p class="text-sm text-gray-600">Lihat semua riwayat peminjaman</p>
                        </Link>
                    </div>
                </div>
                
                <!-- Manajemen Lainnya -->
                <div class="bg-white rounded-xl border border-sidebar-border/70 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-900">Manajemen Lainnya</h2>
                        <LayoutGrid class="h-7 w-7 text-blue-500" />
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <Link 
                            :href="route('books.index')"
                            class="flex flex-col gap-2 p-4 border rounded-lg hover:border-blue-400 hover:shadow-md transition-all"
                        >
                            <div class="flex items-center gap-2">
                                <Book class="h-5 w-5 text-blue-500" />
                                <h3 class="font-medium text-gray-900">Manajemen Buku</h3>
                            </div>
                            <p class="text-sm text-gray-600">Kelola data buku perpustakaan</p>
                        </Link>
                        
                        <Link 
                            :href="route('user-management.index')"
                            class="flex flex-col gap-2 p-4 border rounded-lg hover:border-blue-400 hover:shadow-md transition-all"
                        >
                            <div class="flex items-center gap-2">
                                <Users class="h-5 w-5 text-purple-500" />
                                <h3 class="font-medium text-gray-900">Manajemen Pengguna</h3>
                            </div>
                            <p class="text-sm text-gray-600">Kelola pengguna perpustakaan</p>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
